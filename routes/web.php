<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\UtangController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {

    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

        Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/tambah', [ProdukController::class, 'create'])->name('produk.create');
        Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
        Route::get('/produk/{produk}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
        Route::post('/produk/bulk-delete', [ProdukController::class, 'bulkDelete'])->name('produk.bulkDelete');

        Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::put('/inventory/{produk}/stok', [InventoryController::class, 'updateStok'])->name('inventory.updateStok');

        Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
        Route::post('/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
        Route::post('/pembelian/bulk-delete', [PembelianController::class, 'bulkDelete'])->name('pembelian.bulkDelete');

        Route::get('/utang-piutang', [UtangController::class, 'index'])->name('utang-piutang.index');
        Route::post('/utang', [UtangController::class, 'store'])->name('utang.store');
        Route::post('/utang/{utang}/bayar', [UtangController::class, 'bayar'])->name('utang.bayar');
        Route::post('/utang/bulk-delete', [UtangController::class, 'bulkDelete'])->name('utang.bulkDelete');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::post('/reset-data', [LaporanController::class, 'resetData'])->name('reset.data');

        Route::get('/admin/riwayat', [RiwayatController::class, 'index'])->name('riwayat.admin');
    });

    // Piutang (admin + kasir)
    Route::middleware('role:admin,kasir')->group(function () {
        Route::post('/piutang', [PiutangController::class, 'store'])->name('piutang.store');
        Route::post('/piutang/{piutang}/bayar', [PiutangController::class, 'bayar'])->name('piutang.bayar');
        Route::post('/piutang/bulk-delete', [PiutangController::class, 'bulkDelete'])->name('piutang.bulkDelete');
    });

    // Kasir routes
    Route::middleware('role:kasir')->group(function () {
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');

        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.kasir');
        Route::post('/riwayat/{penjualan}/batalkan', [RiwayatController::class, 'batalkan'])->name('riwayat.batalkan');

        Route::get('/kasir/piutang', [PiutangController::class, 'index'])->name('piutang.kasir');
    });
});
