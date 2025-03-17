@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let originalTitle = document.title;
            let blinkInterval;

            function startTitleBlink() {
                if (!blinkInterval) {
                    blinkInterval = setInterval(() => {
                        document.title = document.title === 'Có tin nhắn mới!' ? originalTitle : 'Có tin nhắn mới!';
                    }, 1000);
                }
            }

            function stopTitleBlink() {
                clearInterval(blinkInterval);
                document.title = originalTitle;
                blinkInterval = null;
            }

            // Dừng nhấp nháy tiêu đề khi người dùng quay lại tab
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    stopTitleBlink();
                }
            });

            @foreach($IsConversations as $conversation)
                Echo.private('notifications.{{ $conversation->id }}')
                    .listen('NotificationSentMessage', (e) => {
                        // Check if the user is still part of the group
                        $.ajax({
                            url: '/check-membership',
                            method: 'POST',
                            data: {
                                conversation_id: {{ $conversation->id }},
                                user_id: {{ Auth::id() }},
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.is_member) {
                                    startTitleBlink();
                                    showNotification(e.notification);
                                }
                            }
                        });
                    });
            @endforeach

            showNotification = (notification) => {
                $('#liveNotification .conversation-notification').attr('data-id', notification.id);
                $('#liveNotification .img-avatar').attr('src', notification.is_group == false ? (notification.friend.avatar ? notification.friend.avatar : '/assets/images/avatar_default.jpg') : (notification.avatar ? notification.avatar : '/assets/images/avatar_default_group.jpg'));
                $('#liveNotification .chat-info h5').text(notification.is_group == false ? notification.friend.name : notification.name);
                
                if (notification.latestMessage) {
                    let senderName = notification.latestMessage.sender_id == {{ Auth::id() }} ? 'Bạn' : notification.latestMessage.sender.name; 
                    if (notification.latestMessage.type == 'image') {
                        $('#liveNotification .text-message').text(senderName + ' đã gửi hình');
                    } else if (notification.latestMessage.type == 'file') {
                        $('#liveNotification .text-message').text(senderName + ' đã gửi file');
                    } else {
                        $('#liveNotification .text-message').text(notification.latestMessage.message);
                    }
                } else {
                    let creator = notification.conversationUsers.find(user => user.user_id == notification.created_by);
                    if (notification.created_by == {{ Auth::id() }}) {
                        $('#liveNotification .text-message').text('Bạn đã tạo nhóm');
                    } else {
                        $('#liveNotification .text-message').text(creator.nickname ? creator.nickname : creator.user.name + ' đã tạo nhóm');
                    }
                }

                let toast = new bootstrap.Toast(liveNotification);
                toast.show();
            }
        });
    </script>
@endpush

<div class="toast-container position-fixed bottom-0 end-0 p-3" role="alert" aria-live="assertive" aria-atomic="true"
    style="z-index: 9999">
    <div id="liveNotification" class="toast align-items-center bg-white" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <a class="text-decoration-none d-flex justify-content-between conversation-link p-2 conversation-notification"
                data-id="{{--{{ id conversation }}--}}">
                <div class="d-flex align-items-center">
                    <img src="{{--{{ avatar }}--}}"
                        alt="User" class="rounded-circle me-3 img-avatar" style="object-fit: cover" width="50"
                        height="50">
                    <div class="chat-info">
                         <h5 class="mb-0 "> {{--{{ name }}--}}</h5> 
                        <p class="text-muted mb-0 text-message">
                            {{--{{ message }}--}}
                        </p>
                    </div>
                </div>
            </a>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>