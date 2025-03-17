<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Friend;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\ConversationUser;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy ba người dùng đã là bạn bè của nhau
        $user1 = User::where('email', 'user1@gmail.com')->first();
        $user2 = User::where('email', 'user2@gmail.com')->first();
        $user3 = User::where('email', 'user3@gmail.com')->first();

        // Tạo cuộc trò chuyện và tin nhắn giữa user1 và user2
        $this->createConversationAndMessages($user1, $user2);

        // Tạo cuộc trò chuyện và tin nhắn giữa user1 và user3
        $this->createConversationAndMessages($user1, $user3);
    }

    private function createConversationAndMessages($user1, $user2)
    {
        // Kiểm tra xem hai người dùng có phải là bạn bè của nhau không
        $friendship = Friend::where(function ($query) use ($user1, $user2) {
            $query->where('user_id', $user1->id)
                ->where('friend_id', $user2->id);
        })->orWhere(function ($query) use ($user1, $user2) {
            $query->where('user_id', $user2->id)
                ->where('friend_id', $user1->id);
        })->first();

        if ($friendship) {
            // Tạo cuộc trò chuyện giữa hai người dùng
            $conversation = Conversation::create([
                'created_by' => $user1->id,
                'is_group' => false,
            ]);

            // Thêm hai người dùng vào cuộc trò chuyện
            ConversationUser::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user1->id,
            ]);

            ConversationUser::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user2->id,
            ]);

            // Tạo một số tin nhắn giữa hai người dùng
            $messages = [
                'Xin chào, bạn có khỏe không?',
                'Tôi khỏe, cảm ơn bạn. Còn bạn thì sao?',
                'Tôi cũng khỏe. Bạn đang làm gì vậy?',
                'Tôi đang làm việc. Bạn thì sao?',
                'Tôi đang học lập trình Laravel.',
            ];

            foreach ($messages as $index => $messageText) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $index % 2 == 0 ? $user1->id : $user2->id,
                    'message' => $messageText,
                ]);
            }
        }
    }
}