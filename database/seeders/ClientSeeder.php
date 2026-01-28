<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'nama' => 'Ray',
                'client_id' => 'Ray-098',
                'alamat' => 'Jl. Sudirman No. 123, Jakarta',
                'telepon' => '081234567890',
                'tanggal_bergabung' => now(),
                'diperbaharui' => now(),
            ],
            [
                'nama' => 'Sarah',
                'client_id' => 'Sarah-001',
                'alamat' => 'Jl. Thamrin No. 45, Jakarta',
                'telepon' => '081234567891',
                'tanggal_bergabung' => now(),
                'diperbaharui' => now(),
            ],
            [
                'nama' => 'Budi',
                'client_id' => 'Budi-002',
                'alamat' => 'Jl. Gatot Subroto No. 67, Jakarta',
                'telepon' => '081234567892',
                'tanggal_bergabung' => now(),
                'diperbaharui' => now(),
            ],
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }
    }
}
