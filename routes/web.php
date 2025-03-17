<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SocialsController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\LanguageController; 
use App\Http\Controllers\ConversationController; 
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\PostController;
use Illuminate\Mail\Message;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/language/{lang}', [LanguageController::class, 'changeLanguage'])->name('language.switch');
Route::get('/', [ThemeController::class, 'index'])->name('home');
Route::post('/change-theme', [ThemeController::class, 'changeTheme'])->name('change.theme');


Route::group(['middleware' => ['auth']], function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    Route::get('/friend-requests', [FriendController::class, 'showFriendRequests'])->name('friend.requests'); //
    Route::post('/friend-requests/accept', [FriendController::class, 'acceptFriendRequestPage'])->name('friend.requests.accept.page'); // Đồng ý kết bạn từ trang friend-requests
    Route::post('/friend-requests/decline', [FriendController::class, 'declineFriendRequestPage'])->name('friend.requests.decline.page'); // Từ chối kết bạn từ trang friend-requests  
    Route::post('/search-friend', [FriendController::class, 'searchFriend']); // tìm kiếm người dùng
    Route::post('/send-friend-request', [FriendController::class, 'sendFriendRequest']); // gửi lời mời kết bạn
    Route::post('/check-friend-request-status', [FriendController::class, 'checkFriendRequestStatus'])->name('check.friend.request.status'); // kiểm tra trạng thái lời mời kết bạn
    Route::post('/cancel-friend-request', [FriendController::class, 'cancelFriendRequest'])->name('cancel.friend.request'); // thu hồi lời mời kết bạn
    Route::get('/get-friend-requests', [FriendController::class, 'getFriendRequests']); // lấy danh sách lời mời kết bạn
    Route::post('/accept-friend-request', [FriendController::class, 'acceptFriendRequest'])->name('friend.requests.accept'); // chấp nhận lời mời kết bạn
    Route::post('/decline-friend-request', [FriendController::class, 'declineFriendRequest'])->name('friend.requests.decline'); // từ chối lời mời kết bạn
    Route::get('/friends-list-modal', [FriendController::class, 'getFriendsList'])->name('friends.list.modal'); // danh sách bạn bè
    Route::get('/friends-list', [FriendController::class, 'showFriendsList'])->name('friends.list'); // danh sách bạn bè trang listfriend
    Route::post('/unfriend', [FriendController::class, 'unfriend'])->name('unfriend'); // Hủy kết bạn
    Route::get('/friends/search', [FriendController::class, 'searchFriends'])->name('friends.search'); // Tìm kiếm bạn bè
    Route::post('/profile/update', [UserController::class, 'update'])->name('profile.update');
    Route::get('/search-messages', [MessageController::class, 'searchMessages'])->name('messages.search');
    Route::get('/friends/list/group', [FriendController::class, 'getFriendsListGroup'])->name('friends.list.group');
    Route::get('/groups/create', [ConversationController::class, 'create'])->name('groups.create');
    Route::post('/groups', [ConversationController::class, 'createGroup'])->name('groups.store');
    Route::get('/friends/{id}', [FriendController::class, 'showFriendInfo'])->name('friend.show');
    

    Route::get('/conversation/{conversationId}/members', [ConversationController::class, 'getMembers'])->name('conversation.members'); // Danh sách thành viên
    Route::post('/conversation/{conversationId}/remove-member', [ConversationController::class, 'removeMember'])->name('conversation.removeMember'); // Xóa thành viên khỏi nhóm
    Route::get('/friends/available-for-group/{conversationId}', [ConversationController::class, 'getAvailableFriendsForGroup'])->name('friends.availableForGroup'); // Lấy danh sách bạn bè chưa có trong nhóm
    Route::post('/conversation/{conversationId}/add-members', [ConversationController::class, 'addMembers'])->name('conversation.addMembers'); // Thêm thành viên vào nhóm
    Route::get('/conversation/{conversation}/leave', [ConversationController::class, 'leaveGroup'])->name('conversation.leave'); // Rời nhóm

    Route::get('/chat/{conversationId}', [MessageController::class, 'showChat'])->name('chat.show');
    Route::post('/send-message', [MessageController::class, 'sendMessage'])->name('send.message');
    Route::get('/conversation/{conversationId}', [MessageController::class, 'openConversation'])->name('conversation');
    Route::get('/conversations/user/{userId}', [MessageController::class, 'openConversationByUser'])->name('conversation.user');

    Route::post('/check-membership', [MessageController::class, 'checkMembership'])->name('check.membership');
});



Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', function () {
        return view('pages.auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::get('/register', function () {
        return view('pages.auth.register');
    })->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::get('/forgot-password', function () {
        return view('pages.auth.forgot-password');
    })->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.password');

    Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});
