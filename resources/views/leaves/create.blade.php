@extends('layouts.app')

@section('title', 'Apply Leave')
@section('page_title', 'Apply Leave')

@section('content')
<div class="card card-soft">
    <div class="card-body">
        <form method="POST" action="{{ route('leaves.store') }}" class="row g-3">
            @csrf
            @include('leaves.form')
            <div class="col-12">
                <button class="btn btn-primary">Submit Leave Request</button>
                <a href="{{ route('leaves.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
