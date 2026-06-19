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

    /**
     * Show the login form. If already logged in, redirect to dashboard.
     */
    public function login()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Handle login form submission.
     */
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
            // Deliberately vague message: don't reveal whether the
            // username exists or the password was wrong.
            // Note: failed attempts aren't written to log_activity here
            // since there's no confirmed id_user to attribute them to.
            return redirect()->back()
                ->withInput()
                ->with('loginError', 'Username atau password salah.');
        }

        // Regenerate session ID on privilege change to prevent session fixation.
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

    /**
     * Log the current user out and destroy the session.
     */
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