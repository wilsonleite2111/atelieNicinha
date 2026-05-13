<?php

namespace Tests\Feature\Products;

use App\Enums\TeamRole;
use App\Models\Product;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    // --- INDEX (viewAny) ---

    public function test_owner_can_view_products_index(): void
    {
        [$owner, $team] = $this->ownerWithTeam();

        $this->actingAs($owner)
            ->get(route('products.index', $team))
            ->assertOk();
    }

    public function test_admin_can_view_products_index(): void
    {
        [, $team, $admin] = $this->teamWithRoles();

        $this->actingAs($admin)
            ->get(route('products.index', $team))
            ->assertOk();
    }

    public function test_member_is_redirected_from_products_index(): void
    {
        [, $team, , $member] = $this->teamWithRoles();

        $this->actingAs($member)
            ->get(route('products.index', $team))
            ->assertRedirect(route('home'));
    }

    public function test_guest_is_redirected_from_products_index(): void
    {
        $team = Team::factory()->create();

        $this->get(route('products.index', $team))
            ->assertRedirect(route('login'));
    }

    // --- CREATE / STORE ---

    public function test_admin_can_access_create_page(): void
    {
        [, $team, $admin] = $this->teamWithRoles();

        $this->actingAs($admin)
            ->get(route('products.create', $team))
            ->assertOk();
    }

    public function test_member_is_redirected_from_create_page(): void
    {
        [, $team, , $member] = $this->teamWithRoles();

        $this->actingAs($member)
            ->get(route('products.create', $team))
            ->assertRedirect(route('home'));
    }

    public function test_admin_can_create_a_product(): void
    {
        [, $team, $admin] = $this->teamWithRoles();

        $this->actingAs($admin)
            ->post(route('products.store', $team), [
                'name' => 'Produto Teste',
                'price' => '9.99',
            ])
            ->assertRedirect(route('products.index', $team));

        $this->assertDatabaseHas('products', [
            'team_id' => $team->id,
            'name' => 'Produto Teste',
        ]);
    }

    public function test_member_is_redirected_when_creating_product(): void
    {
        [, $team, , $member] = $this->teamWithRoles();

        $this->actingAs($member)
            ->post(route('products.store', $team), [
                'name' => 'Produto Teste',
                'price' => '9.99',
            ])
            ->assertRedirect(route('home'));
    }

    public function test_product_store_requires_name_and_price(): void
    {
        [$owner, $team] = $this->ownerWithTeam();

        $this->actingAs($owner)
            ->post(route('products.store', $team), [])
            ->assertSessionHasErrors(['name', 'price']);
    }

    public function test_price_with_comma_is_accepted_and_normalized(): void
    {
        [$owner, $team] = $this->ownerWithTeam();

        $this->actingAs($owner)
            ->post(route('products.store', $team), [
                'name' => 'Produto Vírgula',
                'price' => '1,00',
            ])
            ->assertRedirect(route('products.index', $team));

        $this->assertDatabaseHas('products', [
            'name' => 'Produto Vírgula',
            'price' => '1.00',
        ]);
    }

    // --- EDIT / UPDATE ---

    public function test_admin_can_access_edit_page(): void
    {
        [, $team, $admin] = $this->teamWithRoles();
        $product = Product::factory()->for($team)->create();

        $this->actingAs($admin)
            ->get(route('products.edit', [$team, $product]))
            ->assertOk();
    }

    public function test_member_is_redirected_from_edit_page(): void
    {
        [, $team, , $member] = $this->teamWithRoles();
        $product = Product::factory()->for($team)->create();

        $this->actingAs($member)
            ->get(route('products.edit', [$team, $product]))
            ->assertRedirect(route('home'));
    }

    public function test_admin_can_update_a_product(): void
    {
        [, $team, $admin] = $this->teamWithRoles();
        $product = Product::factory()->for($team)->create(['name' => 'Nome Antigo']);

        $this->actingAs($admin)
            ->patch(route('products.update', [$team, $product]), [
                'name' => 'Nome Novo',
                'price' => '19.99',
            ])
            ->assertRedirect(route('products.index', $team));

        $this->assertEquals('Nome Novo', $product->fresh()->name);
    }

    public function test_member_is_redirected_when_updating_product(): void
    {
        [, $team, , $member] = $this->teamWithRoles();
        $product = Product::factory()->for($team)->create();

        $this->actingAs($member)
            ->patch(route('products.update', [$team, $product]), [
                'name' => 'Nome Hackeado',
                'price' => '0.01',
            ])
            ->assertRedirect(route('home'));
    }

    // --- DESTROY ---

    public function test_owner_can_delete_a_product(): void
    {
        [$owner, $team] = $this->ownerWithTeam();
        $product = Product::factory()->for($team)->create();

        $this->actingAs($owner)
            ->delete(route('products.destroy', [$team, $product]))
            ->assertRedirect(route('products.index', $team));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_delete_a_product(): void
    {
        [, $team, $admin] = $this->teamWithRoles();
        $product = Product::factory()->for($team)->create();

        $this->actingAs($admin)
            ->delete(route('products.destroy', [$team, $product]))
            ->assertRedirect(route('products.index', $team));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_member_is_redirected_when_deleting_product(): void
    {
        [, $team, , $member] = $this->teamWithRoles();
        $product = Product::factory()->for($team)->create();

        $this->actingAs($member)
            ->delete(route('products.destroy', [$team, $product]))
            ->assertRedirect(route('home'));
    }

    // --- IMAGES ---

    public function test_admin_can_upload_images_when_creating_product(): void
    {
        Storage::fake('public');
        [, $team, $admin] = $this->teamWithRoles();

        $this->actingAs($admin)
            ->post(route('products.store', $team), [
                'name' => 'Produto com Imagem',
                'price' => '49.90',
                'images' => [UploadedFile::fake()->image('photo.jpg')],
            ])
            ->assertRedirect(route('products.index', $team));

        $product = Product::where('name', 'Produto com Imagem')->firstOrFail();
        $this->assertCount(1, $product->getMedia('images'));
    }

    public function test_admin_can_delete_image_when_updating_product(): void
    {
        Storage::fake('public');
        [, $team, $admin] = $this->teamWithRoles();
        $product = Product::factory()->for($team)->create();
        $media = $product->addMedia(UploadedFile::fake()->image('old.jpg'))->toMediaCollection('images');

        $this->actingAs($admin)
            ->patch(route('products.update', [$team, $product]), [
                'name' => $product->name,
                'price' => $product->price,
                'delete_images' => [$media->id],
            ])
            ->assertRedirect(route('products.index', $team));

        $this->assertCount(0, $product->fresh()->getMedia('images'));
    }

    public function test_image_upload_rejects_non_image_file(): void
    {
        [, $team, $admin] = $this->teamWithRoles();

        $this->actingAs($admin)
            ->post(route('products.store', $team), [
                'name' => 'Produto',
                'price' => '9.99',
                'images' => [UploadedFile::fake()->create('document.pdf', 100, 'application/pdf')],
            ])
            ->assertSessionHasErrors('images.0');
    }

    // --- CROSS-TEAM ISOLATION ---

    public function test_admin_cannot_edit_another_teams_product(): void
    {
        [, $team, $admin] = $this->teamWithRoles();

        $otherTeam = Team::factory()->create();
        $foreignProduct = Product::factory()->for($otherTeam)->create();

        $this->actingAs($admin)
            ->patch(route('products.update', [$team, $foreignProduct]), [
                'name' => 'Produto Roubado',
                'price' => '1.00',
            ])
            ->assertNotFound();
    }

    // --- HELPERS ---

    /**
     * @return array{User, Team}
     */
    private function ownerWithTeam(): array
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $owner->update(['current_team_id' => $team->id]);

        return [$owner, $team];
    }

    /**
     * @return array{User, Team, User, User}
     */
    private function teamWithRoles(): array
    {
        [$owner, $team] = $this->ownerWithTeam();

        $admin = User::factory()->create();
        $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);

        $member = User::factory()->create();
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);

        return [$owner, $team, $admin, $member];
    }
}
