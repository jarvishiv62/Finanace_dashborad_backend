<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\FinancialRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsRole(RoleEnum $role): string
    {
        $user = User::factory()->create(['role' => $role]);
        return $user->createToken('test')->plainTextToken;
    }

    public function test_summary_returns_correct_structure(): void
    {
        $token = $this->actingAsRole(RoleEnum::Admin);

        $this->withToken($token)
            ->getJson('/api/dashboard/summary')
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'total_income',
                    'total_expenses',
                    'net_balance',
                    'total_records',
                    'this_month_income',
                    'this_month_expenses',
                ],
            ]);
    }

    public function test_summary_net_balance_is_income_minus_expenses(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::Admin]);
        $token = $admin->createToken('test')->plainTextToken;

        FinancialRecord::factory()->create([
            'user_id' => $admin->id,
            'type' => 'income',
            'amount' => 1000.00,
            'date' => now()->format('Y-m-d'),
        ]);

        FinancialRecord::factory()->create([
            'user_id' => $admin->id,
            'type' => 'expense',
            'amount' => 400.00,
            'date' => now()->format('Y-m-d'),
        ]);

        $response = $this->withToken($token)
            ->getJson('/api/dashboard/summary')
            ->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals(1000.00, $data['total_income']);
        $this->assertEquals(400.00, $data['total_expenses']);
        $this->assertEquals(600.00, $data['net_balance']);
    }

    public function test_trends_returns_monthly_data(): void
    {
        $token = $this->actingAsRole(RoleEnum::Admin);

        $this->withToken($token)
            ->getJson('/api/dashboard/trends?months=6')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['month', 'income', 'expense'],
                ],
            ]);
    }

    public function test_categories_returns_breakdown(): void
    {
        $token = $this->actingAsRole(RoleEnum::Admin);

        $this->withToken($token)
            ->getJson('/api/dashboard/categories')
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_recent_returns_last_ten_records(): void
    {
        $token = $this->actingAsRole(RoleEnum::Admin);

        $this->withToken($token)
            ->getJson('/api/dashboard/recent')
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_viewer_cannot_access_any_dashboard_endpoint(): void
    {
        $token = $this->actingAsRole(RoleEnum::Viewer);

        $this->withToken($token)->getJson('/api/dashboard/summary')->assertStatus(403);
        $this->withToken($token)->getJson('/api/dashboard/trends')->assertStatus(403);
        $this->withToken($token)->getJson('/api/dashboard/categories')->assertStatus(403);
        $this->withToken($token)->getJson('/api/dashboard/recent')->assertStatus(403);
    }
}