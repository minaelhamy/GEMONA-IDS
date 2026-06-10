<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiChatHistory extends Model
{
    protected $table = 'ai_chat_histories';

    protected $fillable = [
        'user_id',
        'ai_agent_id',
        'message',
        'response',
    ];

    protected $casts = [
        'id'            => 'integer',
        'user_id'       => 'integer',
        'ai_agent_id'   => 'integer',
        'message'       => 'string',
        'response'      => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
