<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ---------------------------------------------------------------------
// Public routes (no auth required)
// ---------------------------------------------------------------------
$routes->get('/', 'C_Auth::login');
$routes->get('login', 'C_Auth::login');
$routes->post('login', 'C_Auth::attemptLogin');
$routes->get('logout', 'C_Auth::logout');

// ---------------------------------------------------------------------
// Authenticated routes (any logged-in role)
// ---------------------------------------------------------------------
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// ---------------------------------------------------------------------
// Master Data — admin manages all; kasir/produser may need read access
// later as their modules are built (adjust per-route as needed).
// ---------------------------------------------------------------------
$routes->group('kategori', ['filter' => 'auth:admin'], static function ($routes): void {
    $routes->get('/', 'C_KategoriProduk::index');
    $routes->get('create', 'C_KategoriProduk::create');
    $routes->post('store', 'C_KategoriProduk::store');
    $routes->get('edit/(:num)', 'C_KategoriProduk::edit/$1');
    $routes->post('update/(:num)', 'C_KategoriProduk::update/$1');
    $routes->get('delete/(:num)', 'C_KategoriProduk::delete/$1');
});

$routes->group('bahan-baku', ['filter' => 'auth:admin,produser'], static function ($routes): void {
    $routes->get('/', 'C_BahanBaku::index');
    $routes->get('create', 'C_BahanBaku::create');
    $routes->post('store', 'C_BahanBaku::store');
    $routes->get('edit/(:num)', 'C_BahanBaku::edit/$1');
    $routes->post('update/(:num)', 'C_BahanBaku::update/$1');
    $routes->get('delete/(:num)', 'C_BahanBaku::delete/$1');
});

$routes->group('produk', ['filter' => 'auth:admin'], static function ($routes): void {
    $routes->get('/', 'C_Produk::index');
    $routes->get('create', 'C_Produk::create');
    $routes->post('store', 'C_Produk::store');
    $routes->get('edit/(:num)', 'C_Produk::edit/$1');
    $routes->post('update/(:num)', 'C_Produk::update/$1');
    $routes->get('delete/(:num)', 'C_Produk::delete/$1');

    $routes->get('resep/(:num)', 'C_Resep::index/$1');
    $routes->post('resep/(:num)/store', 'C_Resep::store/$1');
    $routes->get('resep/(:num)/delete/(:num)', 'C_Resep::delete/$1/$2');
});

// ---------------------------------------------------------------------
// Transaksi — produser handles produksi, kasir handles penjualan.
// ---------------------------------------------------------------------
$routes->group('produksi', ['filter' => 'auth:admin,produser'], static function ($routes): void {
    $routes->get('/', 'C_Produksi::index');
    $routes->get('create', 'C_Produksi::create');
    $routes->post('store', 'C_Produksi::store');
    $routes->get('detail/(:num)', 'C_Produksi::detail/$1');
    $routes->post('update-status/(:num)', 'C_Produksi::updateStatus/$1');
});

$routes->group('penjualan', ['filter' => 'auth:admin,kasir'], static function ($routes): void {
    $routes->get('/', 'C_Penjualan::index');
    $routes->get('create', 'C_Penjualan::create');
    $routes->post('store', 'C_Penjualan::store');
    $routes->get('detail/(:num)', 'C_Penjualan::detail/$1');
});

// ---------------------------------------------------------------------
// Laporan — admin only for now.
// ---------------------------------------------------------------------
$routes->group('laporan', ['filter' => 'auth:admin'], static function ($routes): void {
    $routes->get('penjualan', 'Laporan::penjualan');
    $routes->get('produksi', 'Laporan::produksi');
});

// ---------------------------------------------------------------------
// User management (multi-user) — admin only.
// ---------------------------------------------------------------------
$routes->group('users', ['filter' => 'auth:admin'], static function ($routes): void {
    $routes->get('/', 'Users::index');
    $routes->get('create', 'Users::create');
    $routes->post('store', 'Users::store');
    $routes->get('edit/(:num)', 'Users::edit/$1');
    $routes->post('update/(:num)', 'Users::update/$1');
    $routes->get('delete/(:num)', 'Users::delete/$1');
});