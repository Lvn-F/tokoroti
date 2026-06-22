<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Penjualan extends Model
{
    protected $table            = 'penjualan';
    protected $primaryKey       = 'id_penjualan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'kode_penjualan',
        'no_invoice',
        'nama_customer',
        'telepon_customer',
        'id_kasir',
        'total_harga',
        'metode_bayar',
        'bayar',
        'kembalian',
        'status',
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    public const METODE_BAYAR = ['cash', 'debit', 'credit', 'qris'];
    public const STATUS       = ['pending', 'completed', 'cancel'];

    protected $validationRules = [
        'nama_customer'    => 'permit_empty|max_length[100]',
        'telepon_customer' => 'permit_empty|max_length[20]',
        'metode_bayar'     => 'required|in_list[cash,debit,credit,qris]',
        'bayar'            => 'required|decimal',
    ];

    protected $validationMessages = [
        'metode_bayar' => [
            'required' => 'Metode pembayaran wajib dipilih.',
            'in_list'  => 'Metode pembayaran tidak valid.',
        ],
        'bayar' => [
            'required' => 'Jumlah bayar wajib diisi.',
            'decimal'  => 'Jumlah bayar harus berupa angka.',
        ],
    ];

    /**
     * @return array<int, array<string, mixed>>
     */
    public function search(string $keyword = '', string $status = '', int $perPage = 10): array
    {
        $builder = $this->select('penjualan.*, users.nama_user')
            ->join('users', 'users.id_user = penjualan.id_kasir', 'left');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('penjualan.kode_penjualan', $keyword)
                ->orLike('penjualan.no_invoice', $keyword)
                ->orLike('penjualan.nama_customer', $keyword)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('penjualan.status', $status);
        }

        $builder->orderBy('penjualan.tanggal_penjualan', 'DESC');

        return $this->paginate($perPage);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findWithRelations(int $id): ?array
    {
        return $this->select('penjualan.*, users.nama_user')
            ->join('users', 'users.id_user = penjualan.id_kasir', 'left')
            ->where('penjualan.id_penjualan', $id)
            ->first();
    }

    public function generateKodePenjualan(): string
    {
        $last = $this->select('kode_penjualan')
            ->like('kode_penjualan', 'JL-', 'after')
            ->orderBy('id_penjualan', 'DESC')
            ->first();

        $nextNumber = 1;

        if ($last !== null && preg_match('/JL-(\d+)/', $last['kode_penjualan'], $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'JL-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function generateNoInvoice(): string
    {
        $today = date('Ymd');

        $last = $this->select('no_invoice')
            ->like('no_invoice', "INV-{$today}-", 'after')
            ->orderBy('id_penjualan', 'DESC')
            ->first();

        $nextNumber = 1;

        if ($last !== null && preg_match('/INV-\d+-(\d+)/', $last['no_invoice'], $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return "INV-{$today}-" . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function totalHariIni(): float
    {
        $result = $this->selectSum('total_harga')
            ->where('status', 'completed')
            ->where('DATE(tanggal_penjualan)', date('Y-m-d'))
            ->first();

        return (float) ($result['total_harga'] ?? 0);
    }

    public function totalBulanIni(): float
    {
        $result = $this->selectSum('total_harga')
            ->where('status', 'completed')
            ->where('YEAR(tanggal_penjualan)', date('Y'))
            ->where('MONTH(tanggal_penjualan)', date('n'))
            ->first();

        return (float) ($result['total_harga'] ?? 0);
    }

    public function countHariIni(): int
    {
        return $this->where('status', 'completed')
            ->where('DATE(tanggal_penjualan)', date('Y-m-d'))
            ->countAllResults();
    }

    /**
     * @return array{labels: list<string>, values: list<float>}
     */
    public function getDailyRevenue(int $days = 7): array
    {
        $start = date('Y-m-d', strtotime("-" . ($days - 1) . " days"));

        $rows = $this->select("DATE(tanggal_penjualan) as tgl, SUM(total_harga) as total")
            ->where('status', 'completed')
            ->where('DATE(tanggal_penjualan) >=', $start)
            ->groupBy('DATE(tanggal_penjualan)')
            ->orderBy('tgl', 'ASC')
            ->findAll();

        $byDate = [];
        foreach ($rows as $r) {
            $byDate[$r['tgl']] = (float) $r['total'];
        }

        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d M', strtotime($date));
            $values[] = $byDate[$date] ?? 0;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getByDateRange(string $startDate, string $endDate): array
    {
        return $this->select('penjualan.*, users.nama_user')
            ->join('users', 'users.id_user = penjualan.id_kasir', 'left')
            ->where('penjualan.status', 'completed')
            ->where('DATE(penjualan.tanggal_penjualan) >=', $startDate)
            ->where('DATE(penjualan.tanggal_penjualan) <=', $endDate)
            ->orderBy('penjualan.tanggal_penjualan', 'ASC')
            ->findAll();
    }

    public function sumByDateRange(string $startDate, string $endDate): float
    {
        $result = $this->selectSum('total_harga')
            ->where('status', 'completed')
            ->where('DATE(tanggal_penjualan) >=', $startDate)
            ->where('DATE(tanggal_penjualan) <=', $endDate)
            ->first();

        return (float) ($result['total_harga'] ?? 0);
    }
}