@extends('layouts.admin') {{-- Change layout to match your admin template structure --}}

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Admin Notifications</h2>
        <form action="{{ route('admin.notifications.readAll') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">Mark All as Read</button>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse(auth()->user()->notifications as $notification)
                    <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="fw-bold mb-1">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                            <p class="mb-1 text-muted small">{{ $notification->data['message'] ?? '' }}</p>
                            <span class="text-primary" style="font-size: 0.75rem;">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        @if(is_null($notification->read_at))
                            <span class="badge bg-primary rounded-pill">Unread</span>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bell-slash fs-2 d-block mb-2"></i>
                        No notifications found.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection