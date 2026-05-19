@extends('layouts.app')

@section('title', 'Edit User')
@section('page_title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Edit User: {{ $user->name }}</span>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
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

                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="role_id">Role <span class="text-danger">*</span></label>
                        <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info" role="alert">
                        <small>Leave password fields empty to keep the current password</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="password">New Password (Optional)</label>
                                <input type="password" name="password" id="password" 
                                    class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="password_confirmation">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
