<?php

namespace Tests\Unit;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function audit_log_can_be_created(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        AuditLog::log(
            action: 'create',
            modelType: 'Product',
            modelId: 1,
            description: 'New product created'
        );

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'create',
            'model_type' => 'Product',
            'model_id' => 1,
            'description' => 'New product created',
        ]);
    }

    /** @test */
    public function audit_log_stores_ip_address(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        AuditLog::log(
            action: 'update',
            modelType: 'Product',
            description: 'Updated'
        );

        $log = AuditLog::latest()->first();
        $this->assertNotNull($log->ip_address);
    }

    /** @test */
    public function audit_log_stores_user_agent(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        AuditLog::log(
            action: 'delete',
            modelType: 'Review',
            description: 'Review deleted'
        );

        $log = AuditLog::latest()->first();
        $this->assertNotNull($log->user_agent);
    }

    /** @test */
    public function audit_logs_can_be_queried_by_action(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        AuditLog::log(action: 'create', modelType: 'Product');
        AuditLog::log(action: 'update', modelType: 'Product');
        AuditLog::log(action: 'create', modelType: 'Review');

        $creates = AuditLog::where('action', 'create')->count();
        $updates = AuditLog::where('action', 'update')->count();

        $this->assertEquals(2, $creates);
        $this->assertEquals(1, $updates);
    }

    /** @test */
    public function audit_logs_can_be_queried_by_model_type(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        AuditLog::log(action: 'create', modelType: 'Product');
        AuditLog::log(action: 'create', modelType: 'Product');
        AuditLog::log(action: 'create', modelType: 'Review');

        $productLogs = AuditLog::where('model_type', 'Product')->count();
        $reviewLogs = AuditLog::where('model_type', 'Review')->count();

        $this->assertEquals(2, $productLogs);
        $this->assertEquals(1, $reviewLogs);
    }

    /** @test */
    public function audit_log_with_old_and_new_values(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        AuditLog::log(
            action: 'update',
            modelType: 'Product',
            modelId: 5,
            oldValues: ['name' => 'Old Name', 'price' => 1000],
            newValues: ['name' => 'New Name', 'price' => 1500],
            description: 'Product details updated'
        );

        $log = AuditLog::latest()->first();
        $this->assertEquals('Old Name', $log->old_values['name']);
        $this->assertEquals('New Name', $log->new_values['name']);
    }
}
