<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseOverviewCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_database_overview_command_displays_key_counts(): void
    {
        Product::syncRequiredCatalog();

        User::factory()->admin()->create([
            'email' => 'admin@example.com',
        ]);

        $product = Product::query()->where('slug', 'produit-a')->firstOrFail();

        $product->votes()->create([
            'visitor_hash' => hash('sha256', 'overview-vote'),
        ]);

        $product->preorders()->create([
            'visitor_hash' => hash('sha256', 'overview-preorder'),
        ]);

        SiteSetting::store('whatsapp_number', '2348012345678');

        $this->artisan('catalog:db-overview')
            ->expectsOutput('Catalog database overview')
            ->expectsOutput('Users: 1')
            ->expectsOutput('Admins: 1')
            ->expectsOutput('Products: 8')
            ->expectsOutput('Votes recorded: 1')
            ->expectsOutput('Preorders recorded: 1')
            ->expectsOutput('Reviews: 0')
            ->expectsOutput('Settings: 1')
            ->expectsOutput('WhatsApp: 2348012345678')
            ->assertExitCode(0);
    }
}
