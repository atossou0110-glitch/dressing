<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Support\ProductInteractionTracker;
use App\Support\SupportAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportChatController extends Controller
{
    public function __construct(
        private readonly ProductInteractionTracker $interactionTracker,
        private readonly SupportAssistantService $assistant,
    ) {}

    /**
     * Persist one chat exchange and return the assistant reply.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'customer_name' => ['nullable', 'string', 'max:80'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'source_path' => ['nullable', 'string', 'max:255'],
            'source_product_slug' => ['nullable', 'string', 'max:120'],
        ]);

        $product = Product::query()
            ->where('slug', (string) ($validated['source_product_slug'] ?? ''))
            ->first();
        $visitorHash = $this->interactionTracker->visitorHash($request);
        $conversation = SupportConversation::query()->firstOrCreate(
            ['visitor_hash' => $visitorHash],
            [
                'source_product_id' => $product?->id,
                'customer_name' => trim((string) ($validated['customer_name'] ?? '')) ?: null,
                'customer_email' => trim((string) ($validated['customer_email'] ?? '')) ?: null,
                'source_path' => trim((string) ($validated['source_path'] ?? $request->path())),
                'status' => 'open',
                'last_message_at' => now(),
                'last_user_message_at' => now(),
            ],
        );

        $conversation->forceFill([
            'source_product_id' => $product?->id ?? $conversation->source_product_id,
            'customer_name' => trim((string) ($validated['customer_name'] ?? '')) ?: $conversation->customer_name,
            'customer_email' => trim((string) ($validated['customer_email'] ?? '')) ?: $conversation->customer_email,
            'source_path' => trim((string) ($validated['source_path'] ?? $request->path())),
            'last_message_at' => now(),
            'last_user_message_at' => now(),
        ])->save();

        $userMessage = $conversation->messages()->create([
            'role' => 'user',
            'body' => trim($validated['message']),
        ]);

        $history = $conversation->messages()
            ->latest('id')
            ->limit(6)
            ->get(['role', 'body'])
            ->reverse()
            ->map(fn (SupportMessage $message): array => [
                'role' => $message->role,
                'body' => $message->body,
            ])
            ->values()
            ->all();

        $reply = $this->assistant->reply(trim($validated['message']), $product, $history);

        $assistantMessage = $conversation->messages()->create([
            'role' => 'assistant',
            'body' => $reply['body'],
            'confidence' => $reply['confidence'],
            'meta' => [
                'quick_replies' => $reply['quickReplies'],
            ],
        ]);

        $conversation->forceFill([
            'needs_human' => $reply['needsHuman'],
            'status' => $reply['needsHuman'] ? 'needs-human' : 'open',
            'last_message_at' => now(),
            'last_assistant_message_at' => now(),
        ])->save();

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'status' => $conversation->status,
                'needsHuman' => $conversation->needs_human,
            ],
            'reply' => [
                'id' => $assistantMessage->id,
                'body' => $assistantMessage->body,
                'confidence' => (float) $assistantMessage->confidence,
                'quickReplies' => $reply['quickReplies'],
            ],
            'message' => [
                'id' => $userMessage->id,
                'body' => $userMessage->body,
            ],
        ], 201);
    }
}
