@include('layouts.partials.header')

<div class="message_mini">
    @yield('content')
    <div class="container-fluid hehe">
        <div class="row h-100">
            <!-- Left Sidebar -->
            <section class="col-2 col-md-1 sidebar">
                @include('pages.modal.profile')
                <div class="d-flex justify-content-center align-items-center w-100">
                    <div class="w-100">
                        <div class="profile mb-4 mt-1 text-center">
                            <a href="#"><img src="{{ asset(Auth::user()->avatar) }}" alt="Profile Picture"
                                    class="rounded-circle" width="50" height="50" data-bs-toggle="modal"
                                    data-bs-target="#profileModal" style="cursor: pointer;"></a>
                        </div>
                        <ul class="nav flex-column align-items-center">
                            <li class="nav-item">
                                <a href="{{ route('home') }}" class="nav-link {{ Route::currentRouteNamed('home') ? 'active' : '' }}" >
                                    <i class="fa-solid fa-message text-white "
                                        style="font-size: 24px;"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('friends.list') }}" class="nav-link {{ Route::currentRouteNamed('friends.list') ? 'active' : '' }}">
                                    <i class="fa-solid fa-user text-white "
                                        style="font-size: 24px;"></i>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('logout') }}" class="nav-link {{ Route::currentRouteNamed('logout') ? 'active' : '' }}">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-white "
                                        style="font-size: 24px;"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Chat List -->
            <section class="chat-list d-none d-md-block col-md-3 col-xs-3 bg-white px-0"
                style="border-right: 0.5px solid rgba(224, 226, 225, 0.874);">
                <div class="search-bar mb-4 d-flex align-items-center border-bottom p-3">
                    <input type="text" class="form-control me-2" placeholder="{{ __('messages.search') }}"
                        id="searchMessages" oninput="searchMessages()">
                    <button class="btn-wrap"
                        data-bs-toggle="modal" data-bs-target="#addFriendModal">
                        <i class="fa-solid fa-user-plus"></i>
                    </button>
                    <button class="btn-wrap" type="button"
                        data-bs-toggle="modal" data-bs-target="#createGroupModal">
                        <a href="#"><i class="fa-solid fa-people-group"></i></a>
                    </button>
                </div>

                <div id="searchResults" style="display: none;">
                    <ul class="list-group" id="searchResultsList"></ul>
                </div>

                @yield('content-1')
            </section>
            <!-- Main Chat Window -->
            <section class="col-10 col-md-8 chat-window px-0">
                @yield('content-2')
            </section>
        </div>
    </div>

</div>




<!--modal thêm bạn-->
<div class="modal fade" id="addFriendModal" tabindex="-1" aria-labelledby="addFriendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFriendModalLabel">{{ __('messages.addNewFriend') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">

                    <label for="friendEmail" class="form-label">{{ __('messages.enterEmail') }}:</label>
                    <div class=" col-10">
                        <input type="email" class="form-control" id="friendEmail"
                            placeholder="{{ __('messages.enterEmail') }}" required
                            pattern="^[\w\.\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$"
                            title="Vui lòng nhập định dạng email hợp lệ." maxlength="100">
                    </div>

                    <div class="col-2">
                        <button type="button" class="btn btn-primary" id="searchButton" disabled><i
                                class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </div>

                <!-- Kết quả tìm kiếm -->
                <div class="search-result mt-3" id="searchResult" style="display: none;">
                    <div class="user-info">
                        <div class="avatar" style="float: left; margin-right: 10px;">
                            <img src="{{ asset('assets/images/logo/uocmo.jpg') }}" alt="Avatar"
                                class="rounded-circle" id="resultUserAvatar" style="height: 50px; width:50px;">
                        </div>
                        <div class="d-flex flex-column">
                            <div class="d-flex">
                                <p class="mb-0 me-2"><strong id="resultUserName"></strong></p> |
                                <p class="mb-0 ms-2" id="resultUserGender" style="color: gray;"></p>
                                <!-- Thêm giới tính -->
                            </div>
                            <p class="mb-0" id="resultUserEmail" style="color: gray;"></p>

                        </div>
                    </div>
                    <div class="d-flex mt-3">
                        <button type="button" class="btn btn-success" id="sendRequestButton"
                            style="display: none;">{{ __('messages.sendFriendRequest') }}</button>
                        <button type="button" class="btn btn-danger" id="cancelRequestButton"
                            style="display: none;">{{ __('messages.revokeRequest') }}</button>
                        
                        <button type="button" class="btn btn-success" id="acceptRequestButton"
                            style="display: none; margin-right: 5px; ">{{ __('messages.accept') }}</button>
                        <button type="button" class="btn btn-danger" id="declineRequestButton"
                            style="display: none;">{{ __('messages.refuse') }}</button>
                    </div>
                </div>


                <div id="errorMessage" class="mt-3 text-danger" style="display: none;"></div>
                <!-- Thêm phần thông báo lỗi -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('messages.close') }}</button>
            </div>
        </div>
    </div>
</div>



<!--modal các lời mời-->

