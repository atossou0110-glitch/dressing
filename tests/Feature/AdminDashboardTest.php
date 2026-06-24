<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\ProductInteractionTracker;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.whatsapp.number', '2348012345678');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('catalog-media/testing'));

        parent::tearDown();
    }

    public function test_admin_dashboard_displays_management_sections(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Gerer les produits')
            ->assertSee('WhatsApp')
            ->assertSee('Avis recents')
            ->assertSee("Rapport d'etude", false);
    }

    public function test_admin_can_export_study_report_csv(): void
    {
        $admin = User::factory()->admin()->create();
        $productA = Product::query()->where('slug', 'produit-a')->firstOrFail();
        $now = CarbonImmutable::now((string) config('app.timezone'));

        DB::table('product_preorders')->insert([
            'product_id' => $productA->id,
            'visitor_hash' => hash('sha256', 'export-preorder-a'),
            'created_at' => $now->subDay()->setTimezone('UTC'),
            'updated_at' => $now->subDay()->setTimezone('UTC'),
        ]);

        DB::table('product_reviews')->insert([
            'product_id' => $productA->id,
            'author_name' => 'Export test',
            'body' => 'Bon produit.',
            'rating' => 5,
            'created_at' => $now->subDay()->setTimezone('UTC'),
            'updated_at' => $now->subDay()->setTimezone('UTC'),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.study.export', ['days' => 14]));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        $this->assertStringContainsString('date,preorders,reviews,preorders_a,preorders_b', $content);
        $this->assertStringContainsString($now->subDay()->format('Y-m-d'), $content);
        $this->assertStringContainsString('timezone,'.config('app.timezone'), $content);
        $this->assertStringContainsString('tendance_precommandes_7j', $content);
        $this->assertStringContainsString('product_code,product_name,preorders,reviews,preorder_share_percent', $content);
    }

    public function test_admin_can_change_study_report_period_and_it_is_kept_in_session(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard', ['study_days' => 90]))
            ->assertOk()
            ->assertSee('Analyse des 90 derniers jours pour suivre');

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Analyse des 90 derniers jours pour suivre');
    }

    public function test_admin_can_update_a_product_from_the_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::query()->where('slug', 'produit-a')->firstOrFail();

        $this->actingAs($admin)
            ->from(route('dashboard.products'))
            ->put(route('admin.products.update', $product), $this->productUpdatePayload($product, [
                'name' => 'Commode Signature',
                'preorder_count' => 3,
                'home_badge' => 'Edition limitee',
                'home_description' => 'Texte accueil modifie depuis le dashboard.',
                'home_price' => 'EUR 950',
                'home_highlight' => 'Montage offert',
                'detail_subtitle' => 'Sous titre ajuste depuis le dashboard.',
                'detail_badge' => 'Edition limitee - EUR 950',
                'detail_description' => 'Description longue modifiee depuis le dashboard.',
                'features_text' => "Penderie ajustable\nFinition noire premium",
                'specifications_text' => "Dimensions : 240 x 190 x 60 cm\nMateriaux : bois renforce",
            ]))
            ->assertRedirect(route('dashboard.products'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Commode Signature',
            'preorder_count' => 3,
        ]);

        $product->refresh();

        $this->assertSame('Edition limitee', $product->resolvedContent()['home_badge']);
        $this->assertSame('Description longue modifiee depuis le dashboard.', $product->resolvedContent()['detail_description']);

        $this->actingAs($admin)
            ->get(route('dashboard.products'))
            ->assertSee('Commode Signature');

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Edition limitee')
            ->assertSee('Texte accueil modifie depuis le dashboard.')
            ->assertSee('EUR 950');

        $this->get(route('products.show.a'))
            ->assertOk()
            ->assertSee('Sous titre ajuste depuis le dashboard.')
            ->assertSee('Description longue modifiee depuis le dashboard.')
            ->assertSee('Penderie ajustable')
            ->assertSee('Dimensions : 240 x 190 x 60 cm');
    }

    public function test_old_input_is_scoped_to_the_product_form_that_failed_validation(): void
    {
        $admin = User::factory()->admin()->create();
        $productA = Product::query()->where('slug', 'produit-a')->firstOrFail();
        $productB = Product::query()->where('slug', 'produit-b')->firstOrFail();

        $response = $this->actingAs($admin)
            ->from(route('dashboard.products'))
            ->put(route('admin.products.update', $productA), array_merge(
                $this->productUpdatePayload($productA, [
                    'name' => '',
                    'home_badge' => 'Badge temporaire A',
                ]),
                ['form_product_id' => $productA->id],
            ));

        $response
            ->assertRedirect(route('dashboard.products'))
            ->assertSessionHasErrors(['name']);

        $page = $this->actingAs($admin)
            ->get(route('dashboard.products'));

        $page->assertOk();
        $this->assertSame(1, substr_count($page->getContent(), 'value="Badge temporaire A"'));
        $this->assertStringContainsString('value="'.$productB->resolvedContent()['home_badge'].'"', $page->getContent());
    }

    public function test_admin_can_reset_product_counters_from_the_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::query()->where('slug', 'produit-b')->firstOrFail();

        $product->votes()->create([
            'visitor_hash' => hash('sha256', 'reset-vote'),
        ]);

        $product->preorders()->create([
            'visitor_hash' => hash('sha256', 'reset-preorder'),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.reset', $product))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'vote_count' => 0,
            'preorder_count' => 0,
        ]);

        $this->assertDatabaseCount('product_votes', 0);
        $this->assertDatabaseCount('product_preorders', 0);
    }

    public function test_setting_product_counters_to_zero_clears_previous_preorders(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::query()->where('slug', 'produit-a')->firstOrFail();
        $visitorToken = 'manual-zero-reset';

        $this->withCredentials()
            ->withCookie(ProductInteractionTracker::VISITOR_COOKIE_NAME, $visitorToken)
            ->postJson(route('products.preorder', $product->fresh()))
            ->assertOk();

        $this->actingAs($admin)
            ->from(route('dashboard.products'))
            ->put(route('admin.products.update', $product->fresh()), $this->productUpdatePayload($product->fresh(), [
                'preorder_count' => 0,
            ]))
            ->assertRedirect(route('dashboard.products'));

        $this->assertDatabaseMissing('product_preorders', [
            'product_id' => $product->id,
            'visitor_hash' => hash('sha256', $visitorToken),
        ]);

        $this->withCookie(ProductInteractionTracker::VISITOR_COOKIE_NAME, $visitorToken)
            ->get(route('products.show.a'))
            ->assertOk()
            ->assertDontSee('Voter pour le produit du mois');

        $this->withCredentials()
            ->withCookie(ProductInteractionTracker::VISITOR_COOKIE_NAME, $visitorToken)
            ->postJson(route('products.preorder', $product->fresh()))
            ->assertOk()
            ->assertJsonPath('product.hasPreordered', true);
    }

    public function test_admin_can_create_a_new_product_from_the_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'slug' => 'produit-i',
                'code' => 'I',
                'name' => 'Bibliotheque Capsule',
                'category' => 'etagere',
                'home_badge' => 'Nouveau module',
                'home_description' => 'Une etagere compacte pour les petits espaces.',
                'home_price' => '219 000 FCFA',
                'home_highlight' => 'Format gain de place',
                'detail_subtitle' => 'Un module simple a integrer.',
                'detail_badge' => 'Capsule 219 000 FCFA',
                'detail_description' => 'Produit cree depuis le dashboard de pilotage.',
                'features_text' => "Compacte\nStable",
                'specifications_text' => "Dimensions : 80 x 180 x 32 cm\nMateriaux : panneaux renforces",
            ])
            ->assertRedirect(route('dashboard.products'));

        $this->assertDatabaseHas('products', [
            'slug' => 'produit-i',
            'code' => 'I',
            'name' => 'Bibliotheque Capsule',
            'category' => 'etagere',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard.products'))
            ->assertOk()
            ->assertSee('Bibliotheque Capsule')
            ->assertSee('Nouveau module');
    }

    public function test_admin_can_update_whatsapp_setting_and_delete_a_review(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::query()->where('slug', 'produit-a')->firstOrFail();
        $review = $product->reviews()->create([
            'author_name' => 'Amina',
            'body' => 'Tres beau meuble.',
            'rating' => 5,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.settings.whatsapp'), [
                'whatsapp_number' => '+234 801 234 5678',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('site_settings', [
            'key' => 'whatsapp_number',
            'value' => '2348012345678',
        ]);

        $this->assertSame('2348012345678', SiteSetting::value('whatsapp_number'));

        $this->actingAs($admin)
            ->delete(route('admin.reviews.destroy', $review))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('product_reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_admin_can_upload_product_images_from_the_dashboard(): void
    {
        config()->set('catalog.products.produit-b.directory', 'catalog-media/testing/produit-b');

        $admin = User::factory()->admin()->create();
        $product = Product::query()->where('slug', 'produit-b')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.products.images.store', $product), [
                'images' => [
                    $this->realImageUpload('Copilot_20260408_111930.png'),
                    $this->realImageUpload('Copilot_20260408_111936.png'),
                ],
            ])
            ->assertRedirect(route('dashboard'));

        $files = File::files(base_path('catalog-media/testing/produit-b'));

        $this->assertCount(2, $files);

        $catalog = $this->get(route('catalog.index'))
            ->assertOk();

        $catalogContent = $catalog->getContent();
        $this->assertTrue(
            collect($files)->contains(fn ($file) => str_contains($catalogContent, $file->getFilename())),
            'Expected at least one uploaded filename to appear in the catalog HTML.',
        );
    }

    public function test_admin_can_delete_a_managed_product_image(): void
    {
        config()->set('catalog.products.produit-b.directory', 'catalog-media/testing/produit-b');

        $admin = User::factory()->admin()->create();
        $product = Product::query()->where('slug', 'produit-b')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.products.images.store', $product), [
                'images' => [
                    $this->realImageUpload('Copilot_20260408_112047.png'),
                ],
            ])
            ->assertRedirect(route('dashboard'));

        $file = File::files(base_path('catalog-media/testing/produit-b'))[0];

        $this->actingAs($admin)
            ->delete(route('admin.products.images.destroy', $product), [
                'filename' => $file->getFilename(),
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertFileDoesNotExist($file->getPathname());
    }

    public function test_non_admin_users_are_redirected_away_from_dashboard_management_routes(): void
    {
        $user = User::factory()->create();
        $product = Product::query()->where('slug', 'produit-a')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.products.update', $product), [
                'name' => 'Blocage',
                'preorder_count' => 1,
            ])
            ->assertRedirect(route('catalog.index'));
    }

    private function realImageUpload(string $filename): UploadedFile
    {
        $directory = base_path('catalog-media/testing/uploads');

        File::ensureDirectoryExists($directory);

        $path = $directory.DIRECTORY_SEPARATOR.$filename;

        File::put(
            $path,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9p8N8WQAAAAASUVORK5CYII='),
        );

        return new UploadedFile(
            $path,
            $filename,
            'image/png',
            null,
            true,
        );
    }

    /**
     * Build a valid product update payload for dashboard forms.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function productUpdatePayload(Product $product, array $overrides = []): array
    {
        $content = $product->resolvedContent();

        return array_merge([
            'name' => $product->name,
            'preorder_count' => (int) $product->preorder_count,
            'home_badge' => $content['home_badge'],
            'home_description' => $content['home_description'],
            'home_price' => $content['home_price'],
            'home_highlight' => $content['home_highlight'],
            'detail_subtitle' => $content['detail_subtitle'],
            'detail_badge' => $content['detail_badge'],
            'detail_description' => $content['detail_description'],
            'features_text' => implode("\n", $content['features']),
            'specifications_text' => implode("\n", $content['specifications']),
        ], $overrides);
    }
}
