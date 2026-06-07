<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_review_is_published_immediately_with_photos_and_visible_publicly(): void
    {
        Storage::fake('public');

        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'review-customer@example.com',
        ]);

        $car = Car::create([
            'kode_unit' => 'MM-HRV-024-524',
            'merk' => 'Honda',
            'tipe' => 'HRV E 1.5 CVT',
            'tahun' => 2024,
            'harga' => 338000000,
            'kilometer' => 14550,
            'status' => 'sold',
            'photos' => [],
        ]);

        Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'paid',
            'total' => 338000000,
            'payment_method' => 'transfer',
            'transaction_channel' => 'online',
            'sales_flow' => 'direct_purchase',
        ]);

        $response = $this
            ->actingAs($customer)
            ->post(route('customer.reviews.store'), [
                'car_id' => $car->id,
                'rating' => 5,
                'review_text' => 'Mobil datang bersih, pelayanannya jelas, dan proses beli terasa nyaman sekali.',
                'media' => [
                    UploadedFile::fake()->image('review-1.jpg'),
                    UploadedFile::fake()->image('review-2.jpg'),
                ],
            ]);

        $response
            ->assertRedirect(route('reviews.page', ['car' => $car->id]))
            ->assertSessionHas('success');

        $review = ProductReview::query()->firstOrFail();

        $this->assertSame('approved', $review->status);
        $this->assertCount(2, $review->media_paths ?? []);
        $this->assertNotNull($review->verified_at);

        foreach ($review->media_paths as $path) {
            Storage::disk('public')->assertExists($path);
        }

        $this->get(route('reviews.page'))
            ->assertOk()
            ->assertSee('Mobil datang bersih, pelayanannya jelas, dan proses beli terasa nyaman sekali.')
            ->assertSee($customer->name);

        $this->post(route('logout'))->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee($customer->name)
            ->assertSee('Apa kata Customer Maharani?');
    }

    public function test_customer_review_photo_upload_is_limited_to_five_images(): void
    {
        Storage::fake('public');

        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'review-limit@example.com',
        ]);

        $car = Car::create([
            'kode_unit' => 'MM-CRV-025-525',
            'merk' => 'Honda',
            'tipe' => 'CRV Prestige',
            'tahun' => 2025,
            'harga' => 410000000,
            'kilometer' => 2500,
            'status' => 'sold',
            'photos' => [],
        ]);

        Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'paid',
            'total' => 410000000,
            'payment_method' => 'transfer',
            'transaction_channel' => 'online',
            'sales_flow' => 'direct_purchase',
        ]);

        $files = collect(range(1, 6))
            ->map(fn (int $index) => UploadedFile::fake()->image("review-{$index}.jpg"))
            ->all();

        $response = $this
            ->actingAs($customer)
            ->from(route('customer.reviews.create'))
            ->post(route('customer.reviews.store'), [
                'car_id' => $car->id,
                'rating' => 5,
                'review_text' => 'Review ini harus gagal karena foto yang diunggah melebihi batas maksimal yang diizinkan sistem.',
                'media' => $files,
            ]);

        $response
            ->assertRedirect(route('customer.reviews.create'))
            ->assertSessionHasErrors('media');

        $this->assertDatabaseCount('product_reviews', 0);
    }
}
