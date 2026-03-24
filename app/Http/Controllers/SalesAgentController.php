<?php

namespace App\Http\Controllers;

use App\Ai\Agents\SalesAgent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Exceptions\RateLimitedException;
use Throwable;

class SalesAgentController extends Controller
{
    private const SESSION_CONVERSATION_KEY = 'sales_agent.conversation_id';
    private const SESSION_GUEST_ID_KEY = 'sales_agent.guest_id';

    public function index()
    {
        $conversationId = session(self::SESSION_CONVERSATION_KEY);
        $history = [];

        if ($conversationId) {
            $history = DB::table('agent_conversation_messages')
                ->select(['role', 'content'])
                ->where('conversation_id', $conversationId)
                ->where('agent', SalesAgent::class)
                ->orderBy('created_at')
                ->limit(100)
                ->get()
                ->map(fn ($message) => [
                    'role' => $message->role === 'user' ? 'user' : 'agent',
                    'text' => (string) $message->content,
                ])
                ->all();
        }

        return view('pages.sales-agent', [
            'conversationId' => $conversationId,
            'history' => $history,
        ]);
    }

    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
            'conversation_id' => ['nullable', 'string', 'max:36'],
        ]);

        $participant = $this->resolveConversationParticipant($request);
        $conversationId = $validated['conversation_id']
            ?? $request->session()->get(self::SESSION_CONVERSATION_KEY);

        $agent = new SalesAgent();

        if ($conversationId) {
            $agent->continue($conversationId, as: $participant);
        } else {
            $agent->forUser($participant);
        }

        try {
            $response = $this->promptWithRetry($agent, $validated['message']);

            if ($response->conversationId) {
                $request->session()->put(self::SESSION_CONVERSATION_KEY, $response->conversationId);
            }

            return response()->json([
                'reply' => trim((string) ($response->text ?? '')),
                'conversation_id' => $response->conversationId,
            ]);
        } catch (RateLimitedException) {
            return response()->json([
                'reply' => 'في ضغط على خدمة الذكاء الاصطناعي حاليا. انتظر ثواني وجرب مرة تانية.',
            ], 429);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'reply' => 'حدث خطأ أثناء معالجة الطلب. حاول مرة أخرى بعد قليل.',
            ], 500);
        }
    }

    public function resetConversation(Request $request): JsonResponse
    {
        $request->session()->forget(self::SESSION_CONVERSATION_KEY);

        return response()->json([
            'ok' => true,
        ]);
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
