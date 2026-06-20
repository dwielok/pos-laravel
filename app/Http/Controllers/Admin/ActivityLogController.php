<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
    ) {
        // $this->middleware('can:activity-logs.view');
    }

    public function index(): View
    {
        $filters = request()->only(['log_name', 'causer_id', 'from', 'to', 'search']);

        $activities = $this->activityLogService->paginateWithFilters($filters, 25);
        $users = User::orderBy('name')->get(['id', 'name']);
        $logNames = \Spatie\Activitylog\Models\Activity::query()->distinct()->pluck('log_name')->filter();

        return view('admin.activity-log.index', compact('activities', 'users', 'logNames', 'filters'));
    }
}
