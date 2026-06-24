<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class OperationsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Artisan::call('up');
        File::deleteDirectory(storage_path('framework/testing-ops-backup'));

        parent::tearDown();
    }

    public function test_backup_command_creates_a_snapshot_file(): void
    {
        $target = storage_path('framework/testing-ops-backup');

        File::deleteDirectory($target);

        $this->artisan('catalog:ops:backup-db', [
            '--target' => $target,
        ])
            ->expectsOutput('Backup termine.')
            ->assertExitCode(0);

        $files = File::files($target);

        $this->assertNotEmpty($files);
        $this->assertTrue(collect($files)->contains(
            fn (\SplFileInfo $file) => str_ends_with($file->getFilename(), '.json'),
        ));
    }

    public function test_maintenance_command_toggles_site_availability(): void
    {
        $this->artisan('catalog:ops:maintenance on')
            ->expectsOutput('Mode maintenance active.')
            ->assertExitCode(0);

        $this->get('/')
            ->assertStatus(503)
            ->assertSee('mise a jour est en cours');

        $this->artisan('catalog:ops:maintenance off')
            ->expectsOutput('Mode maintenance desactive.')
            ->assertExitCode(0);

        $this->get('/')
            ->assertOk();
    }
}
