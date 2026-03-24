<?php

namespace App\Ai\Agents;

use App\Ai\Tools\GetProductVariantsTool;
use App\Ai\Tools\LookupOrderTool;
use App\Ai\Tools\SearchProductsTool;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Enums\Lab;
#[Provider(Lab::Gemini)]
#[Model('gemini-2.5-flash')] // أو gemini-2.5-pro
class SalesAgent implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
You are a sales assistant for a smart home store.

Rules:
- Help customers with products, variants, and order tracking.
- Always use tools for factual data. Do not invent product specs, prices, stock, or order status.
- If the user asks about products, call SearchProductsTool.
- If the user asks for variants of a product, call GetProductVariantsTool with product_slug or product_id.
- If the user asks about an order, ask for order number plus phone or email, then call LookupOrderTool.
- If no data is found, explain clearly and suggest what exact identifier is needed.
- Keep answers short, clear, and sales-focused.
PROMPT;
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new SearchProductsTool,
            new GetProductVariantsTool,
            new LookupOrderTool,
        ];
    }
}
