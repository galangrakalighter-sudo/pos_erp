<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sale;

class SalesWithDiscountBallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data test sebelumnya
        Sale::where('nama_pemesan', 'Test Customer')->delete();

        // Buat beberapa transaksi dengan berbagai kombinasi diskon dan ongkir
        $sales = [
            [
                'nama_pemesan' => 'PT Maju Bersama',
                'id_pesanan' => 'MB-001',
                'status' => 'Selesai',
                'periode' => '2025-09-02',
                'jenis_transaksi' => 'Transfer',
                'nama_bank' => 'BCA',
                'id_bank' => '1234567890',
                'diskon_tipe' => 'rupiah',
                'diskon_nilai' => 25000,
                'diskon_ball_tipe' => 'persen',
                'diskon_ball_nilai' => 10,
                'nama_ekspedisi' => 'SiCepat',
                'ongkir' => 20000,
                'total_quantity' => 150,
                'total_diskon' => 25000,
                'total_diskon_ball' => 22500, // 10% dari (subtotal - diskon reguler)
                'total_harga' => 452500,
            ],
            [
                'nama_pemesan' => 'CV Sukses Mandiri',
                'id_pesanan' => 'SM-002',
                'status' => 'Dalam Proses-Sudah Dibayar',
                'periode' => '2025-09-02',
                'jenis_transaksi' => 'Tunai',
                'nama_bank' => null,
                'id_bank' => null,
                'diskon_tipe' => 'persen',
                'diskon_nilai' => 15,
                'diskon_ball_tipe' => 'rupiah',
                'diskon_ball_nilai' => 50000,
                'nama_ekspedisi' => 'J&T Express',
                'ongkir' => 18000,
                'total_quantity' => 200,
                'total_diskon' => 75000, // 15% dari subtotal
                'total_diskon_ball' => 50000,
                'total_harga' => 393000,
            ],
            [
                'nama_pemesan' => 'UD Makmur Jaya',
                'id_pesanan' => 'MJ-003',
                'status' => 'Selesai',
                'periode' => '2025-09-02',
                'jenis_transaksi' => 'Transfer',
                'nama_bank' => 'Mandiri',
                'id_bank' => '0987654321',
                'diskon_tipe' => null,
                'diskon_nilai' => 0,
                'diskon_ball_tipe' => null,
                'diskon_ball_nilai' => 0,
                'nama_ekspedisi' => 'JNE',
                'ongkir' => 25000,
                'total_quantity' => 100,
                'total_diskon' => 0,
                'total_diskon_ball' => 0,
                'total_harga' => 525000,
            ],
        ];

        foreach ($sales as $saleData) {
            Sale::create($saleData);
        }

        $this->command->info('Sales data with discount ball and shipping has been seeded successfully!');
    }
}
