@extends('admin.layout')

@section('title', 'Wallets')

@section('content')
<div class="page-header mb-3">
    <h4>Wallets Management</h4>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Balance</th>
                    <th>Bonus Points</th>
                    <th>Total Points</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wallets as $wallet)
                <tr>
                    <td>{{ $wallet->user->profile->first_name ?? 'N/A' }} {{ $wallet->user->profile->last_name ?? '' }}</td>
                    <td>{{ $wallet->user->email }}</td>
                    <td>{{ $wallet->balance }}</td>
                    <td>{{ $wallet->bonus_points }}</td>
                    <td>{{ $wallet->balance + $wallet->bonus_points }}</td>
                    <td>
                        <form action="{{ route('admin.wallets.add-points', $wallet) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="number" name="points" value="100" style="width:70px" min="1">
                            <select name="type" style="width:80px">
                                <option value="balance">Balance</option>
                                <option value="bonus">Bonus</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-success">Add</button>
                        </form>
                        <form action="{{ route('admin.wallets.reset', $wallet) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset this wallet?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Reset</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $wallets->links() }}
</div>

@endsection