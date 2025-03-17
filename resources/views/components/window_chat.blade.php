@push('styles')
    <style>
        .header-chat {
            width: 100%;
            z-index: 2;
        }

        .footer-send {
            position: absolute;
            bottom: 0;
            width: 100%;
            z-index: 1;
        }

        .box-chat {
            display: flex;
            flex-direction: column-reverse;
            margin-bottom: 75px;
        }

        #messageInput:focus-visible {
            outline: none !important;
        }

        #previewContainer {
            max-height: 80px; /* Thiết lập chiều cao tối đa */
            overflow-y: auto; /* Thêm cuộn dọc khi nội dung tràn */
        }
    </style>
@endpush


<div class="header-chat bg-white px-2 py-3 border-bottom">

    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex">
            {{-- chỗ này nếu avatar null thì lấy ảnh user ghép lại --}}
            <img class="rounded-circle" width="50" height="50px"
            src="{{ $conversation->is_group ? ($conversation->avatar ? asset(str_replace('public/', 'storage/', $conversation->avatar)) : asset('/assets/images/avatar_default_group.jpg')) : ($conversation->friend->avatar ? asset($conversation->friend->avatar) : asset('/assets/images/avatar_default.jpg')) }}"
            alt="">
            <div class="user-theme ms-3">
                <h5 class="mb-0">
                    @if ($conversation->is_group)
                        {{ $conversation->name }}
                    @else
                        @php
                            $userInfo = $conversation->conversationUserInfo->firstWhere(
                                'user_id',
                                $conversation->friend->id,
                            );
                        @endphp
                        {{ $userInfo->nickname ?? $conversation->friend->name }}
                    @endif
                </h5>
                <p class="text-muted mb-0">
                    {{ $conversation->is_group ? $conversation->conversationUserInfo->count() . ' Thành viên' : '' }}
                </p>
                @if (!$conversation->is_group)
                    <p class="text-muted mb-0">
                        @if ($conversation->friend->isOnline())
                            <span class="badge bg-success">Online</span>
                        @else
                            @if ($conversation->friend->last_seen)
                                <span class="badge bg-secondary">Hoạt động: {{ \Carbon\Carbon::parse($conversation->friend->last_seen)->diffForHumans() }}</span>
                            @else
                                <span class="badge bg-secondary">Chưa bao giờ hoạt động</span>
                            @endif
                        @endif
                    </p>
                @endif
            </div>
        </div>
        <div>
            <!-- Nút mở offcanvas để hiển thị thành viên và chọn thêm -->
            @if ($conversation->is_group)
            <button class="btn btn-primary openAddMembersModal" data-conversation-id="{{ $conversation->id }}" data-is-group="{{ $conversation->is_group }}"><i class="fa-solid fa-user-group"></i></button>
            @endif

            @if ($conversation->is_group)
        <button id="leaveGroupBtn" class="btn btn-danger" onclick="leaveGroup({{ $conversation->id }})">Rời nhóm</button>
@endif
            <!-- Button các chức năng của nhóm -->
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                aria-controls="offcanvasRight">
                <i class="fa-solid fa-layer-group"></i>
            </button>
        </div>
    </div>
    <!-- Offcanvas bên phải -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel">Thông tin</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <!--Gửi file-->
            <div class="group-info mb-4">
                <h6 class="fw-bold">File đã gửi</h6>
                <ul class="list-unstyled">
                    @foreach ($conversation->messages->where('type', 'file')->take(100) as $message)
                        <li><a href="{{ asset($message->message) }}" target="_blank">{{ basename($message->message) }}</a></li>
                        
                    @endforeach
                    
                    <!-- Thêm các file khác -->
                </ul>
            </div>
            <!-- Thông tin nhóm -->
            <div class="group-info mb-4">
                <h6 class="fw-bold">Ảnh đã gửi</h6>
                <div class="images-list d-flex flex-wrap">
                    <!-- Thêm hình ảnh mẫu -->
                    @foreach ($conversation->messages->where('type', 'image')->take(100) as $message)
                        <img src="{{ asset($message->message) }}" alt="Ảnh" class="img-fluid img-thumbnail me-2 mb-2 view-image"
                            style="width: 100px; height: 100px; object-fit: cover;">
                    @endforeach
                </div>
                <!-- Nút "Xem tất cả" -->
                <button class="btn btn-outline-primary mt-2" id="view-toggle-btn">Xem tất cả</button>
            </div>

            <!-- Chức năng nhóm -->
            @if ($conversation->is_group)
                <button class="btn btn-outline-primary w-100" id="view-all-members-btn">Xem tất cả thành
                    viên</button>
            @endif

        </div>
    </div>
    <!-- Offcanvas để hiển thị thành viên -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasMembers"
        aria-labelledby="offcanvasMembersLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasMembersLabel">Danh sách thành viên</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="list-unstyled">
                <li class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/logo/logochat.png') }}" alt="Thành viên 1"
                            class="rounded-circle me-2" style="object-fit:cover; width: 40px; height: 40px;">
                        <span>Thành viên 1</span>
                    </div>
                    <button class="btn btn-danger btn-sm ms-2"><i class="fa-solid fa-user-xmark"></i></button>
                </li>
                <li class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/logo/logochat.png') }}" alt="Thành viên 2"
                            class="rounded-circle me-2" style="object-fit:cover; width: 40px; height: 40px;">
                        <span>Thành viên 2</span>
                    </div>
                    <button class="btn btn-danger btn-sm ms-2"><i class="fa-solid fa-user-xmark"></i></button>
                </li>
                <li class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/logo/logochat.png') }}" alt="Thành viên 3"
                            class="rounded-circle me-2" style="object-fit:cover; width: 40px; height: 40px;">
                        <span>Thành viên 3</span>
                    </div>
                    <button class="btn btn-danger btn-sm ms-2"><i class="fa-solid fa-user-xmark"></i></button>
                </li>
                <li class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/logo/logochat.png') }}" alt="Thành viên 4"
                            class="rounded-circle me-2" style="object-fit:cover; width: 40px; height: 40px;">
                        <span>Thành viên 4</span>
                    </div>
                    <button class="btn btn-danger btn-sm ms-2"><i class="fa-solid fa-user-xmark"></i></button>
                </li>
                <li class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/logo/logochat.png') }}" alt="Thành viên 5"
                            class="rounded-circle me-2" style="object-fit:cover; width: 40px; height: 40px;">
                        <span>Thành viên 5</span>
                    </div>
                    <button class="btn btn-danger btn-sm ms-2"><i class="fa-solid fa-user-xmark"></i></button>
                </li>
            </ul>
        </div>
    </div>


