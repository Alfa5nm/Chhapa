<?php

namespace Database\Seeders;

use App\Models\PrintingBusiness;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment('local')) {
            throw new RuntimeException('The demo seeder is only available in the local environment.');
        }

        $password = env('CHHAPA_DEMO_PASSWORD');
        if (!is_string($password) || strlen($password) < 12 || strlen($password) > 72) {
            throw new RuntimeException('Set CHHAPA_DEMO_PASSWORD to a local demo password of 12 to 72 bytes.');
        }

        DB::transaction(function () use ($password): void {
            $ink = PrintingBusiness::firstOrCreate(['slug' => 'ink-and-clay'], ['name' => 'Ink & Clay Printworks']);
            $thread = PrintingBusiness::firstOrCreate(['slug' => 'threadline'], ['name' => 'Threadline Press']);

            $this->demoUser('Ink & Clay Admin', 'ink@example.test', $password, $ink);
            $this->demoUser('Threadline Admin', 'thread@example.test', $password, $thread);
            $this->demoUser('Demo Customer', 'customer@example.test', $password);

            $this->offering($ink, 'MUG-01', 'Custom ceramic mug', 'Mug', 'published',
                'A ceramic mug for artwork, event branding or personalized gifts.', [
                    ['11 oz', 'Ceramic', 'Gloss', 'Sublimation', 12, 500, '210 × 90 mm wrap', '280.00'],
                    ['15 oz', 'Ceramic', 'Matte', 'UV printing', 12, 200, '220 × 95 mm wrap', null],
                ]);
            $this->offering($ink, 'POSTER-01', 'Matte exhibition poster', 'Poster', 'published',
                'A smooth matte poster for exhibits, promotions and indoor displays.', [
                    ['A3', 'Cardstock', 'Matte', 'Digital printing', 10, 1000, '287 × 410 mm', '85.00'],
                ]);
            $this->offering($ink, 'POSTER-02', 'Gallery proof poster', 'Poster', 'draft',
                'A proof offering awaiting internal review.', [
                    ['A2', 'Photo paper', 'Gloss', 'Digital printing', 1, 50, '410 × 584 mm', null],
                ]);
            $this->offering($thread, 'TEE-01', 'Cotton event T-shirt', 'T-shirt', 'published',
                'A cotton tee with front printing for teams and events.', [
                    ['M', 'Cotton', 'Standard', 'Screen printing', 25, 1000, '280 × 350 mm front', '390.00'],
                    ['L', 'Cotton', 'Standard', 'DTG', 5, 150, '280 × 350 mm front', null],
                ]);
        });
    }

    private function demoUser(string $name, string $email, string $password, ?PrintingBusiness $business = null): void
    {
        $user = User::firstOrNew(['email' => $email]);
        if (!$user->exists) {
            $user->fill(['name' => $name, 'password' => $password]);
            $user->managedBusiness()->associate($business);
            $user->save();
        }
    }

    private function offering(PrintingBusiness $business, string $sku, string $name, string $category,
        string $status, string $description, array $variants): void
    {
        $product = $business->products()->firstOrCreate(
            ['sku' => $sku],
            compact('name', 'category', 'status', 'description'),
        );

        if ($product->wasRecentlyCreated) {
            $fields = ['size', 'material', 'finish', 'method', 'min_quantity', 'max_quantity', 'printable_area', 'unit_price'];
            foreach ($variants as $variant) {
                $product->variants()->create(array_combine($fields, $variant));
            }
        }
    }
}