<div class="modal fade" id="friendRequestsModal" tabindex="-1" aria-labelledby="friendRequestsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="friendRequestsModalLabel">{{ __('messages.friendRequestList') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="friendRequestsList">
                    <!-- Danh sách lời mời kết bạn sẽ được chèn ở đây -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('messages.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!--Modal tìm kiếm kết bạn-->
<div class="modal fade" id="friendSearchModal" tabindex="-1" aria-labelledby="friendSearchLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="friendSearchLabel">Thêm bạn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form tìm kiếm -->
                <div class="search-form">
                    <input type="text" class="form-control" id="emailSearch" placeholder="Nhập email bạn bè...">
                </div>

                <!-- Kết quả tìm kiếm -->
                <div class="search-result mt-3" style="display: none;" id="searchResult">
                    <div class="avatar" style="float: left; margin-right: 10px;">
                        <img src="" alt="Avatar" class="rounded-circle">
                    </div>
                    <div class="user-info">
                        <p><strong id="userName">User1</strong></p>
                        <p id="userEmail" style="color: gray;">User1@gmail.com</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="searchButton">Tìm kiếm</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal tạo nhóm -->
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createGroupModalLabel">{{ __('messages.createaNewGroup') }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createGroupForm" method="POST" action="{{ route('groups.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group d-flex">
                        <div class="group-image-container">
                            <label for="groupImageInput">
                                <div class="group-image-circle">
                                    <i class="fa-solid fa-camera"></i>
                                </div>
                            </label>
                            <input type="file" id="groupImageInput" name="avatar" style="display:none;"
                                onchange="previewImageGroup(event)">
                            <img id="groupImagePreview" src="" alt="Image Preview" style="display: none;">
                        </div>
                        <div class="group-name-container w-100"
                            style="padding-left: 20px; top: 15px; position: relative;">
                            <label for="groupName"> {{ __('messages.nameGroup') }}</label>
                            <input type="text" class="form-control" id="groupName" name="name"
                                placeholder="{{ __('messages.inputGroupName') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="groupMembers">{{ __('messages.nameMember') }}</label>
                        <input type="text" class="form-control" id="groupMembers"
                            placeholder="{{ __('messages.enterName') }}" oninput="filterMembers()">
                    </div>
                    <!-- Danh sách thành viên -->
                    <div class="list-group" id="membersList">
                        <label>{{ __('messages.checkboxMember') }}</label>
                        <div id="friendsListContent">
                            <!-- Danh sách bạn bè sẽ được load vào đây -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="submitGroup()">{{ __('messages.createGR') }}</button>
            </div>
        </div>
    </div>
</div>
@include('layouts.partials.footer')
<script>
    function searchMessages() {
        let query = document.getElementById('searchMessages').value;

        if (query.trim() === '') {
            document.getElementById('searchResults').style.display = 'none';
            return;
        }

        fetch(`/search-messages?q=${encodeURIComponent(query)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Mã lỗi: ' + response.status); // Kiểm tra lỗi phản hồi từ máy chủ
                }
                return response.json();
            })
            .then(data => {
                let searchResultsList = document.getElementById('searchResultsList');
                searchResultsList.innerHTML = '';

                if (data.status === 'success' && data.results.length > 0) {
                    data.results.forEach(item => {
                        let listItem = document.createElement('li');
                        listItem.classList.add('list-group-item');

                        listItem.innerHTML = `
                        <div class="d-flex align-items-center">
                            <img src="${item.avatar_url}" alt="Avatar" class="rounded-circle me-2" width="40" height="40">
                            <div class="info-mess">
                                <strong>${item.sender_name} - ${item.conversation_name}</strong>
                                <p class="mb-0">${item.message}</p>
                                <small class="text-muted">${item.created_at}</small>
                            </div>
                        </div>
                        <a data-conversation-id="${item.conversation_id}" class="dropdown-item open-conversation-search" style="padding: 8px 15px; color: #333; text-decoration: none; display: block;">
                            Xem tin nhắn
                        </a>
                    `;
                        searchResultsList.appendChild(listItem);
                    });
                    document.getElementById('searchResults').style.display = 'block';
                } else {
                    searchResultsList.innerHTML =
                        '<li class="list-group-item text-muted">Không tìm thấy tin nhắn phù hợp</li>';
                    document.getElementById('searchResults').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tìm kiếm tin nhắn.');
            });
    }
    document.addEventListener('keydown', function(event) {
        // Kiểm tra nếu bất kỳ modal nào đang mở
        const modalIsOpen = document.querySelector('.modal.show');

        // Nếu modal đang mở, không xử lý phím tắt
        if (modalIsOpen) {
            return;
        }

        // Tổ hợp phím "Alt + 1" (Đăng xuất)
        if (event.altKey && event.key === '1') {
            window.location.href = '{{ route('logout') }}'; // Chuyển hướng tới trang đăng xuất
        }
        // Tổ hợp phím "Alt + 2" (Danh sách bạn bè)
        else if (event.altKey && event.key === '2') {
            $('#friendsListModal').modal('show'); // Mở modal danh sách bạn bè
        }
        // Tổ hợp phím "Alt + 3" (Tạo nhóm)
        else if (event.altKey && event.key === '3') {
            $('#createGroupModal').modal('show'); // Mở modal tạo nhóm
        }
        // Tổ hợp phím "Alt + 4" (Thêm bạn)
        else if (event.altKey && event.key === '4') {
            $('#addFriendModal').modal('show'); // Mở modal thêm bạn
        }
        // Tổ hợp phím "Alt + 5" (Thông tin cá nhân)
        else if (event.altKey && event.key === '5') {
            $('#profileModal').modal('show'); // Mở modal thông tin cá nhân
        }
        // Tổ hợp phím "Alt + 6" (Tìm kiếm)
        else if (event.altKey && event.key === '6') {
            document.getElementById('searchMessages').focus(); // Tập trung vào ô tìm kiếm
        }
        // Tổ hợp phím "Alt + 7" (Cài đặt chủ đề)
        else if (event.altKey && event.key === '7') {
            $('#themeSettingsModal').modal('show'); // Mở modal cài đặt chủ đề
        }
        // Tổ hợp phím "Alt + 8" (Cài đặt chủ đề)
        else if (event.altKey && event.key === '8') {
            $('#languageSettingsModal').modal('show'); // Mở modal cài đặt chủ đề
        }
    });
</script>
