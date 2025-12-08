<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientsSeeder extends Seeder
{
    public function run(): void
    {
        // لو عندك Admin مسبقاً
        $user = User::where('role', 'admin')->first();

        // لو مفيش Admin - نعمل واحد افتراضي
        if (!$user) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]);
        }

        // إضافة 10 Clients افتراضيين
        $clients = [
            [
                'name' => 'Company One',
                'email' => 'client1@example.com',
                'phone' => '01000000001',
                'address' => 'Cairo, Egypt',
            ],
            [
                'name' => 'Company Two',
                'email' => 'client2@example.com',
                'phone' => '01000000002',
                'address' => 'Alexandria, Egypt',
            ],
            [
                'name' => 'Client Three',
                'email' => 'client3@example.com',
                'phone' => '01000000003',
                'address' => 'Giza, Egypt',
            ],
            [
                'name' => 'Alpha Co.',
                'email' => 'alpha@example.com',
                'phone' => '01000000004',
                'address' => 'Dubai, UAE',
            ],
            [
                'name' => 'Beta Solutions',
                'email' => 'beta@example.com',
                'phone' => '01000000005',
                'address' => 'Riyadh, KSA',
            ],
        ];

        foreach ($clients as $client) {
            Client::create(array_merge($client, [
                'user_id' => $user->id,
            ]));
        }
    }
}
