<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    /** @test */
    public function admin_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertViewHas('overview');
        $response->assertViewHas('products');
        $response->assertViewHas('chartData');
    }

    /** @test */
    public function non_admin_is_redirected_from_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('catalog.index'));
    }

    /** @test */
    public function guest_cannot_view_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirectToRoute('login');
    }

    /** @test */
    public function admin_can_export_products_csv(): void
    {
        Product::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.export.products'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $response->assertHeader('Content-Disposition');
    }

    /** @test */
    public function admin_can_export_orders_csv(): void
    {
        $product = Product::factory()->create();
        ProductOrder::factory()->count(3)->create(['product_id' => $product->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.export.orders'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    /** @test */
    public function admin_can_export_audit_logs_csv(): void
    {
        AuditLog::log(
            action: 'create',
            modelType: 'Product',
            description: 'Test product created'
        );

        $response = $this->actingAs($this->admin)->get(route('admin.export.audit-logs'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    /** @test */
    public function admin_can_view_audit_logs(): void
    {
        AuditLog::log(
            action: 'update',
            modelType: 'Product',
            modelId: 1,
            description: 'Product updated'
        );

        $response = $this->actingAs($this->admin)->get(route('dashboard.audit-logs'));

        $response->assertOk();
        $response->assertViewIs('dashboard-audit-logs');
        $response->assertViewHas('logs');
    }

    /** @test */
    public function audit_logs_can_be_filtered_by_action(): void
    {
        AuditLog::log(action: 'create', modelType: 'Product', description: 'Created');
        AuditLog::log(action: 'update', modelType: 'Product', description: 'Updated');

        $response = $this->actingAs($this->admin)->get(route('dashboard.audit-logs', ['action' => 'update']));

        $response->assertOk();
        $this->assertEquals(1, $response->viewData('logs')->count());
    }

    /** @test */
    public function audit_logs_can_be_searched(): void
    {
        AuditLog::log(action: 'create', modelType: 'Product', description: 'Special product');
        AuditLog::log(action: 'create', modelType: 'Review', description: 'Normal review');

        $response = $this->actingAs($this->admin)->get(route('dashboard.audit-logs', ['search' => 'Special']));

        $response->assertOk();
        $this->assertEquals(1, $response->viewData('logs')->count());
    }

    /** @test */
    public function audit_trail_tracks_admin_actions(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin)->put(
            route('admin.products.update', $product),
            [
                'name' => 'Updated Name',
                'category' => 'commode',
                'preorder_count' => 5,
            ]
        );

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'update',
            'model_type' => 'Product',
            'model_id' => $product->id,
        ]);
    }

    /** @test */
    public function chart_data_is_available_in_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertViewHas('chartData');
        $data = $response->viewData('chartData');

        $this->assertArrayHasKey('orderTrend', $data);
        $this->assertArrayHasKey('topProducts', $data);
        $this->assertArrayHasKey('engagement', $data);
        $this->assertArrayHasKey('categoryDistribution', $data);
        $this->assertArrayHasKey('revenueByProduct', $data);
    }
}
