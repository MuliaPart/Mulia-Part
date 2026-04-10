<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductsImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2; // skip header
    }

    public function model(array $row)
    {
        // Bersihkan data dari spasi dan karakter aneh
        $kategori   = isset($row[0]) ? trim($row[0]) : null;
        $kode       = isset($row[1]) ? trim($row[1]) : null;
        $nama       = isset($row[2]) ? trim($row[2]) : null;
        $hargaPokok = isset($row[3]) ? (int) $row[3] : 0;
        $hargaJual  = isset($row[4]) ? (int) $row[4] : 0;
        $stok       = isset($row[5]) ? (int) $row[5] : 0;

        // Skip baris kosong
        if (!$kode || !$nama) {
            return null;
        }

        // Buat atau ambil kategori
        $category = Category::firstOrCreate([
            'name' => $kategori ?: 'Tanpa Kategori'
        ]);

        // Simpan produk
        return Product::updateOrCreate(
            ['code' => $kode],
            [
                'category_id' => $category->id,
                'name'        => $nama,
                'cost_price'  => $hargaPokok,
                'sell_price'  => $hargaJual,
                'stock'       => $stok,
            ]
        );
    }
}