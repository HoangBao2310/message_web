<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        return [
            'conversation_id' => $this->faker->numberBetween(1, 5), // Chọn ngẫu nhiên một cuộc hội thoại
            'sender_id' => $this->faker->numberBetween(1, 10), // Chọn ngẫu nhiên một người dùng
            'message' => $this->faker->sentence, // Tạo nội dung tin nhắn ngẫu nhiên
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
