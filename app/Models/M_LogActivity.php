<?php

namespace App\Models;

use CodeIgniter\Model;

class M_LogActivity extends Model
{
    protected $table            = 'log_activity';
    protected $primaryKey       = 'id_log';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_user',
        'aktivitas',
        'modul',
        'ip_address',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules = [
        'id_user'   => 'required|integer',
        'aktivitas' => 'required|max_length[255]',
        'modul'     => 'required|max_length[50]',
    ];

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRecent(int $limit = 20, ?int $idUser = null, ?string $modul = null): array
    {
        $builder = $this->select('log_activity.*, users.nama_user, users.role')
            ->join('users', 'users.id_user = log_activity.id_user', 'left')
            ->orderBy('log_activity.created_at', 'DESC');

        if ($idUser !== null) {
            $builder->where('log_activity.id_user', $idUser);
        }

        if ($modul !== null && $modul !== '') {
            $builder->where('log_activity.modul', $modul);
        }

        return $builder->findAll($limit);
    }
}