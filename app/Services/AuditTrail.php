<?php

namespace App\Services;

use App\Models\SystemAuditLog;
use App\Models\User;

final class AuditTrail
{
    /**
     * Levels follow the audit log filter in the BAAK view: info, shift, sync, warning, error.
     */
    public function record(
        string $level,
        string $module,
        string $action,
        string $details,
        ?User $actor = null,
        string $status = 'Sukses',
    ): SystemAuditLog {
        return SystemAuditLog::create([
            'timestamp' => now(),
            'level' => $level,
            'actor_id' => $actor?->id,
            'actor_name' => $actor?->name ?? 'Sistem Otomatis',
            'module' => $module,
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
            'status' => $status,
        ]);
    }
}
