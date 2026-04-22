@extends('layouts.app')

@section('title', 'Employees')
@section('page_title', 'Employees')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('employees.create') }}" class="btn btn-primary">Add Employee</a>
</div>

<div class="card card-soft">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Name</th><th>CID</th><th>Phone</th><th>Role</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->cid }}</td>
                        <td>{{ $employee->phone }}</td>
                        <td>{{ $employee->role_title }}</td>
                        <td><span class="badge bg-{{ $employee->status === 'Active' ? 'success' : 'secondary' }}">{{ $employee->status }}</span></td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="{{ route('employees.destroy', $employee) }}" onsubmit="return confirm('Delete this employee?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted p-4">No employees found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $employees->links() }}</div>
@endsection
