<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\ActivityLogsExport;
use Maatwebsite\Excel\Facades\Excel;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Admin');
    }

    /**
     * Display a listing of activity logs with filtering
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model
        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in details
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(50)->withQueryString();

        // Get unique values for filter dropdowns
        $users = User::orderBy('name')->get();
        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();
        $models = ActivityLog::distinct()->pluck('model')->filter()->sort()->values();

        return view('activity-logs.index', compact('logs', 'users', 'actions', 'models'));
    }

    /**
     * Export logs to CSV/Excel
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx'); // 'xlsx' or 'csv'

        // Apply same filters as index
        $query = ActivityLog::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $fileName = 'activity_logs_' . date('Y-m-d_His') . '.' . $format;

        return Excel::download(new ActivityLogsExport($query), $fileName);
    }
}
