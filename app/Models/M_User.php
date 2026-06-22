<?php

namespace App\Models;

use CodeIgniter\Model;

class M_User extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'username',
        'password',
        'nama_user',
        'role',
        'foto_profil',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    protected $validationRules = [
        'username'  => 'required|max_length[50]|is_unique[users.username,id_user,{id_user}]',
        'nama_user' => 'required|max_length[100]',
        'role'      => 'required|in_list[admin,kasir,produser]',
        'status'    => 'permit_empty|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Username sudah digunakan, silakan pilih username lain.',
        ],
    ];

    /**
     * @var list<string>
     */
    public const ROLES = ['admin', 'kasir', 'produser'];

    /**
     * @return array<string, mixed>|null
     */
    public function findActiveByUsername(string $username): ?array
    {
        return $this->where('username', $username)
            ->where('status', 'active')
            ->first();
    }

    public function hashPassword(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    /**
     * @param array<string, mixed> $user A user row (must contain 'password').
     */
    public function verifyPassword(string $plainPassword, array $user): bool
    {
        return isset($user['password']) && password_verify($plainPassword, $user['password']);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return int|false
     */
    public function createUser(array $data): int|false
    {
        if (isset($data['password'])) {
            $data['password'] = $this->hashPassword($data['password']);
        }

        $result = $this->insert($data);

        return $result === false ? false : (int) $this->getInsertID();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateUser(int $idUser, array $data): bool
    {
        if (isset($data['password'])) {
            if ($data['password'] === '' || $data['password'] === null) {
                unset($data['password']);
            } else {
                $data['password'] = $this->hashPassword($data['password']);
            }
        }

        return $this->update($idUser, $data);
    }
}