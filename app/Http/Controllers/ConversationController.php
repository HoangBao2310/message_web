<?php

namespace App\Http\Controllers;
use App\Models\ConversationUser; 
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\UserLeftGroup;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation)
    {
        // Lấy danh sách thành viên của nhóm chat
        $members = $conversation->users()->get();

        // Trả về view với dữ liệu nhóm chat và danh sách thành viên
        return view('components.window_chat', compact('conversation', 'members'));
    }

    public function getMembers(Request $request, $conversationId)
    {
       $conversation = Conversation::findOrFail($conversationId);
    $perPage = $request->input('per_page', 5); // Số lượng thành viên mỗi trang
    $page = $request->input('page', 1); // Trang hiện tại

    $members = $conversation->users()->withPivot('role')
        ->paginate($perPage, ['*'], 'page', $page);

    // Thêm thông tin về vai trò vào danh sách thành viên
    $members->getCollection()->transform(function ($member) {
        $member->role = $member->pivot->role;
        return $member;
    });

    // Lấy vai trò của người dùng hiện tại
    $currentUserRole = auth()->user()->conversations()->where('conversation_id', $conversationId)->first()->pivot->role;

    return response()->json([
        'members' => $members->items(),
        'currentUserRole' => $currentUserRole,
        'nextPage' => $members->currentPage() + 1,
        'hasMorePages' => $members->hasMorePages()
    ]);
    }
    public function removeMember(Request $request, $conversationId)
{
    $conversation = Conversation::findOrFail($conversationId);
    $user = auth()->user();
    $memberId = $request->input('member_id');

    // Kiểm tra xem người dùng hiện tại có phải là trưởng nhóm không
    if ($conversation->users()->where('user_id', $user->id)->wherePivot('role', 'gold')->exists()) {
        // Kiểm tra xem nhóm trưởng có đang cố gắng tự xóa mình không
        if ($user->id == $memberId) {
            return response()->json(['status' => 'error', 'message' => 'Nhóm trưởng không thể tự xóa mình.'], 403);
        }

        // Kiểm tra xem thành viên có còn trong nhóm hay không
        if ($conversation->users()->where('user_id', $memberId)->exists()) {
            $conversation->users()->detach($memberId);
            return response()->json(['status' => 'success', 'message' => 'Thành viên đã được xóa khỏi nhóm.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Thành viên không còn trong nhóm.'], 404);
        }
    } else {
        return response()->json(['status' => 'error', 'message' => 'Bạn không có quyền xóa thành viên.'], 403);
    }
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Conversation $conversation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conversation $conversation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        //
    }
    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Kiểm tra avatar
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
        ]);
    
        // Tạo nhóm mới
        $group = new Conversation();
        $group->name = $request->name;
        $group->is_group = 1; // Đánh dấu là nhóm
        $group->created_by = auth()->id(); // Gán người tạo
        if ($request->hasFile('avatar')) {
            $filePath = $request->file('avatar')->store('public/uploads/images/groups');
            $group->avatar = $filePath;
        }
        $group->save();
    
        // Thêm người dùng vào nhóm
        foreach ($request->members as $memberId) {
            ConversationUser::create([
                'conversation_id' => $group->id,
                'user_id' => $memberId,
                'invited_by' => auth()->id(),
                'role' => 'member', // Gán vai trò là thành viên
            ]);
        }
    
        // Thêm người tạo vào nhóm với vai trò trưởng nhóm (gold)
        ConversationUser::create([
            'conversation_id' => $group->id,
            'user_id' => auth()->id(),
            'invited_by' => auth()->id(),
            'role' => 'gold', // Gán vai trò là trưởng nhóm (gold)
        ]);
    
        return response()->json(['success' => true, 'message' => __('messages.group_ok'), 'group_id' => $group->id]);
    }

    // lấy danh sách bạn bè chưa có trong nhóm
    public function getAvailableFriendsForGroup($conversationId)
    {
        $user = Auth::user();
    
        // Lấy danh sách bạn bè bao gồm cả hai chiều
        $friends = DB::table('friends')
            ->join('users', function ($join) use ($user) {
                $join->on('friends.friend_id', '=', 'users.id')
                     ->orOn('friends.user_id', '=', 'users.id');
            })
            ->where(function ($query) use ($user) {
                $query->where('friends.user_id', $user->id)
                      ->orWhere('friends.friend_id', $user->id);
            })
            ->where('users.id', '!=', $user->id)
            ->whereNotIn('users.id', function ($query) use ($conversationId) {
                $query->select('user_id')
                      ->from('conversation_user')
                      ->where('conversation_id', $conversationId);
            })
            ->select('users.id', 'users.name', 'users.avatar')
            ->distinct()
            ->get();
    
        return response()->json($friends);
    }

    public function addMembers(Request $request, $conversationId)
{
    $conversation = Conversation::findOrFail($conversationId);
    $user = Auth::user();

    if (!$conversation->users()->where('user_id', $user->id)->exists()) {
        return response()->json(['status' => 'error', 'message' => __('messages.group_no')], 403);
    }

    $memberIds = $request->input('members', []);

    if (empty($memberIds)) {
        return response()->json(['status' => 'error', 'message' => __('messages.group_1')], 400);
    }

    // Sử dụng cơ chế khóa để đảm bảo không có thao tác thêm trùng lặp
    DB::beginTransaction();
    try {
        // Lấy danh sách các thành viên hiện tại trong nhóm
        $existingMemberIds = $conversation->users()->lockForUpdate()->pluck('user_id')->toArray();

        // Lọc ra những thành viên chưa có trong nhóm
        $newMemberIds = array_diff($memberIds, $existingMemberIds);

        if (empty($newMemberIds)) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Yêu cầu đã được xử lý hoặc không còn tồn tại nữa!'], 400);
        }

        // Thêm các thành viên mới vào nhóm
        $conversation->users()->attach($newMemberIds, ['role' => 'member']);

        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'Thành viên đã được thêm vào nhóm.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 'error', 'message' => 'Đã xảy ra lỗi khi thêm thành viên!'], 500);
    }
}
public function leaveGroup($conversationId)
{
    $conversation = Conversation::findOrFail($conversationId);
    $user = auth()->user();

    // Kiểm tra xem người dùng có phải là thành viên của nhóm không
    if ($conversation->users()->where('user_id', $user->id)->exists()) {
        // Xóa người dùng khỏi nhóm
        $conversation->users()->detach($user->id);

        // Phát sự kiện WebSocket để thông báo rằng người dùng đã rời nhóm
        broadcast(new UserLeftGroup($conversationId, $user->id))->toOthers();

        return response()->json(['status' => 'success', 'message' => 'Bạn đã rời nhóm thành công.']);
    } else {
        return response()->json(['status' => 'error', 'message' => 'Bạn không phải là thành viên của nhóm hoặc đã rời nhóm.'], 403);
    }
}
}
