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
     * Allowed roles for the system. Kept here as a single source of truth
     * so controllers/filters can reuse it instead of hardcoding strings.
     *
     * @var list<string>
     */
    public const ROLES = ['admin', 'kasir', 'produser'];

    /**
     * Find an active user by username. Returns null if not found or inactive.
     *
     * @return array<string, mixed>|null
     */
    public function findActiveByUsername(string $username): ?array
    {
        return $this->where('username', $username)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Hash a plain password for storage. Use this before insert/update
     * whenever the password field is being set or changed.
     */
    public function hashPassword(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    /**
     * Verify a plain password against the stored hash for a given user row.
     *
     * @param array<string, mixed> $user A user row (must contain 'password').
     */
    public function verifyPassword(string $plainPassword, array $user): bool
    {
        return isset($user['password']) && password_verify($plainPassword, $user['password']);
    }

    /**
     * Create a new user with the password automatically hashed.
     *
     * @param array<string, mixed> $data Must include 'password' as plain text.
     *
     * @return int|false Insert ID on success, false on validation/db failure.
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
     * Update an existing user. If 'password' is present and non-empty,
     * it will be (re)hashed; if omitted/empty, the existing password
     * is left untouched (common pattern for "edit user" forms).
     *
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