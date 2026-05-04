<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Favorite;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TestDrive;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['email' => 'owner@maharani.com'],
            [
                'name' => 'Owner Maharani',
                'phone' => '081200000001',
                'password' => Hash::make('Rarendra1234'),
                'role' => 'owner',
            ]
        );

        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@maharani.com'],
            [
                'name' => 'Supervisor Maharani',
                'phone' => '081200000002',
                'password' => Hash::make('Supervisormaharani123'),
                'role' => 'supervisor',
            ]
        );

        $marketing = User::firstOrCreate(
            ['email' => 'marketing@maharani.com'],
            [
                'name' => 'Marketing Maharani',
                'phone' => '081200000003',
                'password' => Hash::make('Marketingmaharani123'),
                'role' => 'marketing',
            ]
        );

        $customer1 = User::firstOrCreate(
            ['email' => 'customer1@gmail.com'],
            [
                'name' => 'Customer One',
                'phone' => '081200000004',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        $customer2 = User::firstOrCreate(
            ['email' => 'customer2@gmail.com'],
            [
                'name' => 'Customer Two',
                'phone' => '081200000005',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        $cars = collect([
            [
                'kode_unit' => 'MM-AVZ-2019',
                'merk' => 'Toyota',
                'tipe' => 'Avanza G',
                'tahun' => 2019,
                'harga' => 185000000,
                'kilometer' => 42000,
                'transmisi' => 'Automatic',
                'warna' => 'White',
                'bahan_bakar' => 'Bensin',
                'status' => 'available',
                'deskripsi' => 'Unit keluarga terawat, servis berkala.',
                'created_by' => $supervisor->id,
            ],
            [
                'kode_unit' => 'MM-HRV-2021',
                'merk' => 'Honda',
                'tipe' => 'HR-V 1.5 E',
                'tahun' => 2021,
                'harga' => 295000000,
                'kilometer' => 18500,
                'transmisi' => 'CVT',
                'warna' => 'Gray',
                'bahan_bakar' => 'Bensin',
                'status' => 'reserved',
                'deskripsi' => 'Interior bersih, pajak panjang.',
                'created_by' => $supervisor->id,
            ],
            [
                'kode_unit' => 'MM-CAM-2022',
                'merk' => 'Toyota',
                'tipe' => 'Camry 2.5 V Hybrid',
                'tahun' => 2022,
                'harga' => 545000000,
                'kilometer' => 12450,
                'transmisi' => 'Automatic',
                'warna' => 'White Pearl',
                'bahan_bakar' => 'Hybrid',
                'status' => 'available',
                'deskripsi' => 'Hybrid premium, low mileage.',
                'created_by' => $supervisor->id,
            ],
        ])->map(function ($data) {
            return Car::updateOrCreate(
                ['kode_unit' => $data['kode_unit']],
                $data
            );
        });

        $order = Order::firstOrCreate(
            [
                'user_id' => $customer1->id,
                'car_id' => $cars[0]->id,
            ],
            [
                'status' => 'paid',
                'total' => 185000000,
                'payment_method' => 'transfer',
                'notes' => 'DP 20%',
            ]
        );

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => 'transfer',
                'amount' => 185000000,
                'status' => 'verified',
                'verified_by' => $supervisor->id,
                'verified_at' => now(),
            ]
        );

        Favorite::firstOrCreate([
            'user_id' => $customer1->id,
            'car_id' => $cars[2]->id,
        ]);

        TestDrive::firstOrCreate(
            [
                'user_id' => $customer2->id,
                'car_id' => $cars[2]->id,
                'booking_date' => now()->addDays(2)->toDateString(),
                'booking_time' => '10:00',
            ],
            [
                'status' => 'pending',
            ]
        );

        Offer::firstOrCreate(
            [
                'user_id' => $customer2->id,
                'car_id' => $cars[1]->id,
                'offer_price' => 285000000,
            ],
            [
                'status' => 'pending',
            ]
        );
    }
}
