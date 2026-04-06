<?php

namespace App\Repositories\Frontend;

use Illuminate\Support\Facades\DB;

class SalesAgentRepository
{
    public function getConversationHistory(string $conversationId, string $agentClass, int $limit = 100): array
    {
        return DB::table('agent_conversation_messages')
            ->select(['role', 'content'])
            ->where('conversation_id', $conversationId)
            ->where('agent', $agentClass)
            ->orderBy('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($message) => [
                'role' => $message->role === 'user' ? 'user' : 'agent',
                'text' => (string) $message->content,
            ])
            ->all();
    }
}
