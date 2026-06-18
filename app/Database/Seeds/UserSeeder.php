<?php

namespace App\Database\Seeds;

use App\Models\M_User;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Creates one user per role so you can log in and test
     * role-based access right away. CHANGE THESE PASSWORDS
     * before deploying anywhere beyond local development.
     */
    public function run()
    {
        $userModel = new M_User();

        $users = [
            [
                'username'  => 'admin',
                'password'  => 'admin123',
                'nama_user' => 'Administrator',
                'role'      => 'admin',
                'status'    => 'active',
            ],
            [
                'username'  => 'kasir1',
                'password'  => 'kasir123',
                'nama_user' => 'Kasir Satu',
                'role'      => 'kasir',
                'status'    => 'active',
            ],
            [
                'username'  => 'produser1',
                'password'  => 'produser123',
                'nama_user' => 'Produser Satu',
                'role'      => 'produser',
                'status'    => 'active',
            ],
        ];

        foreach ($users as $user) {
            $exists = $userModel->where('username', $user['username'])->first();

            if ($exists === null) {
                $userModel->createUser($user);
            }
        }
    }
}