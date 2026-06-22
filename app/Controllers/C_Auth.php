<?php

namespace App\Controllers;

use App\Models\M_User;

class C_Auth extends BaseController
{
    protected M_User $userModel;

    public function __construct()
    {
        $this->userModel = new M_User();
    }

    public function login()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findActiveByUsername($username);

        if ($user === null || ! $this->userModel->verifyPassword($password, $user)) {
            return redirect()->back()
                ->withInput()
                ->with('loginError', 'Username atau password salah.');
        }

        session()->regenerate();

        $this->session->set([
            'id_user'     => $user['id_user'],
            'username'    => $user['username'],
            'nama_user'   => $user['nama_user'],
            'role'        => $user['role'],
            'foto_profil' => $user['foto_profil'],
            'isLoggedIn'  => true,
        ]);

        logActivity('Login ke sistem', 'Auth', $user['id_user']);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        $idUser = $this->currentUserId();

        if ($idUser !== null) {
            logActivity('Logout dari sistem', 'Auth', $idUser);
        }

        $this->session->destroy();

        return redirect()->to('/login')->with('message', 'Anda telah logout.');
    }
}