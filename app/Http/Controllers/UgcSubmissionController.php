<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UgcSubmission;
use App\Support\ProductInteractionTracker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UgcSubmissionController extends Controller
{
    public function __construct(
        private readonly ProductInteractionTracker $interactionTracker,
    ) {}

    /**
     * Store one UGC submission for moderation.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'author_name' => ['required', 'string', 'max:80'],
            'author_city' => ['nullable', 'string', 'max:80'],
            'author_email' => ['nullable', 'email', 'max:255'],
            'caption' => ['required', 'string', 'max:255'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'source_product_slug' => ['nullable', 'string', 'max:120'],
        ]);

        $product = Product::query()
            ->where('slug', (string) ($validated['source_product_slug'] ?? ''))
            ->first();

        $directory = public_path('uploads/ugc');
        File::ensureDirectoryExists($directory);

        $extension = (string) $validated['photo']->getClientOriginalExtension();
        $filename = now()->format('YmdHis').'-'.Str::lower(Str::random(12)).'.'.$extension;
        $validated['photo']->move($directory, $filename);

        UgcSubmission::query()->create([
            'product_id' => $product?->id,
            'author_name' => trim($validated['author_name']),
            'author_city' => trim((string) ($validated['author_city'] ?? '')) ?: null,
            'author_email' => trim((string) ($validated['author_email'] ?? '')) ?: null,
            'caption' => trim($validated['caption']),
            'photo_path' => 'uploads/ugc/'.$filename,
            'status' => 'pending',
            'visitor_hash' => $this->interactionTracker->visitorHash($request),
            'ip_hash' => $request->ip() !== null ? hash('sha256', (string) $request->ip()) : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Merci. Votre photo a bien ete envoyee pour validation.',
        ], 201);
    }
}