</div>

<!-- Modal Thêm Thành Viên -->
<div class="modal fade" id="addMembersModal" tabindex="-1" aria-labelledby="addMembersModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMembersModalLabel">Chọn thành viên để thêm vào nhóm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Danh sách các thành viên có thể chọn sẽ được tải vào đây -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addSelectedMembers">Thêm</button>
            </div>
        </div>
    </div>
</div>

<div id="chat-box" class="box-chat chat-messages flex-grow-1 p-3 overflow-auto">
    @foreach ($conversation->messages as $message)
        <div class="message d-flex mb-3 {{ $message->sender_id === Auth::id() ? 'justify-content-end' : '' }}">
            @if ($message->sender_id !== Auth::id())
                <img src="{{ $message->sender->avatar ? asset($message->sender->avatar) : asset('/assets/images/avatar_default.jpg') }}"
                    alt="User" class="rounded-circle me-3 avatar" style="object-fit: cover">
            @endif
            <div
                class="message-content {{ $message->sender_id === Auth::id() ? 'bg-primary text-white align-items-end' : 'bg-white' }} p-2 rounded d-flex flex-column">
                @if ($conversation->is_group && $message->sender_id !== Auth::id())
                    <p class="mb-0 text-muted">{{ $message->sender->name }}</p>
                @endif
                @if ($message->type === 'message')
                    <p class="mb-0">{!! nl2br(e($message->message)) !!}</p>
                @elseif ($message->type === 'image')
                    <img src="{{ asset($message->message) }}" alt="Image" class="img-fluid img-send">
                @elseif ($message->type === 'file')
                    <a href="{{ asset($message->message) }}" target="_blank" class="{{ $message->sender_id === Auth::id() ? 'text-white' : 'text-dark'}}">
                        {{ basename($message->message) }}
                    </a>
                @endif
                <span
                    class="message-time small ">{{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</span>
            </div>
            @if ($message->sender_id === Auth::id())
                <img src="{{ $message->sender->avatar ? asset($message->sender->avatar) : asset('/assets/images/avatar_default.jpg') }}"
                    alt="User" class="rounded-circle ms-3" style="object-fit: cover" width="40"
                    height="40">
            @endif
        </div>
    @endforeach
</div>

<!-- Thêm input để gửi tin nhắn -->

<div class="footer-send chat-input d-flex flex-column border-top">
    <form id="send-message-form" action="{{ route('send.message') }}" enctype="multipart/form-data">
        @csrf
        <div class="">
            <div class="d-flex w-100 flex-column ">
                <div class="chatbox1 px-5 d-flex py-2 ">
                    <a class="me-3" title="Chọn ảnh" id="imageIcon"><i
                            class="fa-solid fa-image fa-lg "></i></a>
                    <a title="Chọn file" id="fileIcon"><i class="fa-solid fa-paperclip fa-lg "></i></a>
                </div>

                <div class="chatbox1 d-flex w-100 justify-content-space-evenly border-top border-primary px-4">
                    <textarea class="chatbox2 w-100 border-0" id="messageInput" placeholder="{{ __('messages.enterSendMessages') }}"
                        rows="1" oninput="toggleSendIcon()" style="resize: none; overflow: hidden"></textarea>

                    <button class="border-0 bg-white" type="submit" id="sendIcon" style="display: none;">
                        <i class="buttonsent fa-solid fa-paper-plane" style="font-size: 25px;"></i>
                    </button>
                </div>

                <div id="previewContainer" class="d-flex flex-wrap px-4"></div>
            </div>

            <!-- Các phần tử input file ẩn -->
            <div>
                <input type="hidden" name="conversation_id" id="conversation_id" value="{{ $conversation->id }}">
                <input type="hidden" name="auth_id" id="auth_id" value="{{ Auth::id() }}">
                <input type="file" id="imageInput" style="display: none;" accept="image/*" multiple>
                <input type="file" id="fileInput" style="display: none;" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.zip,.rar" multiple>
            </div>
        </div>
    </form>
</div>


<!-- Modal để hiển thị ảnh lớn -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <img id="modalImage" src="" alt="Ảnh lớn" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Hàm xử lý nút gửi
        function toggleSendIcon() {
            // Hiển thị nút gửi nếu có tin nhắn hoặc ảnh/tệp đính kèm
            if (messageInput.value.trim() !== '' || previewContainer.children.length > 0) {
                sendIcon.style.display = 'block';
            } else {
                sendIcon.style.display = 'none';
            }
        }
    </script>
@endpush
