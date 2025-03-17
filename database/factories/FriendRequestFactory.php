<?php

namespace Database\Factories;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FriendRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FriendRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sender_id' => User::factory(), // Tạo user mới
            'receiver_id' => User::factory(), // Tạo bạn mới
            'status' => 'pending', // Trạng thái mặc định
        ];
    }
}