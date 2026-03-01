<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogService
{
    /**
     * Write an audit entry for critical actions.
     */
    public function log(
        string $action,
        ?string $entityType = null,
        mixed $entityId = null,
        array $oldValues = [],
        array $newValues = [],
        array $metadata = [],
        ?User $actor = null,
        ?int $tenantId = null,
        ?Request $request = null
    ): AuditLog {
        $actor ??= auth()->user();
        $request ??= request();
        $tenantId ??= $actor?->tenant_id;

        return AuditLog::create([
            'tenant_id' => $tenantId,
            'actor_user_id' => $actor?->id,
            'actor_role' => $actor?->role,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $this->sanitize($oldValues),
            'new_values' => $this->sanitize($newValues),
            'metadata' => $metadata,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    private function sanitize(array $values): array
    {
        $sensitiveKeys = [
            'password',
            'remember_token',
            'token',
            'access_token',
            'firebase_id_token',
        ];

        foreach ($sensitiveKeys as $key) {
            if (array_key_exists($key, $values)) {
                $values[$key] = '[REDACTED]';
            }
        }

        return $values;
    }
}
