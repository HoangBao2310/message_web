<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationUser extends Model
{
    use HasFactory;

    protected $table = 'conversation_user';

    // Model người dùng trong cuộc trò chuyện
    protected $fillable = ['conversation_id', 'user_id', 'nickname', 'role', 'invited_by'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function invitation()
    {
        return $this->hasOne(Invitation::class, 'invited_user_id');
    }
}