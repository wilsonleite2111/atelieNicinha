<?php

namespace Tests\Feature\Products;

use App\Enums\TeamRole;
use App\Models\Product;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_active_product(): void
    {
        $team = Team::factory()->create();
        $product = Product::factory()->for($team)->create(['active' => true]);

        $this->get(route('products.public.show', $product))
            ->assertOk();
    }

    public function test_guest_cannot_view_inactive_product(): void
    {
        $team = Team::factory()->create();
        $product = Product::factory()->for($team)->create(['active' => false]);

        $this->get(route('products.public.show', $product))
            ->assertNotFound();
    }

    public function test_authenticated_user_can_view_active_product(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $product = Product::factory()->for($team)->create(['active' => true]);

        $this->actingAs($user)
            ->get(route('products.public.show', $product))
            ->assertOk();
    }

    public function test_product_size_is_stored_and_returned(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->update(['current_team_id' => $team->id]);

        $this->actingAs($owner)
            ->post(route('products.store', $team), [
                'name' => 'Produto com Tamanho',
                'size' => 'M',
                'price' => '49.90',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('products', [
            'name' => 'Produto com Tamanho',
            'size' => 'M',
        ]);
    }
}
