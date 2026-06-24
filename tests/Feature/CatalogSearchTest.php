<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_search_matches_accented_queries(): void
    {
        $this->get('/?q=%C3%A9tag%C3%A8re')
            ->assertOk()
            ->assertSee('Resultats du catalogue')
            ->assertSee('2 produit(s) visible(s)')
            ->assertSee('Etagere Murale Atelier')
            ->assertSee('Etagere Colonne Nacre');
    }

    public function test_catalog_search_matches_product_features_and_specifications(): void
    {
        $this->get('/?q=soft close')
            ->assertOk()
            ->assertSee('Resultats du catalogue')
            ->assertSee('1 produit(s) visible(s)')
            ->assertSee('Dressing Loft 6 Portes');
    }
}
