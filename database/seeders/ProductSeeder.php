<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'nama_produk' => 'Serum Vitamin C Premium',
                'deskripsi' => 'Serum wajah dengan kandungan Vitamin C tinggi untuk mencerahkan kulit dan mengurangi tanda penuaan',
                'harga' => 250000,
                'stok' => 25,
            ],
            [
                'nama_produk' => 'Moisturizer Anti-Aging',
                'deskripsi' => 'Pelembab wajah dengan formula anti-aging untuk kulit dewasa',
                'harga' => 180000,
                'stok' => 30,
            ],
            [
                'nama_produk' => 'Sunscreen SPF 50+',
                'deskripsi' => 'Tabir surya dengan perlindungan maksimal dari sinar UV',
                'harga' => 120000,
                'stok' => 40,
            ],
            [
                'nama_produk' => 'Toner Hydrating',
                'deskripsi' => 'Toner untuk melembabkan dan menyeimbangkan pH kulit',
                'harga' => 95000,
                'stok' => 35,
            ],
            [
                'nama_produk' => 'Face Mask Collagen',
                'deskripsi' => 'Masker wajah dengan kandungan kolagen untuk kulit kenyal dan sehat',
                'harga' => 75000,
                'stok' => 50,
            ],
            [
                'nama_produk' => 'Eye Cream Retinol',
                'deskripsi' => 'Krim mata dengan retinol untuk mengurangi kerutan dan kantung mata',
                'harga' => 320000,
                'stok' => 15,
            ],
            [
                'nama_produk' => 'Cleanser Gentle Formula',
                'deskripsi' => 'Pembersih wajah dengan formula lembut untuk semua jenis kulit',
                'harga' => 85000,
                'stok' => 45,
            ],
            [
                'nama_produk' => 'Exfoliating Scrub',
                'deskripsi' => 'Scrub wajah untuk mengangkat sel kulit mati dan mencerahkan kulit',
                'harga' => 65000,
                'stok' => 28,
            ],
            [
                'nama_produk' => 'Acne Treatment Gel',
                'deskripsi' => 'Gel perawatan jerawat dengan kandungan salicylic acid',
                'harga' => 110000,
                'stok' => 20,
            ],
            [
                'nama_produk' => 'Brightening Essence',
                'deskripsi' => 'Essence pencerah dengan ekstrak niacinamide dan arbutin',
                'harga' => 150000,
                'stok' => 22,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['nama_produk' => $product['nama_produk']],
                $product
            );
        }
    }
}
