@extends('layouts.app')

@section('title', 'Edit Employee')
@section('page_title', 'Edit Employee')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Edit Employee: {{ $employee->name }}</span>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>

            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.employees.update', $employee) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name', $employee->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="cid">CID (Citizenship ID) <span class="text-danger">*</span></label>
                        <input type="text" name="cid" id="cid" 
                            class="form-control @error('cid') is-invalid @enderror" 
                            value="{{ old('cid', $employee->cid) }}" required>
                        @error('cid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="phone">Phone Number</label>
                                <input type="tel" name="phone" id="phone" 
                                    class="form-control @error('phone') is-invalid @enderror" 
                                    value="{{ old('phone', $employee->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="role_title">Role Title <span class="text-danger">*</span></label>
                                <input type="text" name="role_title" id="role_title" 
                                    class="form-control @error('role_title') is-invalid @enderror" 
                                    value="{{ old('role_title', $employee->role_title) }}" required>
                                @error('role_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="address">Address (Dzongkhag)</label>
                        <select name="address" id="address"
                            class="form-control @error('address') is-invalid @enderror">
                            <option value="">-- Select Dzongkhag --</option>
                            @foreach(($dzongkhags ?? []) as $dzongkhag)
                                <option value="{{ $dzongkhag->name }}" {{ old('address', $employee->address) === $dzongkhag->name ? 'selected' : '' }}>
                                    {{ $dzongkhag->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="joining_date">Joining Date <span class="text-danger">*</span></label>
                                <input type="date" name="joining_date" id="joining_date" 
                                    class="form-control @error('joining_date') is-invalid @enderror" 
                                    value="{{ old('joining_date', $employee->joining_date?->format('Y-m-d')) }}" required>
                                @error('joining_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="">-- Select Status --</option>
                                    <option value="Active" {{ old('status', $employee->status) === 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status', $employee->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Employee</button>
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
