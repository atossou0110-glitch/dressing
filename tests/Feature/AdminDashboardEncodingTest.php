<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardEncodingTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_handles_unicode_characters_correctly(): void
    {
        $admin = User::factory()->admin()->create();
        $productA = Product::query()->where('slug', 'produit-a')->firstOrFail();

        $productA->reviews()->create([
            'author_name' => 'Francois Muller',
            'body' => 'Tres beau meuble. Bonne qualite a recommander!',
            'rating' => 5,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('dashboard'));

        $response->assertOk();

        $content = $response->getContent();
        $this->assertStringContainsString('Francois Muller', $content);
        $this->assertStringContainsString('Tres beau meuble. Bonne qualite a recommander!', $content);
        $this->assertFalse(strpos($content, 'ÃƒÂ©') !== false);
    }

    public function test_dashboard_product_update_preserves_encoding(): void
    {
        $admin = User::factory()->admin()->create();
        $productA = Product::query()->where('slug', 'produit-a')->firstOrFail();

        $this->actingAs($admin)
            ->from(route('dashboard.products'))
            ->put(route('admin.products.update', $productA), [
                'name' => 'Commode Elegante',
                'preorder_count' => 0,
                'home_badge' => 'Edition speciale',
                'home_description' => 'Mobilier de qualite superieure',
                'home_price' => 'EUR 1 200',
                'home_highlight' => 'Livraison gratuite',
                'detail_subtitle' => 'Meuble haut de gamme',
                'detail_badge' => 'Createur depuis 1995',
                'detail_description' => "Un chef-d'oeuvre de l'architecture interieure",
                'features_text' => "Resistance exceptionnelle\nDesign intemporel",
                'specifications_text' => "Materiaux: Bois massif de qualite\nDimensions: 240 x 180 x 50 cm",
            ])
            ->assertRedirect(route('dashboard.products'));

        $productA->refresh();

        $this->assertSame('Commode Elegante', $productA->name);

        $content = $productA->resolvedContent();
        $this->assertSame('Edition speciale', $content['home_badge']);
        $this->assertSame('Meuble haut de gamme', $content['detail_subtitle']);
        $this->assertStringContainsString("chef-d'oeuvre", $content['detail_description']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.products'));

        $response->assertOk();
        $response->assertSee('Commode Elegante');
        $response->assertSee('Edition speciale');

        $pageContent = $response->getContent();
        $this->assertFalse(strpos($pageContent, 'ÃƒÂ©') !== false);
    }

    public function test_admin_export_csv_handles_unicode_correctly(): void
    {
        $admin = User::factory()->admin()->create();
        $productA = Product::query()->where('slug', 'produit-a')->firstOrFail();

        $productA->update(['name' => 'Commode Francaise']);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.study.export', ['days' => 7]));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('Commode Francaise', $content);
        $this->assertFalse(strpos($content, 'ÃƒÂ©') !== false);
    }
}
