@extends('admin.layout')

@section('title', 'Subscriptions Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Subscriptions Management</h2>
    <div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between">
                    <span>Total: {{ $stats['total'] }}</span>
                    <i class="bi bi-credit-card"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between">
                    <span>Active: {{ $stats['active'] }}</span>
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between">
                    <span>Gold: {{ $stats['gold'] }}</span>
                    <i class="bi bi-star"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="background: linear-gradient(45deg, #6f42c1, #a855f7); color: white;">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between">
                    <span>Platinum: {{ $stats['platinum'] }}</span>
                    <i class="bi bi-gem"></i>
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
                <select name="plan" class="form-select">
                    <option value="">All Plans</option>
                    <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>Free</option>
                    <option value="gold" {{ request('plan') === 'gold' ? 'selected' : '' }}>Gold</option>
                    <option value="platinum" {{ request('plan') === 'platinum' ? 'selected' : '' }}>Platinum</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Subscriptions Table -->
<div class="card table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Stripe ID</th>
                        <th>Started</th>
                        <th>Expires</th>
                        <th>Auto Renew</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->id }}</td>
                        <td>
                            @if($subscription->user)
                                <a href="{{ route('admin.users.index', ['search' => $subscription->user->email]) }}">
                                    {{ $subscription->user->email ?? $subscription->user->phone }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $subscription->plan === 'platinum' ? 'purple' : ($subscription->plan === 'gold' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($subscription->plan) }}
                            </span>
                        </td>
                        <td>
                            @if($subscription->stripe_subscription_id)
                                <small><code>{{ $subscription->stripe_subscription_id }}</code></small>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $subscription->started_at->format('M d, Y') }}</td>
                        <td>
                            @if($subscription->expires_at)
                                {{ $subscription->expires_at->format('M d, Y') }}
                                @if($subscription->expires_at->isPast())
                                    <span class="badge bg-danger">Expired</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($subscription->auto_renew)
                                <span class="badge bg-success"><i class="bi bi-check"></i></span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-x"></i></span>
                            @endif
                        </td>
                        <td>
                            @if($subscription->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#subscriptionModal{{ $subscription->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if($subscription->is_active && $subscription->plan !== 'free')
                                <form method="POST" action="{{ route('admin.subscriptions.cancel', $subscription->id) }}" class="d-inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel Subscription">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">No subscriptions found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $subscriptions->links() }}
    </div>
</div>

<!-- Subscription Detail Modals -->
@foreach($subscriptions as $subscription)
<div class="modal fade" id="subscriptionModal{{ $subscription->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subscription Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> {{ $subscription->id }}</p>
                        <p><strong>User:</strong> {{ $subscription->user->email ?? $subscription->user->phone ?? 'Unknown' }}</p>
                        <p><strong>Plan:</strong> {{ ucfirst($subscription->plan) }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> {{ $subscription->is_active ? 'Active' : 'Inactive' }}</p>
                        <p><strong>Auto Renew:</strong> {{ $subscription->auto_renew ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
                <hr>
                <p><strong>Stripe Subscription ID:</strong> {{ $subscription->stripe_subscription_id ?? 'N/A' }}</p>
                <p><strong>Stripe Customer ID:</strong> {{ $subscription->stripe_customer_id ?? 'N/A' }}</p>
                <hr>
                <p><strong>Started:</strong> {{ $subscription->started_at }}</p>
                <p><strong>Expires:</strong> {{ $subscription->expires_at }}</p>
                <p><strong>Created:</strong> {{ $subscription->created_at }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
