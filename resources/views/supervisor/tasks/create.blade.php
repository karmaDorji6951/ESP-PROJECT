@extends('layouts.app')

@section('page_title', 'Assign New Task')
@section('title', 'Assign New Task')

@section('content')
<div class="card card-soft">
    <div class="card-body">
        <form action="{{ route('supervisor.tasks.store') }}" method="POST" class="row g-3">
            @csrf

            <!-- Employee Selection -->
            <div class="col-12">
                <label for="employee_id" class="form-label">Select Employee *</label>
                <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                    <option value="">-- Choose an employee --</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->first_name }} {{ $employee->last_name }} (ID: {{ $employee->id }})
                    </option>
                    @endforeach
                </select>
                @error('employee_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Task Title -->
            <div class="col-12">
                <label for="title" class="form-label">Task Title *</label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                       placeholder="Enter task title" value="{{ old('title') }}" required>
                @error('title')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Task Description -->
            <div class="col-12">
                <label for="description" class="form-label">Task Description</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                          rows="4" placeholder="Enter task description (optional)">{{ old('description') }}</textarea>
                @error('description')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <label for="status" class="form-label">Status *</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Deadline -->
            <div class="col-md-6">
                <label for="deadline" class="form-label">Deadline</label>
                <input type="date" name="deadline" id="deadline" class="form-control @error('deadline') is-invalid @enderror" 
                       value="{{ old('deadline') }}">
                @error('deadline')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Assign Task</button>
                <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection