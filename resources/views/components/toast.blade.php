<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">{{ __('messages.notification') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{-- {{ $message }} --}}
        </div>
    </div>
</div>