@extends('layouts.app')

@section('title', 'Feedback')
@section('page_title', 'Feedback')

@section('content')
    <div class="app-page-hero mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
            <div>
                <div class="app-page-hero-kicker mb-2">Workspace</div>
                <h1 class="app-page-hero-title mb-2">Feedback</h1>
                <p class="app-page-hero-subtitle">View feedback sent to you by other users.</p>
            </div>
            <a class="btn btn-light app-page-hero-action" href="{{ route('feedback.create') }}">New Feedback</a>
        </div>
    </div>

    @if($received->count() === 0)
        <div class="card">
            <div class="card-body text-center py-5">
                <h5 class="mb-2">No feedback yet</h5>
                <p class="text-muted mb-0">When someone sends you feedback, it will appear here.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($received as $item)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <div class="fw-semibold">{{ $item->subject ?: 'Feedback' }}</div>
                                        @if($item->feedback_type)
                                            <span class="badge text-bg-light text-dark">{{ $item->feedback_type }}</span>
                                        @endif
                                        @if($item->priority)
                                            <span class="badge text-bg-light text-dark">Priority: {{ $item->priority }}</span>
                                        @endif
                                        <span class="badge {{ ($item->status ?? 'Pending') === 'Resolved' ? 'text-bg-success' : ((($item->status ?? 'Pending') === 'In Progress') ? 'text-bg-info' : 'text-bg-warning') }}">{{ $item->status ?? 'Pending' }}</span>
                                    </div>

                                    <div class="text-muted small mb-2">
                                        Submitted Date: {{ $item->created_at?->format('M d, Y') ?? '-' }}
                                        • Submitted By: {{ $item->is_anonymous ? 'Anonymous' : (optional($item->sender)->name ?? 'Unknown') }}
                                    </div>

                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <div class="small text-muted">Building</div>
                                            <div class="small">{{ $item->buildingDepartment?->name ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="small text-muted">Area</div>
                                            <div class="small">{{ $item->areaDepartment?->name ?? '-' }}</div>
                                        </div>
                                    </div>

                                    <div class="small text-muted mb-1">Description / Message</div>
                                    <div class="p-3 rounded border bg-light" style="white-space: pre-wrap;">{{ $item->message }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $received->links() }}
        </div>
    @endif
@endsection
