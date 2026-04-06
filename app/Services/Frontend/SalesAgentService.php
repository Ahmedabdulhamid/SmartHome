<?php

namespace App\Services\Frontend;

use App\Ai\Agents\SalesAgent;
use App\Repositories\Frontend\SalesAgentRepository;
use Illuminate\Http\Request;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Exceptions\RateLimitedException;

class SalesAgentService
{
    public const SESSION_CONVERSATION_KEY = 'sales_agent.conversation_id';
    public const SESSION_GUEST_ID_KEY = 'sales_agent.guest_id';

    public function __construct(
        private readonly SalesAgentRepository $salesAgentRepository,
    ) {}

    public function getPageData(Request $request): array
    {
        $conversationId = $request->session()->get(self::SESSION_CONVERSATION_KEY);
        $history = [];

        if ($conversationId) {
            $history = $this->salesAgentRepository->getConversationHistory($conversationId, SalesAgent::class);
        }

        return [
            'conversationId' => $conversationId,
            'history' => $history,
        ];
    }

    public function ask(Request $request, array $validated): array
    {
        $participant = $this->resolveConversationParticipant($request);
        $conversationId = $validated['conversation_id']
            ?? $request->session()->get(self::SESSION_CONVERSATION_KEY);

        $agent = new SalesAgent();

        if ($conversationId) {
            $agent->continue($conversationId, as: $participant);
        } else {
            $agent->forUser($participant);
        }

        $response = $this->promptWithRetry($agent, $validated['message']);

        if ($response->conversationId) {
            $request->session()->put(self::SESSION_CONVERSATION_KEY, $response->conversationId);
        }

        return [
            'reply' => trim((string) ($response->text ?? '')),
            'conversation_id' => $response->conversationId,
        ];
    }

    public function resetConversation(Request $request): void
    {
        $request->session()->forget(self::SESSION_CONVERSATION_KEY);
    }

    private function promptWithRetry(SalesAgent $agent, string $message, int $maxAttempts = 5)
    {
        $attempt = 0;

        while (true) {
            try {
                $attempt++;

                return $agent->prompt(
                    $message,
                    provider: Lab::Gemini,
                    model: 'gemini-2.5-flash',
                    timeout: 120,
                );
            } catch (RateLimitedException $exception) {
                if ($attempt >= $maxAttempts) {
                    throw $exception;
                }

                sleep(2 ** ($attempt - 1));
            }
        }
    }

    private function resolveConversationParticipant(Request $request): object
    {
        if ($request->user()) {
            return $request->user();
        }

        $guestId = (int) $request->session()->get(self::SESSION_GUEST_ID_KEY, 0);

        if ($guestId <= 0) {
            $guestId = (int) sprintf('%u', crc32($request->session()->getId()));
            $request->session()->put(self::SESSION_GUEST_ID_KEY, $guestId);
        }

        return new class($guestId)
        {
            public function __construct(public int $id) {}
        };
    }
}
