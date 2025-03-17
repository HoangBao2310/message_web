<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model
{
    use HasFactory;

    // Model yêu cầu kết bạn
    protected $fillable = ['sender_id', 'receiver_id', 'status'];


    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id'); // Người gửi yêu cầu
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id'); // Người nhận yêu cầu
    }
}
