<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CarPhotoManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_delete_single_photo_when_more_than_five_photos_exist(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
        ]);

        $car = Car::create([
            'kode_unit' => 'PHOTO-001',
            'merk' => 'Toyota',
            'tipe' => 'Raize',
            'tahun' => 2023,
            'harga' => 240000000,
            'kilometer' => 1000,
            'status' => 'available',
            'photos' => [
                'cars/1/a.jpg',
                'cars/1/b.jpg',
                'cars/1/c.jpg',
                'cars/1/d.jpg',
                'cars/1/e.jpg',
                'cars/1/f.jpg',
            ],
            'created_by' => $supervisor->id,
        ]);

        $this->actingAs($supervisor)
            ->delete(route('supervisor.cars.photos.destroy', [$car, 0]))
            ->assertRedirect();

        $this->assertCount(5, $car->fresh()->photos ?? []);
    }

    public function test_marketing_cannot_delete_photo_if_only_five_photos_remain(): void
    {
        $marketing = User::factory()->create([
            'role' => 'marketing',
        ]);

        $car = Car::create([
            'kode_unit' => 'PHOTO-002',
            'merk' => 'Honda',
            'tipe' => 'Brio',
            'tahun' => 2022,
            'harga' => 185000000,
            'kilometer' => 1200,
            'status' => 'available',
            'photos' => [
                'cars/2/a.jpg',
                'cars/2/b.jpg',
                'cars/2/c.jpg',
                'cars/2/d.jpg',
                'cars/2/e.jpg',
            ],
            'created_by' => $marketing->id,
        ]);

        $this->actingAs($marketing)
            ->delete(route('marketing.products.photos.destroy', [$car, 0]))
            ->assertRedirect(route('marketing.products.index'));

        $this->assertCount(5, $car->fresh()->photos ?? []);
    }

    public function test_supervisor_can_replace_single_photo(): void
    {
        Storage::fake('public');

        $supervisor = User::factory()->create([
            'role' => 'supervisor',
        ]);

        Storage::disk('public')->put('cars/3/a.jpg', 'old-a');
        Storage::disk('public')->put('cars/3/b.jpg', 'old-b');
        Storage::disk('public')->put('cars/3/c.jpg', 'old-c');
        Storage::disk('public')->put('cars/3/d.jpg', 'old-d');
        Storage::disk('public')->put('cars/3/e.jpg', 'old-e');
        Storage::disk('public')->put('cars/3/f.jpg', 'old-f');

        $car = Car::create([
            'kode_unit' => 'PHOTO-003',
            'merk' => 'Suzuki',
            'tipe' => 'XL7',
            'tahun' => 2024,
            'harga' => 255000000,
            'kilometer' => 500,
            'status' => 'available',
            'photos' => [
                'cars/3/a.jpg',
                'cars/3/b.jpg',
                'cars/3/c.jpg',
                'cars/3/d.jpg',
                'cars/3/e.jpg',
                'cars/3/f.jpg',
            ],
            'created_by' => $supervisor->id,
        ]);

        $response = $this->actingAs($supervisor)
            ->patch(route('supervisor.cars.photos.replace', [$car, 1]), [
                'photo' => UploadedFile::fake()->image('new-photo.jpg'),
            ]);

        $response->assertRedirect();

        $updatedPhotos = $car->fresh()->photos ?? [];

        $this->assertCount(6, $updatedPhotos);
        $this->assertNotSame('cars/3/b.jpg', $updatedPhotos[1]);
        Storage::disk('public')->assertExists($updatedPhotos[1]);
        Storage::disk('public')->assertMissing('cars/3/b.jpg');
    }

    public function test_supervisor_can_append_single_new_photo_during_car_update(): void
    {
        Storage::fake('public');

        $supervisor = User::factory()->create([
            'role' => 'supervisor',
        ]);

        $existingPhotos = [
            'cars/4/a.jpg',
            'cars/4/b.jpg',
            'cars/4/c.jpg',
            'cars/4/d.jpg',
            'cars/4/e.jpg',
        ];

        foreach ($existingPhotos as $photo) {
            Storage::disk('public')->put($photo, 'existing');
        }

        $car = Car::create([
            'kode_unit' => 'PHOTO-004',
            'merk' => 'Toyota',
            'tipe' => 'Yaris',
            'tahun' => 2021,
            'harga' => 220000000,
            'kilometer' => 1500,
            'status' => 'available',
            'transmisi' => 'AT',
            'warna' => 'Black',
            'bahan_bakar' => 'Bensin',
            'deskripsi' => 'Unit lama',
            'photos' => $existingPhotos,
            'created_by' => $supervisor->id,
        ]);

        $response = $this->actingAs($supervisor)->put(route('supervisor.cars.update', $car), [
            'kode_unit' => $car->kode_unit,
            'merk' => $car->merk,
            'tipe' => $car->tipe,
            'tahun' => $car->tahun,
            'harga' => $car->harga,
            'kilometer' => $car->kilometer,
            'transmisi' => $car->transmisi,
            'warna' => $car->warna,
            'bahan_bakar' => $car->bahan_bakar,
            'status' => $car->status,
            'deskripsi' => $car->deskripsi,
            'photos' => [
                UploadedFile::fake()->image('extra-photo.jpg'),
            ],
        ]);

        $response->assertRedirect(route('supervisor.cars.edit', $car) . '#stored-photos');
        $response->assertSessionHas('success', 'Foto unit berhasil ditambahkan.');

        $updatedPhotos = $car->fresh()->photos ?? [];

        $this->assertCount(6, $updatedPhotos);
        Storage::disk('public')->assertExists($updatedPhotos[5]);
    }

    public function test_marketing_cannot_append_single_new_photo_during_car_update(): void
    {
        Storage::fake('public');

        $marketing = User::factory()->create([
            'role' => 'marketing',
        ]);

        $existingPhotos = [
            'cars/5/a.jpg',
            'cars/5/b.jpg',
            'cars/5/c.jpg',
            'cars/5/d.jpg',
            'cars/5/e.jpg',
        ];

        foreach ($existingPhotos as $photo) {
            Storage::disk('public')->put($photo, 'existing');
        }

        $car = Car::create([
            'kode_unit' => 'PHOTO-005',
            'merk' => 'Honda',
            'tipe' => 'City',
            'tahun' => 2022,
            'harga' => 275000000,
            'kilometer' => 1200,
            'status' => 'available',
            'transmisi' => 'AT',
            'warna' => 'White',
            'bahan_bakar' => 'Bensin',
            'deskripsi' => 'Unit marketing',
            'photos' => $existingPhotos,
            'created_by' => $marketing->id,
        ]);

        $response = $this->actingAs($marketing)->put(route('marketing.products.update', $car), [
            'kode_unit' => $car->kode_unit,
            'merk' => $car->merk,
            'tipe' => $car->tipe,
            'tahun' => $car->tahun,
            'harga' => $car->harga,
            'kilometer' => $car->kilometer,
            'transmisi' => $car->transmisi,
            'warna' => $car->warna,
            'bahan_bakar' => $car->bahan_bakar,
            'status' => $car->status,
            'deskripsi' => $car->deskripsi,
            'photos' => [
                UploadedFile::fake()->image('marketing-extra-photo.jpg'),
            ],
        ]);

        $response->assertRedirect(route('marketing.products.index'));

        $updatedPhotos = $car->fresh()->photos ?? [];

        $this->assertCount(5, $updatedPhotos);
    }
}
