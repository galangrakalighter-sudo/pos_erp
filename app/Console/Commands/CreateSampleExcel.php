<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CreateSampleExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:sample-excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sample Excel file for stock import testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $headers = ['nama', 'sku', 'lokasi', 'tersedia', 'harga', 'diperbaharui', 'kategori'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            // Sample data
            $data = [
                ['Beras Premium', 'BR001', 'Gudang A', 50, 15000, '2025-01-03', 'Makanan Pokok'],
                ['Minyak Goreng', 'MG002', 'Gudang B', 30, 25000, '2025-01-03', 'Makanan Pokok'],
                ['Gula Pasir', 'GP003', 'Gudang A', 25, 12000, '2025-01-03', 'Makanan Pokok'],
                ['Garam Dapur', 'GD004', 'Gudang C', 100, 5000, '2025-01-03', 'Makanan Pokok'],
                ['Telur Ayam', 'TA005', 'Gudang B', 200, 28000, '2025-01-03', 'Protein'],
                ['Ayam Potong', 'AP006', 'Gudang Dingin', 15, 35000, '2025-01-03', 'Protein'],
                ['Ikan Segar', 'IS007', 'Gudang Dingin', 20, 40000, '2025-01-03', 'Protein'],
                ['Wortel', 'WR008', 'Gudang C', 40, 8000, '2025-01-03', 'Sayuran'],
                ['Kentang', 'KT009', 'Gudang C', 35, 10000, '2025-01-03', 'Sayuran'],
                ['Bawang Merah', 'BM010', 'Gudang A', 60, 15000, '2025-01-03', 'Bumbu'],
                ['Bawang Putih', 'BP011', 'Gudang A', 45, 12000, '2025-01-03', 'Bumbu'],
                ['Cabai Merah', 'CM012', 'Gudang C', 25, 20000, '2025-01-03', 'Bumbu'],
                ['Tomat', 'TM013', 'Gudang C', 30, 10000, '2025-01-03', 'Sayuran'],
                ['Timun', 'TM014', 'Gudang C', 20, 8000, '2025-01-03', 'Sayuran'],
                ['Kacang Hijau', 'KH015', 'Gudang A', 40, 18000, '2025-01-03', 'Kacang-kacangan'],
                ['Kacang Merah', 'KM016', 'Gudang A', 35, 16000, '2025-01-03', 'Kacang-kacangan'],
                ['Tempe', 'TP017', 'Gudang Dingin', 50, 12000, '2025-01-03', 'Protein'],
                ['Tahu', 'TH018', 'Gudang Dingin', 60, 8000, '2025-01-03', 'Protein'],
                ['Susu UHT', 'SU019', 'Gudang Dingin', 80, 15000, '2025-01-03', 'Minuman'],
                ['Roti Tawar', 'RT020', 'Gudang A', 25, 12000, '2025-01-03', 'Makanan Siap Saji']
            ];

            // Add data to sheet
            $row = 2;
            foreach ($data as $rowData) {
                $col = 'A';
                foreach ($rowData as $cellData) {
                    $sheet->setCellValue($col . $row, $cellData);
                    $col++;
                }
                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'G') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Create writer and save file
            $writer = new Xlsx($spreadsheet);
            $filePath = public_path('sample_stock_import.xlsx');
            $writer->save($filePath);

            $this->info("File Excel berhasil dibuat: {$filePath}");
            $this->info("File berisi " . count($data) . " item stock dengan format yang sesuai untuk import.");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error creating Excel file: " . $e->getMessage());
            return 1;
        }
    }
}
