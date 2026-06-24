<?php

namespace App\Http\Controllers;

use App\Models\FlashCampaign;
use App\Models\SupportConversation;
use App\Models\UgcSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminEngagementController extends Controller
{
    /**
     * Store one flash campaign from the dashboard.
     */
    public function storeFlashCampaign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:255'],
            'discount_code' => ['nullable', 'string', 'max:40'],
            'cta_label' => ['nullable', 'string', 'max:60'],
            'cta_url' => ['nullable', 'url', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'audience' => ['required', 'string', 'in:all,newsletter,loyalty'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        FlashCampaign::query()->create([
            'title' => trim($validated['title']),
            'message' => trim($validated['message']),
            'discount_code' => trim((string) ($validated['discount_code'] ?? '')) ?: null,
            'cta_label' => trim((string) ($validated['cta_label'] ?? '')) ?: null,
            'cta_url' => trim((string) ($validated['cta_url'] ?? '')) ?: null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'audience' => $validated['audience'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'created_by_user_id' => $request->user()?->id,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('status', 'La campagne flash a ete enregistree.');
    }

    /**
     * Moderate one UGC submission.
     */
    public function updateUgc(Request $request, UgcSubmission $ugcSubmission): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject,feature'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $ugcSubmission->forceFill([
            'admin_notes' => trim((string) ($validated['admin_notes'] ?? '')) ?: null,
            'status' => match ($validated['action']) {
                'reject' => 'rejected',
                default => 'approved',
            },
            'approved_at' => in_array($validated['action'], ['approve', 'feature'], true)
                ? ($ugcSubmission->approved_at ?? now())
                : null,
            'rejected_at' => $validated['action'] === 'reject'
                ? now()
                : null,
            'featured_at' => $validated['action'] === 'feature'
                ? now()
                : ($validated['action'] === 'approve' ? $ugcSubmission->featured_at : null),
        ])->save();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Le statut de la photo client a ete mis a jour.');
    }

    /**
     * Update the status of one support conversation.
     */
    public function updateSupportConversation(Request $request, SupportConversation $supportConversation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,needs-human,resolved'],
        ]);

        $supportConversation->forceFill([
            'status' => $validated['status'],
            'needs_human' => $validated['status'] === 'needs-human',
        ])->save();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Le statut de la conversation support a ete mis a jour.');
    }
}
