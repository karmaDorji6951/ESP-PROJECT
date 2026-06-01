@extends('layouts.app')

@section('title', 'Users Management')
@section('page_title', 'Users Management')

@section('content')
<div class="app-page-hero d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <div class="app-page-hero-kicker mb-2">Admin Workspace</div>
        <h1 class="app-page-hero-title mb-2">Users Management</h1>
        <p class="app-page-hero-subtitle">Manage system accounts, roles, and employee links.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-light app-page-hero-action">Create New User</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card card-soft">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Employee ID</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-info">{{ $user->role?->name ?? 'No Role' }}</span>
                        </td>
                        <td><small>{{ $user->employee_id ?? '-' }}</small></td>
                        <td><small>{{ $user->created_at?->format('Y-m-d') ?? '-' }}</small></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <div class="mb-2">👤 No users found</div>
                            <small><a href="{{ route('admin.users.create') }}">Click here to create a new user</a></small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($users->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $users->links() }}
</div>
@endif

@endsection
