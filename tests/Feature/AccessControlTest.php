<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Models\FinancialRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    // ── Helper ──────────────────────────────────────────────────────────────

    private function userWithToken(RoleEnum $role, StatusEnum $status = StatusEnum::Active): array
    {
        $user = User::factory()->create(['role' => $role, 'status' => $status]);
        $token = $user->createToken('test')->plainTextToken;
        return [$user, $token];
    }

    private function makeRecord(User $owner): FinancialRecord
    {
        return FinancialRecord::factory()->create(['user_id' => $owner->id]);
    }

    // ── Viewer Restrictions ──────────────────────────────────────────────

    public function test_viewer_cannot_create_records(): void
    {
        [, $token] = $this->userWithToken(RoleEnum::Viewer);

        $this->withToken($token)
            ->postJson('/api/records', [
                'amount' => 1000,
                'type' => 'income',
                'category' => 'salary',
                'date' => now()->format('Y-m-d'),
            ])
            ->assertStatus(403);
    }

    public function test_viewer_cannot_update_records(): void
    {
        [$admin] = $this->userWithToken(RoleEnum::Admin);
        [, $token] = $this->userWithToken(RoleEnum::Viewer);
        $record = $this->makeRecord($admin);

        $this->withToken($token)
            ->putJson("/api/records/{$record->id}", ['amount' => 999])
            ->assertStatus(403);
    }

    public function test_viewer_cannot_delete_records(): void
    {
        [$admin] = $this->userWithToken(RoleEnum::Admin);
        [, $token] = $this->userWithToken(RoleEnum::Viewer);
        $record = $this->makeRecord($admin);

        $this->withToken($token)
            ->deleteJson("/api/records/{$record->id}")
            ->assertStatus(403);
    }

    public function test_viewer_can_read_records(): void
    {
        [, $token] = $this->userWithToken(RoleEnum::Viewer);

        $this->withToken($token)
            ->getJson('/api/records')
            ->assertStatus(200);
    }

    public function test_viewer_cannot_access_dashboard(): void
    {
        [, $token] = $this->userWithToken(RoleEnum::Viewer);

        $this->withToken($token)
            ->getJson('/api/dashboard/summary')
            ->assertStatus(403);
    }

    public function test_viewer_cannot_manage_users(): void
    {
        [, $token] = $this->userWithToken(RoleEnum::Viewer);

        $this->withToken($token)
            ->getJson('/api/users')
            ->assertStatus(403);
    }

    // ── Analyst Restrictions ─────────────────────────────────────────────

    public function test_analyst_cannot_update_another_analysts_record(): void
    {
        [$analystOne] = $this->userWithToken(RoleEnum::Analyst);
        [$analystTwo, $tokenTwo] = $this->userWithToken(RoleEnum::Analyst);

        // Record belongs to analystOne
        $record = $this->makeRecord($analystOne);

        // analystTwo tries to update it — should be 403
        $this->withToken($tokenTwo)
            ->putJson("/api/records/{$record->id}", ['amount' => 999])
            ->assertStatus(403);
    }

    public function test_analyst_can_update_their_own_record(): void
    {
        [$analyst, $token] = $this->userWithToken(RoleEnum::Analyst);
        $record = $this->makeRecord($analyst);

        $this->withToken($token)
            ->putJson("/api/records/{$record->id}", ['amount' => 500.00])
            ->assertStatus(200);
    }

    public function test_analyst_cannot_delete_records(): void
    {
        [$analyst, $token] = $this->userWithToken(RoleEnum::Analyst);
        $record = $this->makeRecord($analyst);

        $this->withToken($token)
            ->deleteJson("/api/records/{$record->id}")
            ->assertStatus(403);
    }

    public function test_analyst_can_access_dashboard(): void
    {
        [, $token] = $this->userWithToken(RoleEnum::Analyst);

        $this->withToken($token)
            ->getJson('/api/dashboard/summary')
            ->assertStatus(200);
    }

    public function test_analyst_cannot_manage_users(): void
    {
        [, $token] = $this->userWithToken(RoleEnum::Analyst);

        $this->withToken($token)
            ->getJson('/api/users')
            ->assertStatus(403);
    }

    // ── Admin Permissions ────────────────────────────────────────────────

    public function test_admin_can_update_any_record(): void
    {
        [$analyst] = $this->userWithToken(RoleEnum::Analyst);
        [, $adminToken] = $this->userWithToken(RoleEnum::Admin);
        $record = $this->makeRecord($analyst);

        $this->withToken($adminToken)
            ->putJson("/api/records/{$record->id}", ['amount' => 9999.00])
            ->assertStatus(200);
    }

    public function test_admin_can_delete_records(): void
    {
        [$analyst] = $this->userWithToken(RoleEnum::Analyst);
        [, $adminToken] = $this->userWithToken(RoleEnum::Admin);
        $record = $this->makeRecord($analyst);

        $this->withToken($adminToken)
            ->deleteJson("/api/records/{$record->id}")
            ->assertStatus(200);
    }

    public function test_admin_can_manage_users(): void
    {
        [, $adminToken] = $this->userWithToken(RoleEnum::Admin);

        $this->withToken($adminToken)
            ->getJson('/api/users')
            ->assertStatus(200);
    }

    // ── Inactive User Block ──────────────────────────────────────────────

    /**
     * This is the test most candidates forget.
     * A deactivated user with a still-valid token must be blocked.
     */
    public function test_inactive_user_is_blocked_even_with_valid_token(): void
    {
        [, $token] = $this->userWithToken(RoleEnum::Analyst, StatusEnum::Inactive);

        $this->withToken($token)
            ->getJson('/api/records')
            ->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_inactive_admin_is_also_blocked(): void
    {
        [, $token] = $this->userWithToken(RoleEnum::Admin, StatusEnum::Inactive);

        $this->withToken($token)
            ->getJson('/api/users')
            ->assertStatus(403);
    }

    // ── Response Shape Consistency ───────────────────────────────────────

    public function test_403_response_uses_standard_error_shape(): void
    {
        [, $token] = $this->userWithToken(RoleEnum::Viewer);

        $this->withToken($token)
            ->postJson('/api/records', [])
            ->assertStatus(403)
            ->assertJsonStructure(['success', 'message'])
            ->assertJsonPath('success', false);
    }
}