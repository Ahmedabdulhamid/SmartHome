<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesAgentAskRequest;
use App\Services\Frontend\SalesAgentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Ai\Exceptions\RateLimitedException;
use Throwable;

class SalesAgentController extends Controller
{
    public function __construct(
        private readonly SalesAgentService $salesAgentService,
    ) {}

    public function index(Request $request)
    {
        return view('pages.sales-agent', $this->salesAgentService->getPageData($request));
    }

    public function ask(SalesAgentAskRequest $request): JsonResponse
    {
        try {
            return response()->json($this->salesAgentService->ask($request, $request->validated()));
        } catch (RateLimitedException) {
            return response()->json([
                'reply' => 'The AI service is busy right now. Please wait a few seconds and try again.',
            ], 429);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'reply' => 'An error occurred while processing your request. Please try again shortly.',
            ], 500);
        }
    }

    public function resetConversation(Request $request): JsonResponse
    {
        $this->salesAgentService->resetConversation($request);

        return response()->json([
            'ok' => true,
        ]);
    }
}
