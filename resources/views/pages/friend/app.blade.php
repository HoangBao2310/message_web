@extends('layouts.app')
@section('title', 'Message Mini Web')
@push('styles')
@endpush

@push('scripts')
    <script>
        showSavedToast();
    </script>
@endpush

@section('content')
    @include('components.toast')
@endsection

@section('content-1')

<div class="listfr-item rounded" style="padding: 10px; margin-bottom: 10px; background-color: #f8f9fa;">
    <div class="d-flex align-items-center">
        <i class="fa-solid fa-user-group me-2" style="font-size: 24px; color: #007bff;"></i>
        <div class="listfr-info">
            <a href="{{ route('friends.list') }}" class="mb-0" style="text-decoration: none; color: #007bff; font-weight: bold;">{{ __('messages.friendList') }}</a>
        </div>
    </div>
</div>
<div class="listfr-item rounded" id="friendRequests" style="padding: 10px; margin-bottom: 10px; background-color: #f8f9fa;">
    <div class="d-flex align-items-center">
        <i class="fa-solid fa-user-plus me-2" id="showFriendRequestsModal" style="cursor: pointer; font-size: 24px; color: #28a745;"></i>
        <div class="listfr-info">
            <a href="{{ route('friend.requests') }}" class="mb-0" style="text-decoration: none; color: #28a745; font-weight: bold;">{{ __('messages.friendInvitation') }}</a>

        </div>
    </div>
</div>
@endsection

@section('content-2')
    
    @yield('content-2-1')
    
@endsection
