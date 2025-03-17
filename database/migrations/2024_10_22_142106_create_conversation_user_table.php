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
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade'); // Liên kết với conversation
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Liên kết với user
            $table->string('nickname')->nullable(); // Biệt danh của user trong cuộc trò chuyện, nullable vì có thể không cần biệt danh
            $table->enum('role', ['gold','silver', 'member'])->default('member'); // Quyền của user trong nhóm
            $table->foreignId('invited_by')->nullable()->constrained('users'); // Ai đã mời vào nhóm
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_user');
    }
};
