<?php
namespace App\Controllers;

use App\Models\UserModel;
use Exception;

class UserController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        // $this->startSession();
    }

    // private function startSession(): void
    // {
    //     if (session_status() === PHP_SESSION_NONE) {
    //         session_start();
    //     }
    // }

    public function index(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirectWithMessage('/login', 'error', 'Silakan login terlebih dahulu');
        }

        try {
            $users = $this->userModel->getAll();
            
            include __DIR__ . '/../views/backsite/layouts/header.php';
            include __DIR__ . '/../views/backsite/layouts/sidebar.php';
            include __DIR__ . '/../views/backsite/layouts/topbar.php';

            echo '<main class="p-6">';
            include __DIR__ . '/../views/backsite/users/index.php';
            echo '</main>';

            include __DIR__ . '/../views/backsite/layouts/footer.php';

        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard', 'error', 'Gagal memuat data user: ' . $e->getMessage());
        }
    }

    public function store(): void
    {
        try {
            if (headers_sent()) {
                throw new Exception('Headers already sent');
            }
            
            $requiredFields = ['nama', 'no_telp', 'alamat', 'email', 'password'];
            $this->validateInput($requiredFields);

            $this->userModel->create(
                $_POST['nama'],
                $_POST['no_telp'],
                $_POST['alamat'],
                $_POST['email'],
                $_POST['password']
            );

            $this->redirectWithMessage('/dashboard/users', 'success', 'User berhasil ditambahkan');

        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard/users', 'error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function update(int $id): void
    {
        try {
            $requiredFields = ['nama', 'no_telp', 'alamat', 'email'];
            $this->validateInput($requiredFields);

            $this->userModel->update(
                $id,
                $_POST['nama'],
                $_POST['no_telp'],
                $_POST['alamat'],
                $_POST['email'],
                $_POST['password'] ?? null
            );

            $this->redirectWithMessage('/dashboard/users', 'success', 'Data user berhasil diperbarui');

        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard/users', 'error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): void
    {
        try {
            $this->userModel->delete($id);
            $this->redirectWithMessage('/dashboard/users', 'success', 'User berhasil dihapus');

        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard/users', 'error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    private function validateInput(array $fields): void
    {
        foreach ($fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Field {$field} tidak boleh kosong");
            }
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format email tidak valid");
        }
    }

    private function redirectWithMessage(string $url, string $type, string $message): void
    {
        $_SESSION[$type] = $message;
        header("Location: {$url}");
        exit;
    }
}