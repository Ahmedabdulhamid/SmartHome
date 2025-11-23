<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $guarded = [
        'id'
    ];
      protected $casts = [
        'seen_by_user' => 'boolean',
    ];
    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }
    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
