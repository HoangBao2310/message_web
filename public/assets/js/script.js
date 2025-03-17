//hidden password
$(document).on('click', '#togglePassword', function () {
    const passwordField = $('#password');
    const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
    passwordField.attr('type', type);
    $(this).toggleClass('fa-eye fa-eye-slash');
});

//hidden password confirm
$(document).on('click', '#togglePasswordConfirm', function () {
    const passwordField = $('#password_confirmation');
    const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
    passwordField.attr('type', type);
    $(this).toggleClass('fa-eye fa-eye-slash');
});

//toast
function showToast(message, status) {
    const toastElement = $('#liveToast');
    const toastBody = toastElement.find('.toast-body');

    // Cập nhật nội dung tin nhắn
    toastBody.text(message);

    // Xóa các lớp nền cũ
    toastElement.removeClass('bg-success bg-danger text-white');

    // Thêm lớp nền dựa trên trạng thái
    if (status === 'success') {
        toastElement.addClass('bg-success text-white');
    } else if (status === 'error') {
        toastElement.addClass('bg-danger text-white');
    }

    // Hiển thị toast
    const toast = new bootstrap.Toast(toastElement[0]);
    toast.show();
}

//save toast
function saveToast(message, status) {
    sessionStorage.setItem('toastMessage', message);
    sessionStorage.setItem('toastStatus', status);
}

//show toast
function showSavedToast() {
    const message = sessionStorage.getItem('toastMessage');
    const status = sessionStorage.getItem('toastStatus');

    if (message && status) {
        showToast(message, status);
        sessionStorage.removeItem('toastMessage');
        sessionStorage.removeItem('toastStatus');
    }
}

//otp
function handleInput(element) {
    $(element).val($(element).val().replace(/[^0-9]/g, ''));

    if ($(element).val().length === 1) {
        const nextInput = $(element).next('.otp-input');
        if (nextInput.length) {
            nextInput.focus();
        }
    }
}

// Lưu vị trí cuộn trang khi chuyển trang
$(document).ready(function () {
    $('.click-scroll').on('click', function (e) {
        localStorage.setItem('scrollPosition', $(window).scrollTop());
        window.location.href = $(this).attr('href');
    });

    const scrollPosition = localStorage.getItem('scrollPosition');
    if (scrollPosition) {
        $(window).scrollTop(scrollPosition);
        localStorage.removeItem('scrollPosition');
    }
});


// Danh sách bạn bè
document.addEventListener('DOMContentLoaded', function () {
    const friendsListModal = document.getElementById('friendsListModal');
    const friendsList = document.getElementById('friendsList');

    // Gọi hàm loadFriendsList khi modal được mở
    friendsListModal.addEventListener('show.bs.modal', function () {
        loadFriendsList();
    });

    
     // Hàm asset để tạo đường dẫn đầy đủ
     function asset(path) {
        return `${window.location.origin}/${path}`;
    }
});

// Gọi hàm loadFriendRequests khi modal được mở
document.getElementById('showFriendRequestsModal').addEventListener('click', function () {
    loadFriendRequests();
});

// hàm xử lý tìm kiếm người dùng
document.getElementById('searchButton').addEventListener('click', function () {
    const email = document.getElementById('friendEmail').value.trim();

    if (email === "") {
        showToast('Vui lòng nhập email người dùng.', 'error');
        return;
    }

    fetch('/search-friend', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ email: email })
    })
        .then(response => response.json())
        .then(data => {
            const searchResult = document.getElementById('searchResult');
            const resultUserName = document.getElementById('resultUserName');
            const sendRequestButton = document.getElementById('sendRequestButton');
            const cancelRequestButton = document.getElementById('cancelRequestButton');
            const messageButtonn = document.getElementById('messageButtonn');
            const acceptRequestButton = document.getElementById('acceptRequestButton');
            const declineRequestButton = document.getElementById('declineRequestButton');

            resultUserName.textContent = '';
            searchResult.style.display = "none";

            if (data.status === "success") {
                const userAvatar = data.user.avatar ? asset(data.user.avatar) : asset('assets/images/logo/uocmo.jpg');

                document.getElementById('resultUserAvatar').src = userAvatar;
                document.getElementById('resultUserName').textContent = data.user.name || "Không có tên";
                document.getElementById('resultUserEmail').textContent = data.user.email || "Không có email";
                document.getElementById('resultUserGender').textContent = data.user.gender;

                searchResult.style.display = "block";

                checkFriendRequestStatus(data.user.id, sendRequestButton, cancelRequestButton, acceptRequestButton, declineRequestButton, messageButtonn);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
        });
});

