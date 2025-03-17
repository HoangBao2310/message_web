<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male','female','other']);
            $table->string('google_id')->nullable();
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->enum('active', ['active', 'inactive'])->default('inactive');
            $table->string('key_active')->nullable();
            $table->string('password');
            $table->string('key_reset_password')->nullable();
            $table->timestamp('key_reset_password_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_seen')->nullable(); // Thêm cột last_seen
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
