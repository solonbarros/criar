<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Request;

/**
 * Centralizes creation of audit log entries across the system.
 */
class AuditLogger
{
    /**
     * Persist an audit record with contextual metadata.
     */
    public function log(string $action, ?Authenticatable $user, ?object $model, array $changes = []): void
    {
        AuditLog::create([
            'user_id' => $user?->getAuthIdentifier(),
            'action' => $action,
            'auditable_type' => $model ? $model::class : null,
            'auditable_id' => $model->id ?? null,
            'old_values' => $changes['old'] ?? null,
            'new_values' => $changes['new'] ?? null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
