<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

/**
 * Thin wrapper around spatie/laravel-activitylog. Centralizing logging
 * calls here (rather than calling the activity() helper directly from
 * scattered controllers/services) means:
 *  1. IP/user agent are attached consistently (see LogActivityMiddleware).
 *  2. The activity log list/filter screen has one query surface to test.
 *  3. If the logging backend ever changes, only this class changes.
 */
class ActivityLogService
{
    public function log(string $logName, string $description, ?object $subject = null, array $properties = []): Activity
    {
        $activity = activity($logName)
            ->withProperties($properties)
            ->when($subject, fn($a) => $a->performedOn($subject));

        if (auth()->check()) {
            $activity->causedBy(auth()->user());
        }

        $logged = $activity->log($description);

        if ($logged && app()->bound('request.ip_address')) {
            $logged->forceFill([
                'ip_address' => app('request.ip_address'),
                'user_agent' => app('request.user_agent'),
            ])->saveQuietly();
        }

        return $logged;
    }

    public function paginateWithFilters(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        return Activity::query()
            ->with(['causer', 'subject'])
            ->when($filters['log_name'] ?? null, fn(Builder $q, $v) => $q->where('log_name', $v))
            ->when($filters['causer_id'] ?? null, fn(Builder $q, $v) => $q->where('causer_id', $v))
            ->when($filters['from'] ?? null, fn(Builder $q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['to'] ?? null, fn(Builder $q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($filters['search'] ?? null, fn(Builder $q, $v) => $q->where('description', 'like', "%{$v}%"))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