document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('friendEmail');
    const searchButton = document.getElementById('searchButton');

    // Hàm kiểm tra tính hợp lệ của email
    function validateEmail() {
        return emailInput.checkValidity();
    }

    // Sự kiện khi người dùng nhập vào ô email
    emailInput.addEventListener('input', function () {
        if (validateEmail()) {
            searchButton.disabled = false;
        } else {
            searchButton.disabled = true;
        }
    });
});

// Hàm kiểm tra trạng thái yêu cầu kết bạn
function checkFriendRequestStatus(userId, sendRequestButton, cancelRequestButton) {
    fetch('/check-friend-request-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ friend_id: userId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "friends") {
                messageButtonn.style.display = 'block';
                sendRequestButton.style.display = 'none';
                cancelRequestButton.style.display = 'none';
                acceptRequestButton.style.display = 'none';
                declineRequestButton.style.display = 'none';
                //hai người đã là bạn bè
            } else if (data.status === "sent") {
                sendRequestButton.style.display = 'none';
                cancelRequestButton.style.display = 'block';
                messageButtonn.style.display = 'none';
                acceptRequestButton.style.display = 'none';
                declineRequestButton.style.display = 'none';
            } else if (data.status === "received") {
                sendRequestButton.style.display = 'none';
                cancelRequestButton.style.display = 'none';
                messageButtonn.style.display = 'none';
                acceptRequestButton.style.display = 'block';
                declineRequestButton.style.display = 'block';

                acceptRequestButton.onclick = function () {
                    acceptSearchRequest(data.request_id);
                };

                declineRequestButton.onclick = function () {
                    declineSearchRequest(data.request_id);
                };
                //người này đã gửi lời mời kết bạn cho bạn
            } else {
                sendRequestButton.style.display = 'block';
                cancelRequestButton.style.display = 'none';
                messageButtonn.style.display = 'none';
                acceptRequestButton.style.display = 'none';
                declineRequestButton.style.display = 'none';
            }

            sendRequestButton.onclick = function () {
                sendFriendRequest(userId);
            };

            cancelRequestButton.onclick = function () {
                cancelFriendRequest(userId);
            };
        })
        .catch(error => {
            saveToast('Có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
        });
}

// Hàm gửi yêu cầu kết bạn
function sendFriendRequest(userId) {
    fetch('/send-friend-request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ friend_id: userId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                showToast(data.message, 'success');
                checkFriendRequestStatus(userId, sendRequestButton, cancelRequestButton);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
        });
}

// Hàm hủy yêu cầu kết bạn
function cancelFriendRequest(userId) {
    fetch('/cancel-friend-request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ friend_id: userId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                showToast(data.message, 'success');
                checkFriendRequestStatus(userId, sendRequestButton, cancelRequestButton);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
        });
}

// Hàm đồng ý kết bạn từ ô tìm kiếm
function acceptSearchRequest(requestId) {
    fetch('/accept-friend-request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ request_id: requestId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                showToast(data.message, 'success');
                // Cập nhật lại trạng thái của các nút trong ô tìm kiếm
                document.getElementById('acceptRequestButton').style.display = 'none';
                document.getElementById('declineRequestButton').style.display = 'none';
                document.getElementById('messageButtonn').style.display = 'block';
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
        });
}

