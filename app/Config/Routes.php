<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ---------------------------------------------------------------------
// Public routes (no auth required)
// ---------------------------------------------------------------------
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// ---------------------------------------------------------------------
// Authenticated routes (any logged-in role)
// ---------------------------------------------------------------------
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// ---------------------------------------------------------------------
// Master Data — admin manages all; kasir/produser may need read access
// later as their modules are built (adjust per-route as needed).
// ---------------------------------------------------------------------
$routes->group('kategori', ['filter' => 'auth:admin'], static function ($routes): void {
    $routes->get('/', 'KategoriProduk::index');
    $routes->get('create', 'KategoriProduk::create');
    $routes->post('store', 'KategoriProduk::store');
    $routes->get('edit/(:num)', 'KategoriProduk::edit/$1');
    $routes->post('update/(:num)', 'KategoriProduk::update/$1');
    $routes->get('delete/(:num)', 'KategoriProduk::delete/$1');
});

$routes->group('bahan-baku', ['filter' => 'auth:admin,produser'], static function ($routes): void {
    $routes->get('/', 'BahanBaku::index');
    $routes->get('create', 'BahanBaku::create');
    $routes->post('store', 'BahanBaku::store');
    $routes->get('edit/(:num)', 'BahanBaku::edit/$1');
    $routes->post('update/(:num)', 'BahanBaku::update/$1');
    $routes->get('delete/(:num)', 'BahanBaku::delete/$1');
});

$routes->group('produk', ['filter' => 'auth:admin'], static function ($routes): void {
    $routes->get('/', 'Produk::index');
    $routes->get('create', 'Produk::create');
    $routes->post('store', 'Produk::store');
    $routes->get('edit/(:num)', 'Produk::edit/$1');
    $routes->post('update/(:num)', 'Produk::update/$1');
    $routes->get('delete/(:num)', 'Produk::delete/$1');
});

// ---------------------------------------------------------------------
// Transaksi — produser handles produksi, kasir handles penjualan.
// ---------------------------------------------------------------------
$routes->group('produksi', ['filter' => 'auth:admin,produser'], static function ($routes): void {
    $routes->get('/', 'Produksi::index');
    $routes->get('create', 'Produksi::create');
    $routes->post('store', 'Produksi::store');
    $routes->get('detail/(:num)', 'Produksi::detail/$1');
});

$routes->group('penjualan', ['filter' => 'auth:admin,kasir'], static function ($routes): void {
    $routes->get('/', 'Penjualan::index');
    $routes->get('create', 'Penjualan::create');
    $routes->post('store', 'Penjualan::store');
    $routes->get('detail/(:num)', 'Penjualan::detail/$1');
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