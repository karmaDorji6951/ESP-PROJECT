@extends('layouts.app')

@section('title', 'Create User')
@section('page_title', 'Create New User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Create New User</span>
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

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="role_id">Role <span class="text-danger">*</span></label>
                        <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Create User</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
