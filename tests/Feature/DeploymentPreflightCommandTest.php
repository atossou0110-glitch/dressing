<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DeploymentPreflightCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_preflight_passes_in_baseline_mode_with_warnings(): void
    {
        Product::syncRequiredCatalog();

        config([
            'app.env' => 'testing',
            'app.debug' => true,
            'app.url' => 'http://localhost',
            'session.secure' => false,
            'mail.default' => 'log',
            'queue.default' => 'database',
            'cache.default' => 'database',
            'session.driver' => 'database',
        ]);

        SiteSetting::store('whatsapp_number', null);

        $this->artisan('catalog:preflight')
            ->expectsOutput('Catalog deployment preflight')
            ->expectsOutput('Mode: baseline (warnings allowed)')
            ->expectsOutput('Warnings:')
            ->expectsOutputToContain('APP_ENV should be set to production.')
            ->assertExitCode(0);
    }

    public function test_catalog_preflight_fails_in_strict_mode_when_production_requirements_are_missing(): void
    {
        Product::syncRequiredCatalog();

        config([
            'app.env' => 'local',
            'app.debug' => true,
            'app.url' => 'http://localhost',
            'session.secure' => false,
            'mail.default' => 'log',
            'queue.default' => 'sync',
            'cache.default' => 'array',
            'session.driver' => 'array',
        ]);

        SiteSetting::store('whatsapp_number', null);

        $this->artisan('catalog:preflight --production')
            ->expectsOutput('Catalog deployment preflight')
            ->expectsOutput('Mode: production profile (strict)')
            ->expectsOutput('Blocking issues:')
            ->expectsOutputToContain('APP_ENV should be set to production.')
            ->assertExitCode(1);
    }

    public function test_catalog_preflight_passes_in_strict_mode_when_production_profile_is_hardened(): void
    {
        Product::syncRequiredCatalog();

        config([
            'app.env' => 'production',
            'app.debug' => false,
            'app.url' => 'https://dressingue.example.com',
            'session.secure' => true,
            'mail.default' => 'smtp',
            'queue.default' => 'database',
            'cache.default' => 'database',
            'session.driver' => 'database',
        ]);

        SiteSetting::store('whatsapp_number', '2348012345678');

        File::ensureDirectoryExists(public_path('build'));

        if (! File::exists(public_path('build/manifest.json'))) {
            File::put(public_path('build/manifest.json'), '{}');
        }

        $this->artisan('catalog:preflight --production')
            ->expectsOutput('Catalog deployment preflight')
            ->expectsOutput('Mode: production profile (strict)')
            ->expectsOutput('Preflight passed.')
            ->assertExitCode(0);
    }
}
