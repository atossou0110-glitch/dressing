<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Support\ProductInteractionTracker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductInteractionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.whatsapp.number', '2348012345678');
    }

    public function test_catalog_and_product_pages_render_successfully(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Commode Ivoire Signature')
            ->assertSee('Connexion')
            ->assertSee('Solutions King')
            ->assertSee('https://wa.me/2348012345678?text=', false)
            ->assertDontSee('237XXXXXXXXXX');

        $this->get('/produit-a')
            ->assertOk()
            ->assertSee('Commode Ivoire Signature')
            ->assertSee('Acheter maintenant')
            ->assertDontSee('Voter pour le produit du mois');

        $this->get('/produit-b')
            ->assertOk()
            ->assertSee('Murale Atelier')
            ->assertSee('Acheter maintenant')
            ->assertDontSee('Voter pour le produit du mois');
    }

    public function test_product_detail_is_publicly_accessible(): void
    {
        $this->get('/produit-a')
            ->assertOk()
            ->assertSee('Commode Ivoire Signature')
            ->assertSee('Laisser un commentaire')
            ->assertDontSee('Voter pour le produit du mois');
    }

    public function test_a_product_can_be_preordered_once_per_visitor_without_vote_prerequisite(): void
    {
        $this->get('/')->assertOk();

        $product = Product::query()->where('slug', 'produit-b')->firstOrFail();
        $expectedUrl = sprintf(
            'https://wa.me/2348012345678?text=%s',
            rawurlencode(sprintf(
                'Bonjour, je souhaite precommander %s sur King Rangement Benin. Voici le produit : %s',
                $product->name,
                route('products.show', $product),
            )),
        );
        $visitorToken = 'visitor-b';

        $this->actingAsVisitor($visitorToken)
            ->postJson(route('products.preorder', $product))
            ->assertOk()
            ->assertJsonPath('product.preorderCount', 19)
            ->assertJsonPath('product.hasPreordered', true)
            ->assertJsonPath('whatsappUrl', $expectedUrl);

        $this->assertDatabaseHas('product_preorders', [
            'product_id' => $product->id,
            'visitor_hash' => $this->visitorHash($visitorToken),
        ]);

        $this->actingAsVisitor($visitorToken)
            ->postJson(route('products.preorder', $product))
            ->assertOk()
            ->assertJsonPath('product.preorderCount', 19)
            ->assertJsonPath('product.hasPreordered', true)
            ->assertJsonPath('whatsappUrl', $expectedUrl);
    }

    public function test_a_review_is_stored_and_metrics_are_returned(): void
    {
        $product = Product::query()->where('slug', 'produit-a')->firstOrFail();

        $this->postJson(route('products.reviews.store', $product), [
            'author_name' => 'Awa',
            'body' => 'Belle finition et rangement pratique.',
            'rating' => 4,
        ])
            ->assertCreated()
            ->assertJsonPath('metrics.reviewCount', 1)
            ->assertJsonPath('metrics.averageRating', 4)
            ->assertJsonPath('review.author_name', 'Awa')
            ->assertJsonPath('review.rating', 4);

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'author_name' => 'Awa',
            'rating' => 4,
        ]);
    }

    public function test_missing_catalog_products_are_restored_automatically(): void
    {
        Product::query()->delete();

        $this->get('/')
            ->assertOk()
            ->assertSee('Commode Ivoire Signature')
            ->assertSee('Murale Atelier');

        $this->assertDatabaseHas('products', [
            'slug' => 'produit-a',
            'code' => 'A',
            'name' => 'Commode Ivoire Signature',
            'vote_count' => 0,
            'preorder_count' => 0,
        ]);

        $this->assertDatabaseHas('products', [
            'slug' => 'produit-b',
            'code' => 'B',
            'vote_count' => 0,
            'preorder_count' => 0,
        ]);
    }

    public function test_preorder_route_restores_a_missing_catalog_product(): void
    {
        Product::query()->delete();

        $this->actingAsVisitor('visitor-restore')
            ->postJson('/products/produit-a/preorder')
            ->assertOk()
            ->assertJsonPath('product.slug', 'produit-a')
            ->assertJsonPath('product.preorderCount', 1)
            ->assertJsonPath('product.hasPreordered', true);

        $this->assertDatabaseHas('products', [
            'slug' => 'produit-a',
            'code' => 'A',
            'name' => 'Commode Ivoire Signature',
            'vote_count' => 0,
            'preorder_count' => 1,
        ]);
    }

    private function actingAsVisitor(string $token = 'visitor-default'): static
    {
        return $this
            ->withCredentials()
            ->withCookie(ProductInteractionTracker::VISITOR_COOKIE_NAME, $token);
    }

    private function visitorHash(string $token): string
    {
        return hash('sha256', $token);
    }
}
