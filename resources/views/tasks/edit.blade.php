@extends('layouts.app')

@section('title', 'Edit Timetable Work')
@section('page_title', 'Edit Timetable Work')

@section('content')
<div class="card card-soft">
    <div class="card-body">
        <form method="POST" action="{{ route('tasks.update', $task) }}" class="row g-3">
            @csrf
            @method('PUT')
            @include('tasks.form')
            <div class="col-12">
                <button class="btn btn-primary">Update Assignment</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
