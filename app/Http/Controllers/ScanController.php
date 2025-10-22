<?php

namespace App\Http\Controllers;

use App\Models\VisitorAccess;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API endpoint consumed by the QR reader frontend to mark exits.
 */
class ScanController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    /**
     * Handle QR token scanning.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'uuid'],
        ]);

        $access = VisitorAccess::where('qr_token', $request->input('token'))->first();

        if (! $access) {
            return response()->json(['message' => 'Acesso não encontrado.'], 404);
        }

        if (! $access->exit_at) {
            $access->update(['exit_at' => now()]);
            $this->auditLogger->log('access.closed.scan', null, $access);
        }

        return response()->json([
            'message' => 'Saída registrada',
            'access' => $access->fresh('visitor'),
        ]);
    }
}
