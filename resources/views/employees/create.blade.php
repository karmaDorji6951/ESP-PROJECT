@extends('layouts.app')

@section('title', 'Add Employee')
@section('page_title', 'Add Employee')

@section('content')
<div class="card card-soft">
    <div class="card-body">
        <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" class="row g-3">
            @csrf
            @include('employees.form')
            <div class="col-12">
                <button class="btn btn-primary">Save Employee</button>
                <a href="{{ route('employees.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
