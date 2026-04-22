@extends('layouts.app')

@section('title', 'Assign Timetable Work')
@section('page_title', 'Assign Timetable Work')

@section('content')
<div class="card card-soft">
    <div class="card-body">
        <form method="POST" action="{{ route('tasks.store') }}" class="row g-3">
            @csrf
            @include('tasks.form')
            <div class="col-12">
                <button class="btn btn-primary">Save Assignment</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
