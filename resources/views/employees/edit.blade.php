@extends('layouts.app')

@section('title', 'Edit Employee')
@section('page_title', 'Edit Employee')

@section('content')
<div class="card card-soft">
    <div class="card-body">
        <form method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data" class="row g-3">
            @csrf
            @method('PUT')
            @include('employees.form')
            <div class="col-12">
                <button class="btn btn-primary">Update Employee</button>
                <a href="{{ route('employees.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
