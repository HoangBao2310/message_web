@extends('layouts.app')
@section('title', 'Message Mini Web')
@section('description', 'Săn sale hoàn xu với % khủng cùng Hoàn Xu')
@section('keyword', 'sale, hoàn xu, giảm giá, khuyến mãi, lazada, shopee, tiktok')
@push('styles')

@endpush

@section('content')
@include('components.toast')
@endsection

@section('content-1')
<div class="chat-list-container px-3">
    @foreach ($conversations as $item)
    <a id="conversation-{{ $item->id }}"
        class="text-decoration-none d-flex justify-content-between conversation-link mb-4" data-id="{{ $item->id }}">
        <div class="d-flex align-items-center">
            <img src="{{ $item->is_group ? ($item->avatar ? asset(str_replace('public/', 'storage/', $item->avatar)) : asset('/assets/images/avatar_default_group.jpg')) : ($item->friend->avatar ? asset($item->friend->avatar) : asset('/assets/images/avatar_default.jpg')) }}"
                alt="User" class="rounded-circle me-3" style="object-fit: cover" width="50" height="50">
            <div class="chat-info">
                <h5 class="mb-0">{{ $item->is_group == false ? $item->friend->name : $item->name }}</h5>
                <p class=" mb-0">
                    @if ($item->latestMessage)
                    @php
                    $senderName =
                    $item->latestMessage->sender_id == Auth::id()
                    ? 'Bạn'
                    : $item->latestMessage->sender->name;
                    @endphp
                    @if ($item->latestMessage->type == 'image')
                    {{ $senderName }} đã gửi hình
                    @elseif ($item->latestMessage->type == 'file')
                    {{ $senderName }} đã gửi file
                    @else
                    {{ $item->latestMessage->message }}
                    @endif
                    @else
                    @php
                    $creator = $item->conversationUsers->firstWhere('user_id', $item->created_by);
                    @endphp
                     @if ($item->created_by == Auth::id())
                     Bạn đã tạo nhóm
                 @elseif ($creator)
                     {{ $creator->nickname ?? $creator->user->name }} đã tạo nhóm
                 @else
                     Người tạo nhóm đã rời đi
                 @endif
             @endif
                </p>
            </div>
        </div>
        <span class="chat-time small">
            @if (isset($item->latestMessage->time_diff))
            {{ $item->latestMessage->time_diff }}
            @else
            Tạo {{ $item->time_diff }}
            @endif
        </span>
    </a>
    @endforeach
</div>

<!-- Modal xác nhận xóa thành viên -->
<div class="modal fade" id="confirmRemoveMemberModal" tabindex="-1" aria-labelledby="confirmRemoveMemberModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmRemoveMemberModalLabel">Xác nhận xóa thành viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa thành viên này khỏi nhóm không?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmRemoveMemberBtn">Xóa</button>
            </div>
        </div>
    </div>
</div>
<!-- Offcanvas để hiển thị thành viên -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasMembers" aria-labelledby="offcanvasMembersLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasMembersLabel">Danh sách thành viên</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="list-unstyled">
            <!-- Danh sách thành viên sẽ được tải vào đây -->
        </ul>
        <button id="load-more-members-btn" class="btn btn-primary w-100 mt-3" style="display: none;">Xem thêm</button>
    </div>
</div>
@endsection

@section('content-2')
<div class="window-chat">
    @if (isset($latestConversation))
    @include('components.window_chat', ['conversation' => $latestConversation])
    @endif
</div>
@endsection

