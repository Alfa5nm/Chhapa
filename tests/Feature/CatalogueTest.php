<?php

namespace Tests\Feature;

use App\Models\PrintingBusiness;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class CatalogueTest extends TestCase
{
    use RefreshDatabase;

    private PrintingBusiness $business;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->business = PrintingBusiness::create(['name' => 'Ink & Clay', 'slug' => 'ink-and-clay']);
        $this->admin = $this->makeUser('ink@example.test', $this->business);
    }

    public function test_t01_admin_can_create_a_complete_offering(): void
    {
        $response = $this->actingAs($this->admin)->post(route('workspace.store'), $this->payload());
        $product = $this->business->products()->sole();

        $response->assertRedirect(route('workspace.show', $product));
        $this->assertDatabaseHas('product_variants', ['product_id' => $product->id, 'method' => 'Sublimation']);
        $this->get(route('workspace.index'))->assertOk()->assertSee('Ceramic mug');
    }

    public function test_t02_variant_changes_persist(): void
    {
        $product = $this->makeProduct();
        $data = $this->payload();
        $data['variants'][0]['size'] = '15 oz';
        $this->actingAs($this->admin)->put(route('workspace.update', $product), $data)
            ->assertRedirect(route('workspace.show', $product));
        $this->assertSame('15 oz', $product->variants()->sole()->size);
        $this->get(route('workspace.show', $product))->assertOk()->assertSee('15 oz');
    }

    public function test_t03_draft_is_hidden_from_public_routes(): void
    {
        $product = $this->makeProduct();
        $this->get(route('catalogue.index'))->assertOk()->assertDontSee($product->name);
        $this->get(route('catalogue.show', $product->id))->assertNotFound();
    }

    public function test_t04_publication_unpublication_and_archive(): void
    {
        $product = $this->makeProduct();
        $this->actingAs($this->admin);

        foreach (['published', 'draft', 'archived'] as $status) {
            $this->put(route('workspace.update', $product), $this->payload(['status' => $status]))
                ->assertRedirect(route('workspace.show', $product));
            $this->assertSame($status, $product->fresh()->status);

            $listing = $this->get(route('catalogue.index'))->assertOk();
            $details = $this->get(route('catalogue.show', $product->id));
            if ($status === 'published') {
                $listing->assertSee($product->name);
                $details->assertOk();
            } else {
                $listing->assertDontSee($product->name);
                $details->assertNotFound();
            }
        }
    }

    public function test_t05_zero_and_negative_minimum_are_rejected(): void
    {
        $this->actingAs($this->admin);
        foreach ([0, -1] as $minimum) {
            $data = $this->payload();
            $data['variants'][0]['min_quantity'] = $minimum;
            $this->post(route('workspace.store'), $data)->assertSessionHasErrors('variants.0.min_quantity');
        }
        $this->assertDatabaseCount('products', 0);
    }

    public function test_t06_reversed_quantity_range_is_rejected(): void
    {
        $data = $this->payload();
        $data['variants'][0]['max_quantity'] = 1;
        $this->actingAs($this->admin)->post(route('workspace.store'), $data)
            ->assertSessionHasErrors('variants.0.max_quantity');
        $this->assertDatabaseCount('products', 0);
    }

    public function test_t07_sku_is_unique_per_business(): void
    {
        $product = $this->makeProduct();
        $this->actingAs($this->admin)->post(route('workspace.store'), $this->payload(['sku' => ' mug-01 ']))
            ->assertSessionHasErrors('sku');
        $this->assertSame('MUG-01', $product->fresh()->sku);

        $other = PrintingBusiness::create(['name' => 'Threadline', 'slug' => 'threadline']);
        $this->actingAs($this->makeUser('thread@example.test', $other))
            ->post(route('workspace.store'), $this->payload())->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseCount('products', 2);
    }

    public function test_t08_other_business_cannot_read_or_edit_private_offering(): void
    {
        $product = $this->makeProduct();
        $other = PrintingBusiness::create(['name' => 'Threadline', 'slug' => 'threadline']);
        $this->actingAs($this->makeUser('thread@example.test', $other));
        $this->get(route('workspace.index'))->assertOk()->assertDontSee($product->name);
        $this->get(route('workspace.show', $product))->assertForbidden();
        $this->get(route('workspace.edit', $product))->assertForbidden();
        $this->put(route('workspace.update', $product), $this->payload(['status' => 'published']))->assertForbidden();
        $this->assertSame('draft', $product->fresh()->status);
    }

    public function test_t09_customer_cannot_use_workspace_mutations(): void
    {
        $product = $this->makeProduct();
        $this->post(route('workspace.store'), $this->payload())->assertRedirect(route('login'));
        $this->actingAs($this->makeUser('customer@example.test'));
        $this->post(route('workspace.store'), $this->payload())->assertForbidden();
        $this->put(route('workspace.update', $product), $this->payload())->assertForbidden();
        $this->assertDatabaseCount('products', 1);
    }

    public function test_t10_tampered_option_and_business_id_are_rejected(): void
    {
        $data = $this->payload();
        $data['variants'][0]['material'] = 'Steel';
        $this->actingAs($this->admin)->post(route('workspace.store'), $data)
            ->assertSessionHasErrors('variants.0.method');
        $this->post(route('workspace.store'), $this->payload(['business_id' => 789]))
            ->assertSessionHasErrors('business_id');
        $this->assertDatabaseCount('products', 0);
    }

    public function test_t11_public_detail_shows_actual_provider_and_options(): void
    {
        $product = $this->makeProduct(['status' => 'published']);
        $other = PrintingBusiness::create(['name' => 'Threadline', 'slug' => 'threadline']);
        $other->products()->create([
            'name' => 'Event tee', 'sku' => 'TEE-01', 'description' => 'Event print',
            'status' => 'published', 'category' => 'T-shirt',
        ]);

        $this->get(route('catalogue.show', $product->id))->assertOk()
            ->assertSee('Ink & Clay')->assertSee('Sublimation')->assertSee('Quotation required')
            ->assertDontSee('Threadline')->assertDontSee('MUG-01');
        $this->get(route('catalogue.index', ['provider' => 'threadline', 'category' => 'T-shirt']))
            ->assertOk()->assertSee('Event tee')->assertDontSee($product->name);
    }

    public function test_t12_empty_state_and_invalid_input(): void
    {
        $this->get(route('catalogue.index'))->assertOk()->assertSee('No published offerings');
        $this->actingAs($this->admin)->get(route('workspace.index'))->assertOk()->assertSee('no offerings yet');
        $this->from(route('workspace.create'))
            ->post(route('workspace.store'), $this->payload(['name' => str_repeat('x', 121), 'variants' => 'invalid']))
            ->assertRedirect(route('workspace.create'))->assertSessionHasErrors(['name', 'variants']);
        $this->get(route('workspace.create'))->assertOk();
        $this->assertDatabaseCount('products', 0);
    }

    private function makeUser(string $email, ?PrintingBusiness $business = null): User
    {
        $user = new User(['name' => 'Demo User', 'email' => $email, 'password' => 'local-test-password']);
        $user->managedBusiness()->associate($business);
        $user->save();

        return $user;
    }

    private function makeProduct(array $overrides = []): Product
    {
        $data = $this->payload($overrides);
        $variants = $data['variants'];
        unset($data['variants']);
        $product = $this->business->products()->create($data);
        $product->variants()->createMany($variants);

        return $product;
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Ceramic mug',
            'category' => 'Mug',
            'description' => 'Ceramic mug with wrap printing',
            'sku' => 'MUG-01',
            'status' => 'draft',
            'variants' => [[
                'size' => '11 oz', 'material' => 'Ceramic', 'finish' => 'Gloss',
                'method' => 'Sublimation', 'min_quantity' => 12, 'max_quantity' => 500,
                'printable_area' => '210 × 90 mm', 'unit_price' => null,
            ]],
        ], $overrides);
    }
}
