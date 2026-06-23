<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\Piutang;
use App\Models\PembayaranPiutang;
use App\Models\Produk;
use App\Models\ItemPembelian;
use App\Models\Toko;
use App\Models\User;
use App\Models\Utang;
use App\Models\PembayaranUtang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $tokoA = Toko::create(['nama' => 'Toko Pondok Kopi']);
        $tokoB = Toko::create(['nama' => 'Toko Pondok Labu']);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin12345'),
            'role' => 'admin',
            'toko_id' => null,
        ]);

        User::create([
            'name' => 'Kasir Toko A',
            'email' => 'kasir1@gmail.com',
            'password' => Hash::make('kasir12345'),
            'role' => 'kasir',
            'toko_id' => $tokoA->id,
        ]);

        User::create([
            'name' => 'Kasir Toko B',
            'email' => 'kasir2@gmail.com',
            'password' => Hash::make('kasir12345'),
            'role' => 'kasir',
            'toko_id' => $tokoB->id,
        ]);

        $sembako = Kategori::create(['nama' => 'Sembako']);
        $minuman = Kategori::create(['nama' => 'Minuman']);
        $bumbu = Kategori::create(['nama' => 'Bumbu']);
        $rokok = Kategori::create(['nama' => 'Rokok']);
        $snack = Kategori::create(['nama' => 'Snack']);
        $lainnya = Kategori::create(['nama' => 'Lainnya']);

        // === PRODUK TOKO A ===
        $produkDataA = [
            ['nama' => 'Beras Premium 5kg', 'kategori_id' => $sembako->id, 'stok' => 50, 'stok_minimum' => 10, 'harga_jual' => 75000, 'harga_beli' => 65000],
            ['nama' => 'Minyak Goreng 1L', 'kategori_id' => $sembako->id, 'stok' => 40, 'stok_minimum' => 8, 'harga_jual' => 18000, 'harga_beli' => 15000],
            ['nama' => 'Gula Pasir 1kg', 'kategori_id' => $sembako->id, 'stok' => 35, 'stok_minimum' => 10, 'harga_jual' => 16000, 'harga_beli' => 13500],
            ['nama' => 'Tepung Terigu 1kg', 'kategori_id' => $sembako->id, 'stok' => 25, 'stok_minimum' => 5, 'harga_jual' => 12000, 'harga_beli' => 10000],
            ['nama' => 'Telur Ayam 1kg', 'kategori_id' => $sembako->id, 'stok' => 30, 'stok_minimum' => 10, 'harga_jual' => 28000, 'harga_beli' => 25000],
            ['nama' => 'Indomie Goreng', 'kategori_id' => $sembako->id, 'stok' => 100, 'stok_minimum' => 20, 'harga_jual' => 3500, 'harga_beli' => 2800],
            ['nama' => 'Indomie Kuah Soto', 'kategori_id' => $sembako->id, 'stok' => 80, 'stok_minimum' => 20, 'harga_jual' => 3500, 'harga_beli' => 2800],
            ['nama' => 'Kecap Manis ABC 135ml', 'kategori_id' => $bumbu->id, 'stok' => 20, 'stok_minimum' => 5, 'harga_jual' => 8000, 'harga_beli' => 6500],
            ['nama' => 'Saos Sambal ABC 135ml', 'kategori_id' => $bumbu->id, 'stok' => 18, 'stok_minimum' => 5, 'harga_jual' => 7500, 'harga_beli' => 6000],
            ['nama' => 'Garam Halus 250g', 'kategori_id' => $bumbu->id, 'stok' => 30, 'stok_minimum' => 10, 'harga_jual' => 4000, 'harga_beli' => 2500],
            ['nama' => 'Teh Botol Sosro 450ml', 'kategori_id' => $minuman->id, 'stok' => 48, 'stok_minimum' => 12, 'harga_jual' => 5000, 'harga_beli' => 3800],
            ['nama' => 'Aqua 600ml', 'kategori_id' => $minuman->id, 'stok' => 60, 'stok_minimum' => 15, 'harga_jual' => 4000, 'harga_beli' => 2500],
            ['nama' => 'Kopi Kapal Api Sachet', 'kategori_id' => $minuman->id, 'stok' => 50, 'stok_minimum' => 15, 'harga_jual' => 2000, 'harga_beli' => 1500],
            ['nama' => 'Surya 12', 'kategori_id' => $rokok->id, 'stok' => 25, 'stok_minimum' => 5, 'harga_jual' => 25000, 'harga_beli' => 22000],
            ['nama' => 'Djarum Super 12', 'kategori_id' => $rokok->id, 'stok' => 20, 'stok_minimum' => 5, 'harga_jual' => 22000, 'harga_beli' => 19500],
            ['nama' => 'Chitato 68g', 'kategori_id' => $snack->id, 'stok' => 3, 'stok_minimum' => 5, 'harga_jual' => 11000, 'harga_beli' => 9000],
            ['nama' => 'Oreo 133g', 'kategori_id' => $snack->id, 'stok' => 0, 'stok_minimum' => 5, 'harga_jual' => 10000, 'harga_beli' => 8000],
            ['nama' => 'Sabun Lifebuoy 100g', 'kategori_id' => $lainnya->id, 'stok' => 15, 'stok_minimum' => 5, 'harga_jual' => 5000, 'harga_beli' => 3800],
        ];

        $produksA = [];
        foreach ($produkDataA as $p) {
            $produksA[] = Produk::create(array_merge($p, ['toko_id' => $tokoA->id]));
        }

        // === PRODUK TOKO B ===
        $produkDataB = [
            ['nama' => 'Beras Premium 5kg', 'kategori_id' => $sembako->id, 'stok' => 45, 'stok_minimum' => 10, 'harga_jual' => 76000, 'harga_beli' => 65000],
            ['nama' => 'Minyak Goreng 2L', 'kategori_id' => $sembako->id, 'stok' => 30, 'stok_minimum' => 8, 'harga_jual' => 34000, 'harga_beli' => 29000],
            ['nama' => 'Gula Pasir 1kg', 'kategori_id' => $sembako->id, 'stok' => 28, 'stok_minimum' => 10, 'harga_jual' => 16500, 'harga_beli' => 13500],
            ['nama' => 'Indomie Goreng', 'kategori_id' => $sembako->id, 'stok' => 90, 'stok_minimum' => 20, 'harga_jual' => 3500, 'harga_beli' => 2800],
            ['nama' => 'Telur Ayam 1kg', 'kategori_id' => $sembako->id, 'stok' => 20, 'stok_minimum' => 10, 'harga_jual' => 28000, 'harga_beli' => 25000],
            ['nama' => 'Susu Ultra 1L', 'kategori_id' => $minuman->id, 'stok' => 15, 'stok_minimum' => 5, 'harga_jual' => 17000, 'harga_beli' => 14000],
            ['nama' => 'Teh Pucuk 350ml', 'kategori_id' => $minuman->id, 'stok' => 36, 'stok_minimum' => 10, 'harga_jual' => 4500, 'harga_beli' => 3200],
            ['nama' => 'Aqua 600ml', 'kategori_id' => $minuman->id, 'stok' => 48, 'stok_minimum' => 12, 'harga_jual' => 4000, 'harga_beli' => 2500],
            ['nama' => 'Gudang Garam Filter 12', 'kategori_id' => $rokok->id, 'stok' => 18, 'stok_minimum' => 5, 'harga_jual' => 28000, 'harga_beli' => 25000],
            ['nama' => 'Sampoerna Mild 16', 'kategori_id' => $rokok->id, 'stok' => 2, 'stok_minimum' => 5, 'harga_jual' => 32000, 'harga_beli' => 28500],
            ['nama' => 'Beng-Beng', 'kategori_id' => $snack->id, 'stok' => 24, 'stok_minimum' => 8, 'harga_jual' => 3000, 'harga_beli' => 2200],
            ['nama' => 'Deterjen Rinso 800g', 'kategori_id' => $lainnya->id, 'stok' => 10, 'stok_minimum' => 3, 'harga_jual' => 15000, 'harga_beli' => 12500],
        ];

        $produksB = [];
        foreach ($produkDataB as $p) {
            $produksB[] = Produk::create(array_merge($p, ['toko_id' => $tokoB->id]));
        }

        // === RESTOCK (beberapa hari terakhir) ===
        $restockData = [
            [$produksA[0], 30, 65000, 75000, now()->subDays(10)],
            [$produksA[1], 20, 15000, 18000, now()->subDays(8)],
            [$produksA[5], 50, 2800, 3500, now()->subDays(5)],
            [$produksA[10], 24, 3800, 5000, now()->subDays(3)],
            [$produksB[0], 25, 65000, 76000, now()->subDays(7)],
            [$produksB[3], 40, 2800, 3500, now()->subDays(4)],
            [$produksB[6], 24, 3200, 4500, now()->subDays(2)],
        ];

        foreach ($restockData as [$produk, $jumlah, $hargaBeli, $hargaJual, $tanggal]) {
            ItemPembelian::create([
                'produk_id' => $produk->id,
                'jumlah' => $jumlah,
                'harga_beli' => $hargaBeli,
                'harga_jual' => $hargaJual,
                'tanggal' => $tanggal->toDateString(),
                'supplier' => 'Gudang Pusat',
            ]);
        }

        // === PENJUALAN TOKO A ===
        $kasirNames = ['Siti', 'Budi', 'Rina'];
        $penjualanDataA = [
            [$produksA[0], 3, 75000, now()->subDays(6)],
            [$produksA[0], 2, 74000, now()->subDays(4)],
            [$produksA[1], 5, 18000, now()->subDays(5)],
            [$produksA[2], 3, 16000, now()->subDays(3)],
            [$produksA[4], 2, 28000, now()->subDays(2)],
            [$produksA[5], 10, 3500, now()->subDays(1)],
            [$produksA[5], 8, 3000, now()],
            [$produksA[6], 5, 3500, now()],
            [$produksA[10], 6, 5000, now()->subDays(1)],
            [$produksA[11], 4, 4000, now()],
            [$produksA[12], 10, 2000, now()->subDays(2)],
            [$produksA[13], 3, 25000, now()->subDays(1)],
            [$produksA[14], 2, 22000, now()],
            [$produksA[7], 3, 8000, now()->subDays(3)],
            [$produksA[17], 2, 5000, now()],
        ];

        foreach ($penjualanDataA as [$produk, $jumlah, $hargaJual, $tanggal]) {
            Penjualan::create([
                'produk_id' => $produk->id,
                'toko_id' => $tokoA->id,
                'jumlah' => $jumlah,
                'harga_jual' => $hargaJual,
                'harga_beli_saat_itu' => $produk->harga_beli,
                'tanggal' => $tanggal->toDateString(),
                'nama_kasir' => $kasirNames[array_rand($kasirNames)],
                'status' => 'aktif',
            ]);
        }

        // 1 transaksi dibatalkan
        Penjualan::create([
            'produk_id' => $produksA[2]->id,
            'toko_id' => $tokoA->id,
            'jumlah' => 5,
            'harga_jual' => 16000,
            'harga_beli_saat_itu' => $produksA[2]->harga_beli,
            'tanggal' => now()->subDays(2)->toDateString(),
            'nama_kasir' => 'Budi',
            'status' => 'dibatalkan',
            'catatan' => 'Salah input jumlah',
        ]);

        // === PENJUALAN TOKO B ===
        $penjualanDataB = [
            [$produksB[0], 4, 76000, now()->subDays(5)],
            [$produksB[0], 2, 75000, now()->subDays(2)],
            [$produksB[1], 3, 34000, now()->subDays(3)],
            [$produksB[2], 4, 16500, now()->subDays(1)],
            [$produksB[3], 15, 3500, now()],
            [$produksB[4], 3, 28000, now()->subDays(1)],
            [$produksB[6], 8, 4500, now()],
            [$produksB[7], 6, 4000, now()->subDays(2)],
            [$produksB[8], 2, 28000, now()],
            [$produksB[10], 5, 3000, now()->subDays(1)],
            [$produksB[11], 2, 15000, now()],
        ];

        foreach ($penjualanDataB as [$produk, $jumlah, $hargaJual, $tanggal]) {
            Penjualan::create([
                'produk_id' => $produk->id,
                'toko_id' => $tokoB->id,
                'jumlah' => $jumlah,
                'harga_jual' => $hargaJual,
                'harga_beli_saat_itu' => $produk->harga_beli,
                'tanggal' => $tanggal->toDateString(),
                'nama_kasir' => ['Dedi', 'Ani'][array_rand(['Dedi', 'Ani'])],
                'status' => 'aktif',
            ]);
        }

        // === UTANG ===
        $utang1 = Utang::create([
            'nama' => 'Utang Beras Batch Juni',
            'supplier' => 'Gudang Pusat',
            'nominal' => 5000000,
            'sisa' => 3000000,
            'tanggal' => now()->subDays(20)->toDateString(),
            'jatuh_tempo' => now()->addDays(10)->toDateString(),
            'keterangan' => 'Beras 100 karung untuk 2 toko',
            'toko_id' => null,
        ]);
        PembayaranUtang::create(['utang_id' => $utang1->id, 'nominal' => 2000000, 'tanggal' => now()->subDays(10)->toDateString()]);

        $utang2 = Utang::create([
            'nama' => 'Utang Minyak Mei',
            'supplier' => 'Gudang Pusat',
            'nominal' => 2500000,
            'sisa' => 0,
            'tanggal' => now()->subDays(40)->toDateString(),
            'jatuh_tempo' => now()->subDays(10)->toDateString(),
            'keterangan' => 'Sudah lunas',
            'toko_id' => $tokoA->id,
        ]);
        PembayaranUtang::create(['utang_id' => $utang2->id, 'nominal' => 2500000, 'tanggal' => now()->subDays(15)->toDateString()]);

        Utang::create([
            'nama' => 'Utang Rokok Juni',
            'supplier' => 'Gudang Pusat',
            'nominal' => 1800000,
            'sisa' => 1800000,
            'tanggal' => now()->subDays(5)->toDateString(),
            'jatuh_tempo' => now()->subDays(1)->toDateString(),
            'keterangan' => 'Rokok campur untuk Toko B',
            'toko_id' => $tokoB->id,
        ]);

        Utang::create([
            'nama' => 'Utang Snack Juni',
            'supplier' => 'Gudang Pusat',
            'nominal' => 800000,
            'sisa' => 800000,
            'tanggal' => now()->subDays(3)->toDateString(),
            'jatuh_tempo' => now()->addDays(14)->toDateString(),
            'keterangan' => 'Snack & bumbu campur',
        ]);

        // === PIUTANG ===
        $pi1 = Piutang::create([
            'toko_id' => $tokoA->id,
            'nama_pelanggan' => 'Bu Warni',
            'nominal' => 250000,
            'sisa' => 100000,
            'tanggal' => now()->subDays(14)->toDateString(),
            'jatuh_tempo' => now()->addDays(3)->toDateString(),
            'keterangan' => 'Beras + Minyak, bayar setelah gajian',
        ]);
        PembayaranPiutang::create(['piutang_id' => $pi1->id, 'nominal' => 150000, 'tanggal' => now()->subDays(7)->toDateString()]);

        Piutang::create([
            'toko_id' => $tokoA->id,
            'nama_pelanggan' => 'Pak Joko',
            'nominal' => 180000,
            'sisa' => 180000,
            'tanggal' => now()->subDays(5)->toDateString(),
            'jatuh_tempo' => now()->subDays(2)->toDateString(),
            'keterangan' => 'Sembako bulanan',
        ]);

        Piutang::create([
            'toko_id' => $tokoA->id,
            'nama_pelanggan' => 'Mbak Dewi',
            'nominal' => 75000,
            'sisa' => 0,
            'tanggal' => now()->subDays(10)->toDateString(),
            'jatuh_tempo' => now()->subDays(3)->toDateString(),
            'keterangan' => 'Sudah lunas',
        ]);

        Piutang::create([
            'toko_id' => $tokoB->id,
            'nama_pelanggan' => 'Pak Hendra',
            'nominal' => 350000,
            'sisa' => 350000,
            'tanggal' => now()->subDays(7)->toDateString(),
            'jatuh_tempo' => now()->addDays(7)->toDateString(),
            'keterangan' => 'Belanja warung, bayar akhir bulan',
        ]);

        $pi5 = Piutang::create([
            'toko_id' => $tokoB->id,
            'nama_pelanggan' => 'Bu Sari',
            'nominal' => 120000,
            'sisa' => 50000,
            'tanggal' => now()->subDays(12)->toDateString(),
            'jatuh_tempo' => now()->subDays(1)->toDateString(),
            'keterangan' => 'Rokok + snack',
        ]);
        PembayaranPiutang::create(['piutang_id' => $pi5->id, 'nominal' => 70000, 'tanggal' => now()->subDays(5)->toDateString()]);
    }
}
