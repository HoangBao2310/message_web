<script>
    
    $(document).ready(function() {

       

        function appendMessage(message, isSender) {
            const avatarUrl = message.sender.avatar_url;
            let messageContent = '';

            if (message.type === 'message') {
                messageContent = `<p class="mb-0">${message.message}</p>`;
            } else if (message.type === 'image') {
                messageContent = `<img src="${message.message}" alt="Image" class="img-fluid img-send">`;
            } else if (message.type === 'file') {
                messageContent = `<a href="${message.message}" target="_blank" class="${isSender ? 'text-white' : 'text-dark'}">${message.message.split('/').pop()}</a>`;
            }

            const messageHtml = `
                <div class="message d-flex mb-3 ${isSender ? 'justify-content-end' : ''}">
                    ${!isSender ? `
                        <img src="${avatarUrl}" alt="User" class="rounded-circle me-3 avatar" style="object-fit: cover" width="40" height="40">
                    ` : ''}
                    <div class="message-content ${isSender ? 'bg-primary text-white align-items-end' : 'bg-white'} p-2 rounded d-flex flex-column">
                        ${message.conversation.is_group && !isSender ? `<p class="mb-0 text-muted">${message.sender.name}</p>` : ''}
                        ${messageContent}
                        <span class="message-time small ">${message.time_diff}</span>
                    </div>
                    ${isSender ? `
                        <img src="${avatarUrl}" alt="User" class="rounded-circle ms-3 avatar" style="object-fit: cover" width="40" height="40">
                    ` : ''}
                </div>
            `;
            $('.box-chat').prepend(messageHtml);
            var chatBox = document.querySelector('.box-chat');
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function initializeEcho() {
            const conversationId = $('#conversation_id').val();
            Echo.private('chat.' + conversationId)
                .listen('MessageSent', (e) => {
                    // Check if the user is still part of the group
                    $.ajax({
                        url: '/check-membership',
                        method: 'POST',
                        data: {
                            conversation_id: conversationId,
                            user_id: {{ Auth::id() }},
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.is_member) {
                                appendMessage(e.message, parseInt(e.message.sender_id) === parseInt({{ Auth::id() }}));
                            }
                        },
                        error: function(xhr) {
                            showToast(xhr.responseJSON.message, 'error');
                        }
                    });
                });
        }

        function initializeMessageForm() {

            const imageFiles = $('#imageInput');
            const fileFiles = $('#fileInput');
            const sendMessageForm = $('#send-message-form');

            $(document).on('submit', '#send-message-form', function(e) {
                e.preventDefault();
                let message = $('#messageInput').val();
                let imageFiles = imageInput.files;
                let fileFiles = fileInput.files;

                // Check if there is any data to send
                if (message.trim() === '' && imageFiles.length === 0 && fileFiles.length === 0) {
                    return; // Exit if there's no data
                }

                $('#sendIcon').css('display', 'none');
                $('#messageInput').val('');
                $('#previewContainer').empty();

                let formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val());
                formData.append('conversation_id', $('#conversation_id').val());
                formData.append('message', message);

                for (let i = 0; i < imageFiles.length; i++) {
                    formData.append('images[]', imageFiles[i]);
                }

                for (let i = 0; i < fileFiles.length; i++) {
                    formData.append('files[]', fileFiles[i]);
                }

                $.ajax({
                    url: '{{ route('send.message') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {

                
                        $('#message').val('');
                        imageInput.value = '';
                        fileInput.value = '';


                        if (response.status === 'success') {
                            response.messages.forEach(message => {
                                appendMessage(message, parseInt(message.sender_id) === parseInt({{ Auth::id() }}));
                            });
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation error
                            let errors = xhr.responseJSON.errors;
                            let errorMessage = '';
                            for (let key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    errorMessage += errors[key][0] + '\n';
                                }
                            }
                            showToast(errorMessage, 'error');
                        } else {
                            showToast(xhr.responseJSON.message, 'error');
                        }
                    }
                });
            });
        }

        // input gửi tin nhắn
        function initializeEventListeners() {
           
            $(document).on('click', '#imageIcon', function() {
                $('#imageInput').click();
            });

            $(document).on('click', '#fileIcon', function() {
                $('#fileInput').click();
            });

            $(document).on('change', '#imageInput', function() {
                handleFileSelect(this.files, 'image');
            });

            $(document).on('change', '#fileInput', function() {
                handleFileSelect(this.files, 'file');
            });

            $(document).on('input', '#messageInput', function() {
                toggleSendIcon();
            });

            function handleFileSelect(files, type) {
                const $previewContainer = $('#previewContainer');

                $previewContainer.empty();

                $.each(files, function(i, file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const $previewElement = $('<div></div>')
                            .addClass('preview-element m-2')
                            .css({
                                width: '70px',
                                height: '70px',
                                border: '1px solid #ccc',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                overflow: 'hidden'
                            });

                        if (type === 'image') {
                            const $img = $('<img>')
                                .attr('src', e.target.result)
                                .css({
                                    width: '100%',
                                    height: '100%',
                                    objectFit: 'scale-down'
                                });
                            $previewElement.append($img);
                        } else {
                            const $fileName = $('<span></span>')
                                .text(file.name)
                                .css({
                                    fontSize: '12px',
                                    textAlign: 'center'
                                });
                            $previewElement.append($fileName);
                        }

                        $previewContainer.append($previewElement);
                        toggleSendIcon();
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        // Hàm xử lý nút gửi
        function toggleSendIcon() {
            // Hiển thị nút gửi nếu có tin nhắn hoặc ảnh/tệp đính kèm
            if (messageInput.value.trim() !== '' || previewContainer.children.length > 0) {
                sendIcon.style.display = 'block';
            } else {
                sendIcon.style.display = 'none';
            }
        }

        initializeEcho();
        initializeMessageForm();
        initializeEventListeners();
    });
</script>