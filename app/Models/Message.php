<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;


    // Model tin nhắn
    protected $fillable = ['conversation_id', 'sender_id', 'message','type'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender() // Quan hệ với người gửi
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
