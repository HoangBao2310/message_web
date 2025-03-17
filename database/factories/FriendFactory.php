<?php

namespace Database\Factories;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FriendFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Friend::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(), // Tạo user mới
            'friend_id' => User::factory(), // Tạo bạn mới
        ];
    }

    /**
     * Configure the factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Friend $friend) {
            // Đảm bảo rằng user_id và friend_id không bị trùng lặp
            if ($friend->user_id === $friend->friend_id) {
                $friend->friend_id = User::factory()->create()->id;
            }
        });
    }
}