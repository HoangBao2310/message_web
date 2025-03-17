<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Message;
use App\Models\Socials;
use App\Models\Conversation;
use App\Models\ConversationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Lấy các cuộc trò chuyện mà user tham gia , 1-1 thì cần có tin nhắn, group thì không
        $conversations = Conversation::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where(function ($query) {
            $query->whereHas('messages') // Chỉ lấy các cuộc trò chuyện có tin nhắn
                  ->orWhere('is_group', true); // Hoặc là cuộc trò chuyện nhóm
        })
        ->with(['latestMessage', 'users' => function ($query) use ($userId) {
            $query->where('user_id', '!=', $userId); // Lấy thông tin người bạn
        }, 'creator', 'conversationUsers' => function ($query) {
            $query->with('user'); // Lấy thông tin user từ bảng conversation_user
        }])
        ->get();

        // Kiểm tra nếu không có cuộc trò chuyện nào
        if ($conversations->isEmpty()) {
            return view('pages.home', compact('conversations'));
        }

        // Lọc các cuộc trò chuyện không phải nhóm và lấy thông tin người bạn
        $conversations = $conversations->map(function ($conversation) use ($userId) {
            if (!$conversation->is_group) {
                $conversation->friend = $conversation->users->first();
            }

            // Lấy tin nhắn cuối cùng và định dạng thời gian
            $now = Carbon::now();
            if ($conversation->latestMessage) {
                $latestMessageTime = Carbon::parse($conversation->latestMessage->created_at);
               

                $conversation->latestMessage->time_diff = $this->formatTimeDiff($latestMessageTime, $now);
            }else{
                $conversation->time_diff = $this->formatTimeDiff($conversation->created_at, $now);
            }
            return $conversation;
        });

        //sửa có 1 cuộc trò chuyện chưa có tin nhắn thì phải sắp xếp theo thời gian tạo với thời gian tin nhắn mới nhất
        // Sắp xếp các cuộc trò chuyện theo tin nhắn mới nhất
        $conversations = $conversations->filter(function ($conversation) {
            return $conversation->latestMessage !== null || $conversation->is_group;
        })->sortByDesc(function ($conversation) {
            return $conversation->latestMessage ? $conversation->latestMessage->created_at : $conversation->created_at;
        });
        
        // Lấy cuộc trò chuyện mới nhất
        $latestConversation = $conversations->first();
        
        if ($latestConversation) {
            //các user trong cuộc trò chuyện
            $latestConversation->conversationUserInfo = ConversationUser::where('conversation_id', $latestConversation->id)
                ->get();

            // Lấy 20 tin nhắn mới nhất của cuộc trò chuyện này
            $latestConversation->messages = Message::where('conversation_id', $latestConversation->id)
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->with('sender')
                ->get();
                $latestConversation->images = $latestConversation->messages->filter(function ($message) {
                    return $message->type === 'image';
                }); 
                $latestConversation->files = $latestConversation->messages->filter(function ($message) {
                    return $message->type === 'file';
                });
                // dd($latestConversation->files);
        }
        return view('pages.home', compact('conversations', 'latestConversation'));
    }


    private function formatTimeDiff($latestTime, $now)
    {
        if ($latestTime->diffInSeconds($now) < 60) {
            return $latestTime->diffInSeconds($now) . __('messages.secondBefore');
        } elseif ($latestTime->diffInMinutes($now) < 60) {
            return $latestTime->diffInMinutes($now) . __('messages.minuteBefore');
        } elseif ($latestTime->diffInHours($now) < 24) {
            return $latestTime->diffInHours($now) . __('messages.hourBefore');
        } else {
            return $latestTime->diffInDays($now) . __('messages.dayBefore');
        }
    }
}
