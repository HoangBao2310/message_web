<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'dob' => $this->faker->date,
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'google_id' => $this->faker->unique()->safeEmail,
            'email' => $this->faker->unique()->safeEmail,
            'avatar' => 'assets/images/logo/uocmo.jpg', 
            'cover_image' => 'assets/images/logo/uocmo.jpg',
            'description' => $this->faker->paragraph,
            'active' => $this->faker->randomElement(['active', 'inactive']),
            'key_active' => Str::random(10),
            'password' => bcrypt('123456'), // Mật khẩu mặc định
            'key_reset_password' => Str::random(10),
            'key_reset_password_at' => $this->faker->dateTime,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}