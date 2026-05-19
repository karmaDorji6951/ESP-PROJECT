@extends('layouts.app')

@section('title', 'Notifications')
@section('page_title', 'My Notifications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>🔔 My Notifications</h3>
    <form method="POST" action="{{ route('notifications.read-all') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-primary">Mark All as Read</button>
    </form>
</div>

@if ($notifications->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach ($notifications as $notification)
                            <div class="list-group-item list-group-item-action {{ !$notification->read_at ? 'bg-light' : '' }}">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                @if(!$notification->read_at)
                                                    <span class="badge bg-primary rounded-circle p-2">🔔</span>
                                                @else
                                                    <span class="text-muted">📄</span>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">
                                                    <a href="{{ \App\Helpers\NotificationHelper::getNotificationUrlWithMarkAsRead($notification) }}" class="text-decoration-none {{ $notification->read_at ? 'text-muted' : 'text-dark' }}">
                                                        {{ \App\Helpers\NotificationHelper::getNotificationIcon($notification) }} {{ $notification->data['message'] ?? 'New notification' }}
                                                    </a>
                                                    @if(!$notification->read_at)
                                                        <span class="badge bg-primary ms-2">New</span>
                                                    @endif
                                                </div>
                                                
                                                <!-- Task Completion Notifications -->
                                                @if(isset($notification->data['title']))
                                                    <div class="mt-1">
                                                        <small class="text-muted">Task: {{ $notification->data['title'] }}</small>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($notification->data['description']))
                                                    <div class="mt-1">
                                                        <small class="text-muted">{{ Str::limit($notification->data['description'], 100) }}</small>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($notification->data['deadline']))
                                                    <div class="mt-1">
                                                        <small class="text-warning">⏰ Deadline: {{ $notification->data['deadline'] }}</small>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($notification->data['assigned_by']))
                                                    <div class="mt-1">
                                                        <small class="text-info">Assigned by: {{ $notification->data['assigned_by'] }}</small>
                                                    </div>
                                                @endif
                                                
                                                <!-- Leave Request Notifications -->
                                                @if(isset($notification->data['leave_type']))
                                                    <div class="mt-1">
                                                        <small class="text-info">📅 Leave Type: {{ $notification->data['leave_type'] }}</small>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($notification->data['start_date']) && isset($notification->data['end_date']))
                                                    <div class="mt-1">
                                                        <small class="text-muted">📅 Period: {{ $notification->data['start_date'] }} to {{ $notification->data['end_date'] }}</small>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($notification->data['requested_by']))
                                                    <div class="mt-1">
                                                        <small class="text-info">Requested by: {{ $notification->data['requested_by'] }}</small>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($notification->data['status']))
                                                    <div class="mt-1">
                                                        <small class="text-{{ $notification->data['status'] === 'Approved' ? 'success' : ($notification->data['status'] === 'Rejected' ? 'danger' : 'warning') }}">
                                                            Status: {{ $notification->data['status'] }}
                                                        </small>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($notification->data['reviewed_by']))
                                                    <div class="mt-1">
                                                        <small class="text-info">Reviewed by: {{ $notification->data['reviewed_by'] }}</small>
                                                    </div>
                                                @endif
                                                
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="far fa-clock"></i> {{ $notification->created_at->format('M d, Y \a\t h:i A') }}
                                                        ({{ $notification->created_at->diffForHumans() }})
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        @if(!$notification->read_at)
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">Mark as Read</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->links() }}
    </div>
@else
    <div class="text-center py-5">
        <div class="mb-3">
            <span style="font-size: 3rem;">📭</span>
        </div>
        <h4 class="text-muted">No Notifications</h4>
        <p class="text-muted">You don't have any notifications at the moment.</p>
    </div>
@endif

@endsection
