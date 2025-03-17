
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
<script>
    $(document).ready(function() {
        @if (session('success'))
            showToast('{{ session('success') }}', 'success');
        @elseif (session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
    });
</script>
@vite('resources/js/app.js')
@include('components.js.action_message')
@stack('scripts')
</body>
</html>
