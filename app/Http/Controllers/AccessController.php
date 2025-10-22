<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccessRequest;
use App\Models\VisitorAccess;
use App\Services\AuditLogger;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Handle visitor access lifecycle from entry to exit.
 */
class AccessController extends Controller
{
    public function __construct(
        private readonly BadgeService $badgeService,
        private readonly AuditLogger $auditLogger,
    ) {
        $this->middleware(['permission:access.manage']);
    }

    /**
     * Register a new visitor access and generate the badge.
     */
    public function store(StoreAccessRequest $request): JsonResponse
    {
        $access = DB::transaction(function () use ($request) {
            $access = VisitorAccess::create([
                'visitor_id' => $request->integer('visitor_id'),
                'department_id' => $request->integer('department_id'),
                'user_id' => $request->user()->id,
                'entry_at' => now(),
                'purpose' => $request->input('purpose'),
                'qr_token' => Str::uuid()->toString(),
            ]);

            $access->badge_path = $this->badgeService->render($access->load('visitor', 'department'));
            $access->save();

            return $access;
        });

        Log::notice('Access registered', ['access_id' => $access->id]);
        $this->auditLogger->log('access.created', $request->user(), $access);

        $access->setAttribute('badge_url', Storage::disk('public')->url($access->badge_path));

        return response()->json($access, 201);
    }

    /**
     * Mark exit for a given access.
     */
    public function close(VisitorAccess $access): JsonResponse
    {
        if ($access->exit_at) {
            return response()->json(['message' => 'Saída já registrada.'], 400);
        }

        $access->update(['exit_at' => now()]);
        $this->auditLogger->log('access.closed', request()->user(), $access);

        return response()->json(['message' => 'Saída registrada.']);
    }

    /**
     * List accesses with filtering.
     */
    public function index(): JsonResponse
    {
        $accesses = VisitorAccess::with('visitor', 'department', 'user')->latest('entry_at')->paginate();

        return response()->json($accesses);
    }
}
