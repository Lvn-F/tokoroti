<?php

namespace App\Controllers;

use App\Models\M_BahanBaku;
use App\Models\M_LogActivity;
use App\Models\M_Penjualan;
use App\Models\M_Produk;
use App\Models\M_Produksi;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $produkModel    = new M_Produk();
        $bahanModel     = new M_BahanBaku();
        $penjualanModel = new M_Penjualan();
        $produksiModel  = new M_Produksi();
        $logModel       = new M_LogActivity();

        $data = [
            'pageTitle' => 'Dashboard',

            // Stat cards
            'totalProduk'      => $produkModel->countActive(),
            'totalLowStock'    => $bahanModel->countLowStock(),
            'totalPenjualanHariIni' => $penjualanModel->countHariIni(),
            'omsetHariIni'     => $penjualanModel->totalHariIni(),
            'omsetBulanIni'    => $penjualanModel->totalBulanIni(),
            'produksiByStatus' => $produksiModel->countByStatus(),

            // Charts
            'revenueChart' => $penjualanModel->getDailyRevenue(7),

            // Recent activity
            'recentLogs' => $logModel->getRecent(10),

            // Low stock list (for the warning table)
            'lowStockItems' => $bahanModel->where('stok <=', 'stok_minimum', false)
                ->orderBy('stok', 'ASC')
                ->findAll(5),
        ];

        return view('dashboard/dashboard_index', $data);
    }
}