@push('scripts')
{{-- load conversation đã chọn --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

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
                                updateConversationList(e.notification);
                                console.log('Có tin nhắn mới từ cuộc trò chuyện ' + e.notification.id);
                            }
                        }
                    });
                });
        @endforeach

            function updateConversationList(notification) {
                const conversationId = notification.id;
                const conversationElement = $(`#conversation-${conversationId}`);

                // Cập nhật nội dung cuộc trò chuyện
                const avatarUrl = notification.is_group == false ? (notification.friend.avatar ? notification.friend.avatar : '/assets/images/avatar_default.jpg') : (notification.avatar ? notification.avatar : '/assets/images/avatar_default_group.jpg');
                const name = notification.is_group == false ? notification.friend.name : notification.name;
                let message = '';
                if (notification.latestMessage) {
                    let senderName = notification.latestMessage.sender_id == {{ Auth::id() }} ? 'Bạn' : notification.latestMessage.sender.name; 
                    if (notification.latestMessage.type == 'image') {
                        message = `${senderName} đã gửi hình`;
                    } else if (notification.latestMessage.type == 'file') {
                        message = `${senderName} đã gửi file`;
                    } else {
                        message = notification.latestMessage.message;
                    }
                } else {
                    let creator = notification.conversationUsers.find(user => user.user_id == notification.created_by);
                    if (notification.created_by == {{ Auth::id() }}) {
                        message = 'Bạn đã tạo nhóm';
                    } else {
                        message = `${creator.nickname ? creator.nickname : creator.user.name} đã tạo nhóm`;
                    }
                }
                const time = notification.latestMessage ? notification.latestMessage.time_diff : `Tạo ${notification.time_diff}`;

                // Cập nhật nội dung HTML
                conversationElement.find('img').attr('src', avatarUrl);
                conversationElement.find('.chat-info h5').text(name);
                conversationElement.find('.chat-info p').text(message);
                conversationElement.find('.chat-time').text(time);

                console.log('Cập nhật cuộc trò chuyện:', conversationId);
                
                // Di chuyển cuộc trò chuyện lên đầu danh sách
                conversationElement.detach().prependTo('.chat-list-container');
            }

            document.querySelectorAll('.conversation-link').forEach(function(element) {
                element.addEventListener('click', function(event) {
                    event.preventDefault();
                    const conversationId = this.getAttribute('data-id');
                    loadConversation(conversationId);
                });
            });
        
            function loadConversation(conversationId) {
                fetch(`/conversation/${conversationId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.querySelector('.window-chat').innerHTML = data.html;
                        
                        reinitializeEvents(); // Khởi tạo lại các sự kiện sau khi nội dung được tải lại
                    })
                    .catch(error => showToast(error, 'error'));
            }
        
            function reinitializeEvents() {
                initializeModals();
                initializeScrollToBottom();
                initializeImageView();
                initializeViewAllMembers();
            }
        
            function initializeModals() {
                // Khởi tạo modal Thêm thành viên
                const addMembersModal = new bootstrap.Modal(document.getElementById('addMembersModal'));
        
                // Sự kiện mở modal
                const openAddMembersModalBtn = document.getElementById('openAddMembersModal');
                if (openAddMembersModalBtn) {
                    openAddMembersModalBtn.removeEventListener('click', showAddMembersModal);
                    openAddMembersModalBtn.addEventListener('click', showAddMembersModal);
                }
        
                function showAddMembersModal() {
                    addMembersModal.show();
                }
        
                // Sự kiện thêm thành viên đã chọn
                const addSelectedMembersBtn = document.getElementById('addSelectedMembers');
                if (addSelectedMembersBtn) {
                    addSelectedMembersBtn.removeEventListener('click', addSelectedMembers);
                    addSelectedMembersBtn.addEventListener('click', addSelectedMembers);
                }
        
                function addSelectedMembers() {
                    const selectedMembers = [];
                    document.querySelectorAll('#addMembersModal .form-check-input:checked').forEach(
                        checkbox => {
                            selectedMembers.push(checkbox.value);
                        });
                    console.log("Các thành viên được thêm:", selectedMembers);
                    // Đóng modal
                    addMembersModal.hide();
                }
            }
        
            function initializeScrollToBottom() {
                // Hàm để cuộn xuống dưới
                function scrollToBottom() {
                    const chatMessages = document.querySelector('.chat-messages');
                    if (chatMessages) {
                        chatMessages.scrollTop = chatMessages.scrollHeight; // Cuộn xuống dưới cùng
                    }
                }
        
                // Gọi hàm cuộn xuống khi trang tải xong
                scrollToBottom();
            }
        
            function initializeImageView() {
                // Ẩn hiện ảnh đã gửi
                const imagesList = document.querySelectorAll('.images-list .view-image');
                const viewToggleBtn = document.getElementById('view-toggle-btn');
                function hideExtraImages() {
                    imagesList.forEach((img, index) => {
                        img.classList.toggle('d-none', index >= 6);
                    });
                    viewToggleBtn.textContent = "Xem tất cả";
                }
        
                hideExtraImages();
        
                // Xử lý khi bấm nút "Xem tất cả" hoặc "Ẩn bớt"
                if (viewToggleBtn) {
                    viewToggleBtn.removeEventListener('click', toggleViewImages);
                    viewToggleBtn.addEventListener('click', toggleViewImages);
                }
        
                function toggleViewImages() {
                    if (viewToggleBtn.textContent === "Xem tất cả") {
                        imagesList.forEach(img => img.classList.remove('d-none'));
                        viewToggleBtn.textContent = "Ẩn bớt";
                    } else {
                        hideExtraImages();
                    }
                }
        
                // Hiển thị ảnh lớn khi bấm vào ảnh nhỏ
                imagesList.forEach(image => {
                    image.removeEventListener('click', showImageModal);
                    image.addEventListener('click', showImageModal);
                });
        
                function showImageModal() {
                    const modalImage = document.getElementById('modalImage');
                    if (modalImage) {
                        modalImage.src = this.src;
                        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                        imageModal.show();
                    } else {
                        //console.error("Không tìm thấy phần tử 'modalImage'.");
                    }
                }
            }

            let memberIdToRemove = null;
    let conversationIdToRemove = null;
    let currentPage = 1;
    const perPage = 5;

    function initializeViewAllMembers() {
        // xem tất cả thành viên
        const viewMembersBtn = document.getElementById('view-all-members-btn');
        const membersOffcanvas = document.getElementById('offcanvasMembers');
        // Kiểm tra xem cả hai phần tử có tồn tại hay không
        if (viewMembersBtn && membersOffcanvas) {
            // Khởi tạo offcanvas Bootstrap cho phần tử 'offcanvasMembers'
            const offcanvasInstance = new bootstrap.Offcanvas(membersOffcanvas);

            // Thêm sự kiện click cho nút để hiển thị offcanvas
            viewMembersBtn.removeEventListener('click', showMembersOffcanvas);
            viewMembersBtn.addEventListener('click', showMembersOffcanvas);

            function showMembersOffcanvas() {
                offcanvasInstance.show();
                const conversationId = document.getElementById('conversation_id').value;
                currentPage = 1; // Reset trang hiện tại
                loadMembers(conversationId, currentPage);
            }
        } else {
            //console.error("Không tìm thấy phần tử 'view-all-members-btn' hoặc 'offcanvasMembers'");
        }
    }

    function loadMembers(conversationId, page) {
        fetch(`/conversation/${conversationId}/members?page=${page}&per_page=${perPage}`)
            .then(response => response.json())
            .then(data => {
                const { members, currentUserRole, nextPage, hasMorePages } = data;
                console.log(currentUserRole);
                const membersList = document.querySelector('#offcanvasMembers .list-unstyled');
                if (page === 1) {
                    membersList.innerHTML = ''; // Xóa danh sách thành viên hiện tại nếu là trang đầu tiên
                }
                members.forEach(member => {
                    const leaderNote = member.role === 'gold' ? ' (trưởng nhóm)' : '';
                    const memberHtml = `
                        <li class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <img src="${member.avatar ? member.avatar : '/assets/images/avatar_default.jpg'}" alt="${member.name}"
                                    class="rounded-circle me-2" style="object-fit:cover; width: 40px; height: 40px;">
                                <span>${member.name}${leaderNote}</span>
                            </div>
                            ${currentUserRole === 'gold' && member.role !== 'gold' ? `<button class="btn btn-danger btn-sm ms-2 remove-member-btn" data-member-id="${member.id}"><i class="fa-solid fa-user-xmark"></i></button>` : ''}
                        </li>
                    `;
                    membersList.insertAdjacentHTML('beforeend', memberHtml);
                });

                // Thêm sự kiện click cho các nút xóa thành viên
                document.querySelectorAll('.remove-member-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        memberIdToRemove = this.getAttribute('data-member-id');
                        conversationIdToRemove = conversationId;
                        const confirmRemoveMemberModal = new bootstrap.Modal(document.getElementById('confirmRemoveMemberModal'));
                        confirmRemoveMemberModal.show();
                    });
                });

                // Hiển thị hoặc ẩn nút "Load More"
                const loadMoreBtn = document.getElementById('load-more-members-btn');
                if (hasMorePages) {
                    loadMoreBtn.style.display = 'block';
                    loadMoreBtn.setAttribute('data-next-page', nextPage);
                } else {
                    loadMoreBtn.style.display = 'none';
                }
            })
            .catch(error => console.error('Error loading members:', error));
    }

    document.getElementById('confirmRemoveMemberBtn').addEventListener('click', function() {
        if (memberIdToRemove && conversationIdToRemove) {
            removeMember(conversationIdToRemove, memberIdToRemove);
        }
    });

    document.getElementById('load-more-members-btn').addEventListener('click', function() {
        const conversationId = document.getElementById('conversation_id').value;
        const nextPage = this.getAttribute('data-next-page');
        loadMembers(conversationId, nextPage);
    });

    function removeMember(conversationId, memberId) {
        fetch(`/conversation/${conversationId}/remove-member`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ member_id: memberId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'success');
                loadMembers(conversationId, 1); // Tải lại danh sách thành viên từ trang đầu tiên sau khi xóa
                const confirmRemoveMemberModal = bootstrap.Modal.getInstance(document.getElementById('confirmRemoveMemberModal'));
                confirmRemoveMemberModal.hide(); // Đóng modal sau khi xóa thành công
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => console.error('Error removing member:', error));
    }

    // Gọi hàm khởi tạo sự kiện khi DOMContentLoaded
    initializeViewAllMembers();
        });
</script>

<script>
    showSavedToast();
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    // Khi mở modal, tải danh sách bạn bè qua AJAX
    document.getElementById('createGroupModal').addEventListener('shown.bs.modal', function() {
        fetch('/friends/list/group')
            .then(response => response.json())
            .then(friends => {
                let content = '';
                if (friends.length > 0) {
                    friends.forEach(friend => {
                        content += `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="${friend.id}" id="member${friend.id}" name="members[]">
                                <label class="form-check-label" for="member${friend.id}">
                                    <img src="${friend.avatar ? friend.avatar : '/assets/images/avatar_default.jpg'}" alt="avatar" width="60" height="60" style="border-radius: 50%">
                                    ${friend.name}
                                </label>
                            </div>`;
                    });
                } else {
                    content = '<p>Không có bạn bè nào.</p>';
                }
                document.getElementById('friendsListContent').innerHTML = content;
            })
            .catch(() => {
                document.getElementById('friendsListContent').innerHTML = '<p>Không thể tải danh sách bạn bè.</p>';
            });
    });
});

function submitGroup() {
    const formData = new FormData(document.getElementById('createGroupForm'));

    fetch('/groups', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success'); // Hiện thông báo thành công
            // Đóng modal hoặc thực hiện các hành động khác
            $('#createGroupModal').modal('hide');
            // Mở cuộc trò chuyện của nhóm mới tạo
            loadConversation(data.group_id);
        } else {
            showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error'); // Hiện thông báo lỗi
        }
    })
    .catch(() => {
        showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
    });
}

function loadConversation(conversationId) {
    fetch(`/conversation/${conversationId}`)
        .then(response => response.json())
        .then(data => {
            document.querySelector('.window-chat').innerHTML = data.html;
        })
        .catch(error => showToast(error, 'error'));
}

function filterMembers() {
    const input = document.getElementById('groupMembers').value.toLowerCase();
    const memberCheckboxes = document.querySelectorAll('#friendsListContent .form-check');

    memberCheckboxes.forEach(checkbox => {
        const label = checkbox.querySelector('.form-check-label');
        const memberName = label.textContent.toLowerCase();

        if (memberName.includes(input)) {
            checkbox.style.display = 'block'; // Hiện checkbox nếu tên thành viên phù hợp
        } else {
            checkbox.style.display = 'none'; // Ẩn checkbox nếu không phù hợp
        }
    });
}

function previewImageGroup(event) {
    const preview = document.getElementById('groupImagePreview');
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block'; // Hiển thị ảnh
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.style.display = 'none'; // Ẩn ảnh nếu không có file
    }
}

</script>
@endpush