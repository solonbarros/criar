<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitorRequest;
use App\Http\Requests\UpdateVisitorRequest;
use App\Models\Visitor;
use App\Services\AuditLogger;
use App\Services\VisitorPhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Manage visitor lifecycle, including photo capture and anonymization.
 */
class VisitorController extends Controller
{
    public function __construct(
        private readonly VisitorPhotoService $photoService,
        private readonly AuditLogger $auditLogger,
    ) {
        $this->middleware(['permission:visitors.manage'])->except(['index', 'show']);
    }

    /**
     * List visitors with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $visitors = Visitor::query()
            ->when($request->get('search'), function ($query, $search) {
                $query->where('full_name', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate();

        return response()->json($visitors);
    }

    /**
     * Create a visitor along with the captured webcam picture.
     */
    public function store(StoreVisitorRequest $request): JsonResponse
    {
        $photo = $this->photoService->storeFromBase64($request->input('photo_base64'));

        $visitor = Visitor::create([
            'full_name' => $request->input('full_name'),
            'document_number' => $request->input('document_number'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'consent_at' => $request->boolean('consent') ? now() : null,
            'photo_path' => $photo['path'],
            'photo_hash' => $photo['hash'],
        ]);

        $this->auditLogger->log('visitor.created', $request->user(), $visitor);
        Log::info('Visitor created', ['visitor_id' => $visitor->id, 'user_id' => $request->user()->id]);

        return response()->json($visitor, 201);
    }

    /**
     * Show a single visitor record.
     */
    public function show(Visitor $visitor): JsonResponse
    {
        return response()->json($visitor);
    }

    /**
     * Update visitor metadata.
     */
    public function update(UpdateVisitorRequest $request, Visitor $visitor): JsonResponse
    {
        $oldValues = $visitor->getOriginal();

        $visitor->fill($request->safe()->except(['photo_base64']));

        if ($request->filled('photo_base64')) {
            $photo = $this->photoService->storeFromBase64($request->input('photo_base64'));
            $visitor->photo_path = $photo['path'];
            $visitor->photo_hash = $photo['hash'];
        }

        $visitor->save();
        $this->auditLogger->log('visitor.updated', $request->user(), $visitor, [
            'old' => $oldValues,
            'new' => $visitor->getAttributes(),
        ]);

        return response()->json($visitor);
    }

    /**
     * Anonymize a visitor record when required by LGPD rules.
     */
    public function anonymize(Visitor $visitor): JsonResponse
    {
        $visitor->update([
            'full_name' => 'Anonimizado',
            'document_number' => null,
            'email' => null,
            'phone' => null,
            'photo_path' => null,
            'photo_hash' => null,
            'anonymized_at' => now(),
        ]);

        $this->auditLogger->log('visitor.anonymized', request()->user(), $visitor);

        return response()->json(['message' => 'Visitante anonimizado.']);
    }
}
