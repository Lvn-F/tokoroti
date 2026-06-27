<?php

namespace App\Controllers;

use App\Models\M_BahanBaku;
use App\Models\M_BeliBahan;

class C_BeliBahan extends BaseController
{
    protected M_BeliBahan $model;
    protected M_BahanBaku $bahanModel;

    public function __construct()
    {
        $this->model      = new M_BeliBahan();
        $this->bahanModel = new M_BahanBaku();
    }

    public function index(): string
    {
        $keyword = $this->request->getGet('q') ?? '';
        $perPage = 10;

        $data = [
            'pageTitle' => 'Pembelian Bahan Baku',
            'keyword'   => $keyword,
            'pembelian' => $this->model->search($keyword, $perPage),
            'pager'     => $this->model->pager,
        ];

        return view('belibahan/belibahan_index', $data);
    }

    public function create(): string
    {
        return view('belibahan/belibahan_create', [
            'pageTitle'   => 'Tambah Pembelian Bahan Baku',
            'bahans'      => $this->bahanModel->orderBy('nama_bahan', 'ASC')->findAll(),
            'satuanBeli'  => M_BeliBahan::SATUAN_BELI,
            'satuanDasar' => M_BahanBaku::SATUAN,
            'conversion'  => M_BeliBahan::CONVERSION,
        ]);
    }

    public function store()
    {
        $mode = $this->request->getPost('mode'); // 'existing' | 'new'

        $rules = [
            'satuan_beli'  => 'required|in_list[kg,gram,liter,ml,butir]',
            'jumlah_beli'  => 'required|decimal|greater_than[0]',
            'total_harga'  => 'required|decimal|greater_than[0]',
            'tanggal_beli' => 'required|valid_date',
        ];

        if ($mode === 'new') {
            $rules['nama_bahan_baru']   = 'required|max_length[100]';
            $rules['satuan_dasar_baru'] = 'required|in_list[gram,ml,butir]';
            $rules['stok_minimum_baru'] = 'permit_empty|decimal';
        } else {
            $rules['id_bahan'] = 'required|integer';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $satuanBeli  = $this->request->getPost('satuan_beli');
        $jumlahBeli  = (float) $this->request->getPost('jumlah_beli');
        $totalHarga  = (float) $this->request->getPost('total_harga');
        $tanggalBeli = $this->request->getPost('tanggal_beli');

        // --- Resolve which bahan_baku row this purchase targets ---
        if ($mode === 'new') {
            $satuanDasarBaru = $this->request->getPost('satuan_dasar_baru');

            if (! $this->model->isCompatible($satuanBeli, $satuanDasarBaru)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Satuan beli '{$satuanBeli}' tidak cocok dengan satuan dasar '{$satuanDasarBaru}' yang dipilih.");
            }

            $kodeBahan = $this->bahanModel->generateKodeBahan();

            $this->bahanModel->insert([
                'kode_bahan'   => $kodeBahan,
                'nama_bahan'   => $this->request->getPost('nama_bahan_baru'),
                'satuan'       => $satuanDasarBaru,
                'stok'         => 0,
                'stok_minimum' => $this->request->getPost('stok_minimum_baru') ?: 0,
                'harga_beli'   => 0,
            ]);

            $idBahan = $this->bahanModel->getInsertID();

            $this->logActivity(
                "Mendaftarkan bahan baku baru: {$kodeBahan} - " . $this->request->getPost('nama_bahan_baru'),
                'Bahan Baku'
            );

            $bahan = $this->bahanModel->find($idBahan);
        } else {
            $bahan = $this->bahanModel->find($this->request->getPost('id_bahan'));

            if ($bahan === null) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Bahan baku yang dipilih tidak ditemukan.');
            }
        }

        // --- Record the purchase (this also updates stok + harga_beli) ---
        $result = $this->model->recordPurchase(
            $bahan,
            $satuanBeli,
            $jumlahBeli,
            $totalHarga,
            $tanggalBeli,
            $this->currentUserId()
        );

        if (! $result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['error']);
        }

        $this->logActivity(
            "Pembelian bahan baku: {$result['kodeBeli']} - {$bahan['nama_bahan']} ({$jumlahBeli} {$satuanBeli}, Rp " . number_format($totalHarga, 0, ',', '.') . ')',
            'Beli Bahan'
        );

        return redirect()->to('/beli-bahan')
            ->with('success', "Pembelian berhasil disimpan. Stok {$bahan['nama_bahan']} bertambah, harga beli diperbarui ke per-{$bahan['satuan']}.");
    }

    /**
     * AJAX endpoint: given an id_bahan, return its base unit + compatible
     * purchase units, so the create form can filter the satuan_beli dropdown.
     */
    public function compatibleUnits(int $idBahan)
    {
        $bahan = $this->bahanModel->find($idBahan);

        if ($bahan === null) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Bahan tidak ditemukan']);
        }

        $compatible = array_keys(array_filter(
            M_BeliBahan::CONVERSION,
            static fn ($c) => $c['base'] === $bahan['satuan']
        ));

        return $this->response->setJSON([
            'satuan_dasar' => $bahan['satuan'],
            'satuan_beli'  => $compatible,
        ]);
    }
}