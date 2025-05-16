<?php
namespace App\Controllers;

use App\Models\UserModel;

class AuthController {
    protected $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
        // session_start();
    }

    // Tampilkan form login
    public function showLogin(): void {
        renderView('auth/login', false);
    }

    // Proses login
    public function login(): void {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);
        if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
            // Set session dan redirect
            $_SESSION['user_id'] = $user['id'];
            header('Location: /dashboard');
            exit;
        }

        // Gagal login: kembali ke form dengan pesan error
        $_SESSION['error'] = 'Email atau password salah';
        header('Location: /login');
    }

    // Logout
    public function logout(): void {
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }
}