// Hàm từ chối kết bạn từ ô tìm kiếm
function declineSearchRequest(requestId) {
    fetch('/decline-friend-request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ request_id: requestId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                showToast(data.message, 'success');
                // Cập nhật lại trạng thái của các nút trong ô tìm kiếm
                document.getElementById('acceptRequestButton').style.display = 'none';
                document.getElementById('declineRequestButton').style.display = 'none';
                document.getElementById('sendRequestButton').style.display = 'block';
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
        });
}
// hàm xử lý lời mời kết bạn

function loadFriendRequests() {
    fetch('/get-friend-requests', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                const friendRequestsList = document.getElementById('friendRequestsList');
                friendRequestsList.innerHTML = ''; // Xóa nội dung hiện tại
                
                // Duyệt qua danh sách lời mời và tạo HTML
                
                data.requests.forEach(request => {
                    const requestTime = new Date(request.created_at).toLocaleString(); // Chuyển đổi thời gian thành chuỗi dễ đọc
                    friendRequestsList.innerHTML += `
                       <div class="friend-request-item"
    style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border: 1px solid #ddd; border-radius: 5px; ">
    <div style="display: flex; align-items: center;">
        <img src="${asset(request.sender.avatar)}" alt="${request.sender.name}"
            style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
        <div>
            <p style="margin: 0; font-weight: bold;">${request.sender.name}</p>
            <p style="margin: 0; color: #888;">Gửi lúc: ${requestTime}</p>
        </div>
    </div>
    <div style="display: flex; align-items: center;">
        <button class="btn btn-success" style="margin-right: 5px; position: relative; width: 110px; height: 40px;  display: flex; align-items: center; color: white; flex-direction: column; justify-content: center; border: none; padding: 12px; gap: 12px; border-radius: 8px; cursor: pointer;
    }" onclick="acceptRequest(${request.id})">Chấp nhận</button> 
    <button class="btn btn-danger" style="width: 110px; height: 40px;  border-radius: 5px; padding: 10px 25px cursor: pointer; transition: all 0.3s ease; position: relative; display: inline-block 
     outline: none;" onclick="declineRequest(${request.id})">Từ chối</button>
    </div>
</div>
                    `;
                });
            } else {
                saveToast(data.message, 'error');
            }
        })
        .catch(error => {
            saveToast('Có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
        });
        
}
function asset(path) {
    // Loại bỏ dấu gạch chéo thừa ở đầu đường dẫn nếu có
    if (path.startsWith('/')) {
        path = path.substring(1);
    }
    return `${window.location.origin}/${path}`;
}
// Hàm đồng ý kết bạn
function acceptRequest(requestId) {
    fetch('/accept-friend-request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ request_id: requestId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                showToast(data.message, 'success');
                loadFriendRequests(); // Cập nhật lại danh sách lời mời
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
        });
}

// Hàm từ chối kết bạn
function declineRequest(requestId) {
    fetch('/decline-friend-request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ request_id: requestId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                showToast(data.message, 'success');
                loadFriendRequests(); // Cập nhật lại danh sách lời mời
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
        });
}


