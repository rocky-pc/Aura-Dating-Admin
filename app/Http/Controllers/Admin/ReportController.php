<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * List all reports with filters
     */
    public function index(Request $request)
    {
        $query = UserReport::with(['reporter', 'reported', 'resolver'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('reason')) {
            $query->where('reason', $request->reason);
        }

        $reports = $query->paginate($request->get('per_page', 20));

        return response()->json($reports);
    }

    /**
     * Web - List all reports with filters
     */
    public function webIndex(Request $request)
    {
        $query = UserReport::with(['reporter', 'reported'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('reason') && $request->reason) {
            $query->where('reason', $request->reason);
        }

        $reports = $query->paginate(20);

        $stats = [
            'pending' => UserReport::where('status', 'pending')->count(),
            'reviewed' => UserReport::where('status', 'reviewed')->count(),
            'action_taken' => UserReport::where('status', 'action_taken')->count(),
            'dismissed' => UserReport::where('status', 'dismissed')->count(),
        ];

        return view('admin.reports.index', compact('reports', 'stats'));
    }

    /**
     * Get single report details
     */
    public function show(UserReport $report)
    {
        $report->load(['reporter', 'reported', 'resolver']);

        return response()->json($report);
    }

    /**
     * Resolve a report
     */
    public function resolve(Request $request, UserReport $report)
    {
        $request->validate([
            'status' => 'required|in:resolved,dismissed,escalated',
            'resolution_notes' => 'sometimes|string|max:1000',
        ]);

        $report->update([
            'status' => $request->status,
            'resolution_notes' => $request->resolution_notes,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
        ]);

        // If report is sustained, take action on the reported user
        if ($request->status === 'resolved' && $request->action_on_user) {
            $reportedUser = $report->reported;
            
            if (isset($request->action_on_user['ban'])) {
                $reportedUser->update(['is_active' => false]);
            }
            
            if (isset($request->action_on_user['warn'])) {
                // Create a warning notification (would need notification logic)
            }
        }

        return response()->json([
            'message' => 'Report resolved successfully',
            'report' => $report->fresh(['reporter', 'reported', 'resolver']),
        ]);
    }

    /**
     * Web - Resolve a report
     */
    public function webResolve(Request $request, UserReport $report)
    {
        $action = $request->input('action', 'dismissed');
        
        $status = $action === 'action_taken' ? 'action_taken' : 'dismissed';

        $report->update([
            'status' => $status,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Report resolved successfully');
    }

    /**
     * Get report statistics
     */
    public function stats()
    {
        $stats = [
            'total' => UserReport::count(),
            'pending' => UserReport::where('status', 'pending')->count(),
            'resolved' => UserReport::where('status', 'resolved')->count(),
            'dismissed' => UserReport::where('status', 'dismissed')->count(),
            'escalated' => UserReport::where('status', 'escalated')->count(),
            'by_reason' => UserReport::select('reason', DB::raw('count(*) as count'))
                ->groupBy('reason')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get report reasons
     */
    public function reasons()
    {
        return response()->json([
            'reasons' => [
                'fake_profile' => 'Fake Profile',
                'inappropriate_behavior' => 'Inappropriate Behavior',
                'harassment' => 'Harassment',
                'spam' => 'Spam',
                'scam' => 'Scam',
                'other' => 'Other',
            ],
        ]);
    }
}
