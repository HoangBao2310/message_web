<?php

namespace Database\Factories;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3), // Tạo tên cuộc hội thoại ngẫu nhiên
            'is_group' => $this->faker->boolean, // Tạo giá trị ngẫu nhiên cho trường is_group
            'created_by' => $this->faker->numberBetween(1, 10), // Giả sử có 10 người dùng
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
