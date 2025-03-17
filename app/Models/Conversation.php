<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    // Model nhóm trò chuyện
    protected $fillable = ['name', 'is_group', 'created_by'];

    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'conversation_user', 'conversation_id', 'user_id')
    //                 ->withPivot('role', 'invited_by')
    //                 ->withTimestamps();
    // }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function messageLast()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function conversationUsers()
    {
        return $this->hasMany(ConversationUser::class);
    }


}
