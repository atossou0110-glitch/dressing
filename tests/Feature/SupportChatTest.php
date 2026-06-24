<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Support\ProductInteractionTracker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Product::syncRequiredCatalog();
        config()->set('services.whatsapp.number', '22997000000');
    }

    public function test_support_chat_handles_a_natural_recommendation_request(): void
    {
        $this->actingAsVisitor('visitor-support-reco')
            ->postJson(route('support.chat'), [
                'message' => 'je cherche un meuble pour ma chambre parentale',
                'source_path' => '/dressing',
            ])
            ->assertCreated()
            ->assertJsonPath('reply.quickReplies.0', 'Envoyer mes dimensions')
            ->assertJson(fn ($json) => $json
                ->where('conversation.needsHuman', false)
                ->where('conversation.status', 'open')
                ->where('reply.confidence', 0.86)
                ->etc())
            ->assertSee('Dressing Ivoire Modulable');
    }

    public function test_support_chat_keeps_delivery_context_between_messages(): void
    {
        $visitor = 'visitor-support-delivery';

        $this->actingAsVisitor($visitor)
            ->postJson(route('support.chat'), [
                'message' => 'Quel est le delai de livraison ?',
                'source_path' => '/',
            ])
            ->assertCreated();

        $this->actingAsVisitor($visitor)
            ->postJson(route('support.chat'), [
                'message' => 'Je suis a Cotonou',
                'source_path' => '/',
            ])
            ->assertCreated()
            ->assertSee('Cotonou')
            ->assertSee('delai');
    }

    public function test_support_chat_checks_dimensions_from_a_statement(): void
    {
        $this->actingAsVisitor('visitor-support-dimensions')
            ->postJson(route('support.chat'), [
                'message' => 'mon espace fait 280 x 240 cm',
                'source_product_slug' => 'produit-e',
                'source_path' => '/produit-e',
            ])
            ->assertCreated()
            ->assertSee('peut entrer')
            ->assertSee('280 cm')
            ->assertSee('240 cm');
    }

    public function test_support_chat_handles_budget_objection_on_product_context(): void
    {
        $this->actingAsVisitor('visitor-support-budget')
            ->postJson(route('support.chat'), [
                'message' => 'je trouve ce meuble trop cher',
                'source_product_slug' => 'produit-e',
                'source_path' => '/produit-e',
            ])
            ->assertCreated()
            ->assertSee('899 000 FCFA')
            ->assertSee('plus accessible');
    }

    private function actingAsVisitor(string $token): static
    {
        return $this
            ->withCredentials()
            ->withCookie(ProductInteractionTracker::VISITOR_COOKIE_NAME, $token);
    }
}
