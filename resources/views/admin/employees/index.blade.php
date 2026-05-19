@extends('layouts.app')

@section('title', 'Employees Management')
@section('page_title', 'Employees Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Employees</h5>
            <a href="{{ route('admin.employees.create') }}" class="btn btn-success btn-sm">Add New Employee</a>
        </div>
    </div>
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
                    <th>CID</th>
                    <th>Role Title</th>
                    <th>Phone</th>
                    <th>Joining Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td class="fw-semibold">{{ $employee->name }}</td>
                        <td><small>{{ $employee->cid }}</small></td>
                        <td>{{ $employee->role_title }}</td>
                        <td>{{ $employee->phone ?? '-' }}</td>
                        <td><small>{{ $employee->joining_date?->format('Y-m-d') ?? '-' }}</small></td>
                        <td>
                            <span class="badge {{ $employee->status === 'Active' ? 'bg-success' : 'bg-danger' }}">
                                {{ $employee->status }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <div class="mb-2">👥 No employees found</div>
                            <small><a href="{{ route('admin.employees.create') }}">Click here to add a new employee</a></small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($employees->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $employees->links() }}
</div>
@endif

@endsection
