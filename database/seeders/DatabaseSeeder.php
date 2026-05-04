<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Daftar akun internal resmi yang dipakai aplikasi.
     */
    private const INTERNAL_ACCOUNTS = [
        [
            'name' => 'Supervisor Maharani',
            'email' => 'supervisor@maharani.com',
            'aliases' => [
                'supervisor@maharani.com',
                'supervisormaharani@gmail.com',
            ],
            'password' => 'Supervisormaharani123',
            'role' => 'supervisor',
            'phone' => '081200000002',
        ],
        [
            'name' => 'Marketing Maharani',
            'email' => 'marketing@maharani.com',
            'aliases' => [
                'marketing@maharani.com',
                'marketingmaharani@gmail.com',
            ],
            'password' => 'Marketingmaharani123',
            'role' => 'marketing',
            'phone' => '081200000003',
        ],
        [
            'name' => 'Owner Maharani',
            'email' => 'owner@maharani.com',
            'aliases' => [
                'owner@maharani.com',
                'ownermaharani@gmail.com',
            ],
            'password' => 'Rarendra1234',
            'role' => 'owner',
            'phone' => '081200000001',
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedInternalAccounts();

        if (!User::where('email', 'customer1@gmail.com')->exists()) {
            $this->call(DummyDataSeeder::class);
        }
    }

    /**
     * Membuat atau memperbarui akun internal resmi untuk role supervisor, marketing, dan owner.
     */
    private function seedInternalAccounts(): void
    {
        foreach (self::INTERNAL_ACCOUNTS as $account) {
            $user = User::query()
                ->where(function ($query) use ($account) {
                    foreach (array_values($account['aliases']) as $index => $alias) {
                        if ($index === 0) {
                            $query->whereRaw('LOWER(email) = ?', [strtolower($alias)]);
                            continue;
                        }

                        $query->orWhereRaw('LOWER(email) = ?', [strtolower($alias)]);
                    }
                })
                ->first();

            if (!$user) {
                $user = new User();
            }

            $user->fill([
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => Hash::make($account['password']),
                'role' => $account['role'],
                'provider' => null,
                'provider_id' => null,
                'phone' => $account['phone'],
            ]);

            $user->save();
        }
    }
}
