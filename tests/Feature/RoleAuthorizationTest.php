<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get(route('admin.index'))->assertRedirect(route('login'));
    }

    public function test_operators_are_forbidden_from_the_admin_area(): void
    {
        $operator = User::factory()->create();

        $this->actingAs($operator)->get(route('admin.index'))->assertForbidden();
    }

    public function test_admins_can_open_the_admin_area(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.index'))->assertOk();
    }
}
