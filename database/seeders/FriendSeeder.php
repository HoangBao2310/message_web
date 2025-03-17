<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Friend;
use App\Models\FriendRequest;

class FriendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo 5 user cụ thể với thông tin xác định trước và đặt mật khẩu
        $specificUsers = collect([
            User::factory()->create([
                'name' => 'User 1',
                'email' => 'user1@gmail.com',
                'password' => bcrypt('111111'),
                'active' => 'active',
                'email_verified_at' => now(),
            ]),
            User::factory()->create([
                'name' => 'User 2',
                'email' => 'user2@gmail.com',
                'password' => bcrypt('222222'),
                'active' => 'active',
                'email_verified_at' => now(),
            ]),
            User::factory()->create([
                'name' => 'User 3',
                'email' => 'user3@gmail.com',
                'password' => bcrypt('333333'),
                'active' => 'active',
                'email_verified_at' => now(),
            ]),
            User::factory()->create([
                'name' => 'User 4',
                'email' => 'user4@gmail.com',
                'password' => bcrypt('444444'),
                'active' => 'active',
                'email_verified_at' => now(),
            ]),
            User::factory()->create([
                'name' => 'User 5',
                'email' => 'user5@gmail.com',
                'password' => bcrypt('555555'),
                'active' => 'active',
                'email_verified_at' => now(),
            ]),
        ]);

        // Tạo 50 user ngẫu nhiên
        $randomUsers = User::factory()->count(50)->create();

        // Tạo mối quan hệ bạn bè giữa các user cụ thể
        foreach ($specificUsers as $user) {
            foreach ($specificUsers as $friend) {
                if ($user->id !== $friend->id) {
                    Friend::factory()->create([
                        'user_id' => $user->id,
                        'friend_id' => $friend->id,
                    ]);
                }
            }
        }

        // Tạo 10 mối quan hệ bạn bè cho mỗi user cụ thể với các user ngẫu nhiên
        foreach ($specificUsers as $user) {
            // Lấy ngẫu nhiên 10 user khác để làm bạn
            $friendIds = $randomUsers->where('id', '!=', $user->id)->random(10)->pluck('id');

            foreach ($friendIds as $friendId) {
                Friend::factory()->create([
                    'user_id' => $user->id,
                    'friend_id' => $friendId,
                ]);
            }
        }

        // Tạo 5 lời mời kết bạn cho mỗi user cụ thể từ các tài khoản ngẫu nhiên
        foreach ($specificUsers as $user) {
            // Lấy ngẫu nhiên 5 user khác để gửi lời mời kết bạn
            $friendRequestIds = $randomUsers->where('id', '!=', $user->id)->random(5)->pluck('id');

            foreach ($friendRequestIds as $friendRequestId) {
                FriendRequest::factory()->create([
                    'sender_id' => $friendRequestId, // Người gửi là user ngẫu nhiên
                    'receiver_id' => $user->id, // Người nhận là user cụ thể
                ]);
            }
        }

        // Tạo các mối quan hệ bạn bè ngẫu nhiên cho các user còn lại
        foreach ($randomUsers as $user) {
            // Lấy ngẫu nhiên 10 user khác để làm bạn
            $friendIds = $randomUsers->where('id', '!=', $user->id)->random(10)->pluck('id');

            foreach ($friendIds as $friendId) {
                Friend::factory()->create([
                    'user_id' => $user->id,
                    'friend_id' => $friendId,
                ]);
            }
        }
    }
}