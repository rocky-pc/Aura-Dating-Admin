@extends('admin.layout')

@section('title', 'Reports Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Reports Management</h2>
    <div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between">
                    <span>Pending: {{ $stats['pending'] }}</span>
                    <i class="bi bi-clock"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between">
                    <span>Reviewed: {{ $stats['reviewed'] }}</span>
                    <i class="bi bi-eye"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between">
                    <span>Action Taken: {{ $stats['action_taken'] }}</span>
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-secondary text-white">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between">
                    <span>Dismissed: {{ $stats['dismissed'] }}</span>
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option value="action_taken" {{ request('status') === 'action_taken' ? 'selected' : '' }}>Action Taken</option>
                    <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="reason" class="form-select">
                    <option value="">All Reasons</option>
                    <option value="fake_profile" {{ request('reason') === 'fake_profile' ? 'selected' : '' }}>Fake Profile</option>
                    <option value="inappropriate_content" {{ request('reason') === 'inappropriate_content' ? 'selected' : '' }}>Inappropriate Content</option>
                    <option value="harassment" {{ request('reason') === 'harassment' ? 'selected' : '' }}>Harassment</option>
                    <option value="spam" {{ request('reason') === 'spam' ? 'selected' : '' }}>Spam</option>
                    <option value="underage" {{ request('reason') === 'underage' ? 'selected' : '' }}>Underage</option>
                    <option value="other" {{ request('reason') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Reports Table -->
<div class="card table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Reporter</th>
                        <th>Reported User</th>
                        <th>Reason</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td>{{ $report->id }}</td>
                        <td>
                            @if($report->reporter)
                                <a href="{{ route('admin.users.index', ['search' => $report->reporter->email]) }}">
                                    {{ $report->reporter->email ?? $report->reporter->phone }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($report->reported)
                                <a href="{{ route('admin.users.index', ['search' => $report->reported->email]) }}">
                                    {{ $report->reported->email ?? $report->reported->phone }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $report->reason === 'harassment' ? 'danger' : ($report->reason === 'spam' ? 'warning' : 'info') }}">
                                {{ str_replace('_', ' ', ucfirst($report->reason)) }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#reportModal{{ $report->id }}">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                        <td>
                            <span class="badge badge-{{ $report->status }}">{{ ucfirst($report->status) }}</span>
                        </td>
                        <td>{{ $report->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group">
                                @if($report->status === 'pending')
                                <form method="POST" action="{{ route('admin.reports.resolve', $report->id) }}" class="d-inline">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="action" value="action_taken">
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Take Action">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.reports.resolve', $report->id) }}" class="d-inline">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="action" value="dismissed">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Dismiss">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">No reports found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $reports->links() }}
    </div>
</div>

<!-- Report Detail Modals -->
@foreach($reports as $report)
<div class="modal fade" id="reportModal{{ $report->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Reason:</strong> {{ ucfirst(str_replace('_', ' ', $report->reason)) }}</p>
                <p><strong>Description:</strong></p>
                <p>{{ $report->description ?? 'No description provided' }}</p>
                <hr>
                <p><strong>Reporter:</strong> {{ $report->reporter->email ?? $report->reporter->phone ?? 'Unknown' }}</p>
                <p><strong>Reported User:</strong> {{ $report->reported->email ?? $report->reported->phone ?? 'Unknown' }}</p>
                <hr>
                <p><strong>Status:</strong> {{ ucfirst($report->status) }}</p>
                <p><strong>Created:</strong> {{ $report->created_at }}</p>
                @if($report->reviewed_at)
                <p><strong>Reviewed:</strong> {{ $report->reviewed_at }}</p>
                @endif
                @if($report->admin_notes)
                <p><strong>Admin Notes:</strong> {{ $report->admin_notes }}</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
