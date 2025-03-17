<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Mini Chat Web</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/logochat.png') }}" type="image/x-icon">
    @stack('meta')

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->

    {{-- styles --}}
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">

    @stack('styles-main')

    {{-- end styles --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::id() }}">
</head>

<body style="padding-top:0">

    <div class="">
        @yield('content-main')
    </div>

    <div id="notification-container" style="position: fixed; bottom: 10px; right: 10px; z-index: 9999;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="{{ mix('js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userId = document.querySelector('meta[name="user-id"]').getAttribute('content');

            window.Echo.private(`friends.${userId}`)
                .listen('FriendOnline', (e) => {
                    showNotification(`${e.user.name} is now online!`);
                });
        });

        function showNotification(message) {
            const notificationContainer = document.getElementById('notification-container');
            const notification = document.createElement('div');
            notification.className = 'alert alert-info';
            notification.innerText = message;
            notificationContainer.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
    @stack('scripts-main')
</body>
</html>