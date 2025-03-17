<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    // Model lời mời tham gia cuộc trò chuyện
    protected $fillable = ['conversation_id', 'invited_user_id', 'invited_by', 'status'];
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
