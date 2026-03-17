<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log an activity
     */
    public static function log(string $action, ?string $model = null, ?int $modelId = null, ?array $details = null, ?string $ipAddress = null): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log CRUD operations
     */
    public static function logCrud(string $action, string $model, $modelInstance = null, ?array $changes = null): void
    {
        $details = [];
        
        if ($modelInstance) {
            $details['data'] = $modelInstance->toArray();
        }
        
        if ($changes) {
            $details['changes'] = $changes;
        }

        self::log(
            action: $action,
            model: $model,
            modelId: $modelInstance?->id,
            details: $details ?: null
        );
    }

    /**
     * Log user authentication events
     */
    public static function logAuth(string $action, ?string $email = null, bool $success = true, ?string $reason = null): void
    {
        $details = [
            'email' => $email ?? Auth::user()?->email,
            'success' => $success,
        ];

        if ($reason) {
            $details['reason'] = $reason;
        }

        self::log(
            action: $action,
            model: 'User',
            details: $details
        );
    }
}