// Hàm biểu tượng ảnh đã có
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        const output = document.createElement('img');
        output.src = reader.result;

        const inputId = event.target.id;
        const imageCircle = document.querySelector('.group-image-circle');

        if (inputId === 'cover_image') {
            const coverImagePreview = document.getElementById('coverImagePreview');
            coverImagePreview.src = reader.result; // Cập nhật ảnh bìa
        } else if (inputId === 'avatar') {
            const avatarPreview = document.getElementById('avatarPreview');
            avatarPreview.src = reader.result; // Cập nhật ảnh đại diện
        }
    };
    reader.readAsDataURL(event.target.files[0]);
}




    // Hàm tải danh sách bạn bè
    function loadFriendsList() {
        fetch('/friends-list-modal', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    friendsList.innerHTML = ''; // Xóa nội dung hiện tại
                    
                    // Duyệt qua danh sách bạn bè và tạo HTML
                    data.friends.forEach(friend => {
                        friendsList.innerHTML += `
                            <div class="friend-item">
                                <img src="${friend.avatar}" alt="${friend.name}" class="avatar" style="height:96px; width:96px;">
                                <p>${friend.name}</p>
                            </div>
                        `;
                    });
                } else {
                    saveToast(data.message, 'error');
                }
            })
            .catch(error => {
                saveToast('Có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
            });
    }

         //Hàm chuyển đổi ngôn ngữ
         document.getElementById('saveSettingsBtn').addEventListener('click', function() {
            var selectedLang = document.getElementById('languageSelect').value;
            // Gửi yêu cầu đến route để thay đổi ngôn ngữ
            fetch(`/language/${selectedLang}`)
            .then(response => {
                if (response.ok) {
                    // Tái tải trang để áp dụng ngôn ngữ mới
                    location.reload();
                } else {
                    // Xử lý lỗi nếu cần
                    console.error('Error changing language');
                }
            })
            .catch(error => console.error('Fetch error:', error));
        });


// ---------------Modal tạo nhóm------------------




// ------------------------Đóng---------------------
$(document).ready(function() {
    // Khởi tạo modal Thêm thành viên
    const addMembersModal = new bootstrap.Modal(document.getElementById('addMembersModal'));

    // Sự kiện mở modal
    $(document).on('click', '.openAddMembersModal', function() {
        const conversationId = $(this).data('conversation-id');
        const isGroup = $(this).data('is-group');

        if (isGroup) {
            loadFriends(conversationId);
            addMembersModal.show();
        } else {
            // Nếu không phải nhóm, không làm gì cả
            console.log('Cuộc trò chuyện này không phải là nhóm.');
        }
    });

    // Sự kiện thêm thành viên đã chọn
    $('#addSelectedMembers').on('click', function() {
        const selectedMembers = [];
        $('#addMembersModal .form-check-input:checked').each(function() {
            selectedMembers.push($(this).val());
        });

        // Gửi danh sách bạn bè được chọn đến server để thêm vào nhóm chat
        const conversationId = $('#conversation_id').val();
        $.ajax({
            url: `/conversation/${conversationId}/add-members`,
            method: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify({ members: selectedMembers }),
            success: function(data) {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    addMembersModal.hide();
                } else {
                    showToast(data.message, 'error');
                }
            },
            error: function(error) {
                if (error.responseJSON && error.responseJSON.message) {
                    showToast(error.responseJSON.message, 'error');
                } else {
                    console.error('Error adding members:', error);
                }
            }
        });
    });

    function loadFriends(conversationId) {
        $.ajax({
            url: `/friends/available-for-group/${conversationId}`,
            method: 'GET',
            success: function(friends) {
                const friendsList = $('#addMembersModal .modal-body');
                friendsList.empty(); // Xóa danh sách bạn bè hiện tại

                friends.forEach(friend => {
                    const friendHtml = `
                        <div class="form-check">
                            <input class="form-check-input" name="friend" type="checkbox" value="${friend.id}" id="friend${friend.id}">
                            <label class="form-check-label" for="friend${friend.id}">
                                <img src="${friend.avatar ? friend.avatar : '/assets/images/avatar_default.jpg'}" alt="${friend.name}" class="rounded-circle me-2" style="object-fit: cover" width="30" height="30">
                                ${friend.name}
                            </label>
                        </div>
                    `;
                    friendsList.append(friendHtml);
                });
            },
            error: function(error) {
                console.error('Error loading friends:', error);
            }
        });
    }
});
// Hàm để cuộn xuống dưới
function scrollToBottom() {
    const chatMessages = document.querySelector('.chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight; // Cuộn xuống dưới cùng
    }
}

