<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $guarded = [
        'id'
    ];
     protected $casts = [
        'last_message_at' => 'datetime',
    ];
      public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('created_at', 'asc');

    }
   public function assignedUser()
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }
}