// Gọi hàm cuộn xuống khi trang tải xong
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();

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
    viewToggleBtn.addEventListener('click', function() {
        if (viewToggleBtn.textContent === "Xem tất cả") {
            imagesList.forEach(img => img.classList.remove('d-none'));
            viewToggleBtn.textContent = "Ẩn bớt";
        } else {
            hideExtraImages();
        }
    });

    // Hiển thị ảnh lớn khi bấm vào ảnh nhỏ
    imagesList.forEach(image => {
        image.addEventListener('click', function() {
            const modalImage = document.getElementById('modalImage');
            if (modalImage) {
                modalImage.src = this.src;
                const imageModal = new bootstrap.Modal(document.getElementById(
                    'imageModal'));
                imageModal.show();
            } else {
                //console.error("Không tìm thấy phần tử 'modalImage'.");
            }
        });
    });
});

// JavaScript để xử lý nút "Xem tất cả" và mở modal ảnh
document.addEventListener('DOMContentLoaded', function() {
    const viewAllBtn = document.getElementById('view-all-btn');
    const extraImages = document.querySelector('.extra-images');
    // Kiểm tra xem cả hai phần tử có tồn tại không
    if (viewAllBtn && extraImages) {
        viewAllBtn.addEventListener('click', function() {
            extraImages.classList.toggle('d-none');
            this.innerText = this.innerText === "Xem tất cả" ? "Ẩn bớt" : "Xem tất cả";
        });
    }
});

// Xử lý sự kiện khi nhấn vào ảnh để xem lớn
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-image').forEach(image => {
        image.addEventListener('click', function() {
            const modalImage = document.getElementById('modalImage');
            if (modalImage) {
                modalImage.src = this.src;
                const imageModal = new bootstrap.Modal(document.getElementById(
                    'imageModal'));
                imageModal.show();
            } else {
                //console.error("Không tìm thấy phần tử 'modalImage'.");
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const themeSelect = document.getElementById('themeSelect');
    const saveSettingsBtnTheme = document.getElementById('saveSettingsBtnTheme');
    const currentTheme = localStorage.getItem('theme') || 'light';

    // Áp dụng theme hiện tại từ localStorage
    if (currentTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeSelect.value = 'dark'; // Dark Mode tương ứng với 'dark'
    } else {
        themeSelect.value = 'light'; // Light Mode tương ứng với 'light'
    }

    // Lưu theme khi người dùng nhấn nút "Đồng ý"
    saveSettingsBtnTheme.addEventListener('click', function () {
        const selectedTheme = themeSelect.value;
        if (selectedTheme === 'dark') { // Nếu chọn "Dark Mode"
            document.body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        } else { // Nếu chọn "Light Mode"
            document.body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
        }

        // Ẩn modal sau khi lưu cài đặt
        const modalElement = document.getElementById('themeSettingsModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();
    });
});


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

// Lắng nghe sự kiện WebSocket khi người dùng rời nhóm
// Echo.private(`chat.${conversationId}`)
//     .listen('UserLeftGroup', (e) => {
//         if (e.user_id === currentUserId) {
//             showToast('Bạn đã rời nhóm.', 'info');
//             removeConversationFromList(conversationId);
//             clearChatWindow();
//         }
//     });

function leaveGroup(conversationId) {
    fetch(`/conversation/${conversationId}/leave`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message, 'success');
            // Cập nhật giao diện người dùng sau khi rời nhóm thành công
            removeConversationFromList(conversationId);
            clearChatWindow();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => console.error('Error leaving group:', error));
}

function removeConversationFromList(conversationId) {
    const conversationElement = document.querySelector(`.conversation-link[data-id="${conversationId}"]`);
    if (conversationElement) {
        conversationElement.remove();
    }
}

function clearChatWindow() {
    const chatWindow = document.querySelector('.window-chat');
    if (chatWindow) {
        chatWindow.innerHTML = `
            <div style="text-align: center; padding: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;">
                <p style="font-size: 18px; font-weight: bold;">Bạn đã rời nhóm.</p>
                <p>Vui lòng chọn một cuộc trò chuyện khác.</p>
            </div>
        `;
    }
}