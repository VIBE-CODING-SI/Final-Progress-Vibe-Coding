<?php
namespace App\Controllers;

use App\Models\TransaksiModel;
use Exception;

class TransaksiController
{
    private TransaksiModel $transaksiModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->transaksiModel = new TransaksiModel();
    }

    public function index(): void
    {
        try {
            if (empty($_SESSION['user_id'])) {
                $this->redirectWithMessage('/login', 'error', 'Silakan login terlebih dahulu');
                return;
            }

            $statuses = $this->transaksiModel->getStatusTransaksi();
            
            include __DIR__ . '/../views/backsite/layouts/header.php';
            include __DIR__ . '/../views/backsite/layouts/sidebar.php';
            include __DIR__ . '/../views/backsite/layouts/topbar.php';
            
            echo '<main class="p-6">';
            include __DIR__ . '/../views/backsite/transaksi/index.php';
            echo '</main>';
            
            include __DIR__ . '/../views/backsite/layouts/footer.php';

        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard/transaksi', 'error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    public function datatable()
    {
        try {
            $request = [
                'draw' => $_GET['draw'] ?? 1,
                'start' => $_GET['start'] ?? 0,
                'length' => $_GET['length'] ?? 10,
                'search' => $_GET['search']['value'] ?? ''
            ];

            $data = $this->transaksiModel->getPaginatedTransactions(
                $request['length'],
                $request['start'],
                $request['search']
            );

            $total = $this->transaksiModel->countAll($request['search']);

            header('Content-Type: application/json');
            echo json_encode([
                "draw" => intval($request['draw']),
                "recordsTotal" => $total,
                "recordsFiltered" => $total,
                "data" => $data
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    public function show(int $id)
    {
        try {
            $transaksi = $this->transaksiModel->findById($id);
            
            if (!$transaksi) {
                throw new Exception('Data transaksi tidak ditemukan');
            }

            // Tambahkan konversi tanggal
            $transaksi['tanggal_mulai'] = date('d/m/Y', strtotime($transaksi['tanggal_mulai']));
            $transaksi['tanggal_selesai'] = date('d/m/Y', strtotime($transaksi['tanggal_selesai']));

            header('Content-Type: application/json');
            echo json_encode($transaksi);
            exit;

        } catch (Exception $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    public function updatePaymentStatus(int $id): void
    {
        try {
            $this->validatePaymentStatus($_POST);
            
            $this->transaksiModel->updatePaymentStatus(
                $id,
                $_POST['status_pembayaran']
            );

            $this->redirectWithMessage('/dashboard/transaksi', 'success', 'Status pembayaran berhasil diperbarui');
            
        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard/transaksi', 'error', $e->getMessage());
        }
    }

    public function updateTransactionStatus(int $id): void
    {
        try {
            $this->validateTransactionStatus($_POST);
            
            $this->transaksiModel->updateTransactionStatus(
                $id,
                $_POST['status_transaksi']
            );

            $this->redirectWithMessage('/dashboard/transaksi', 'success', 'Status transaksi berhasil diperbarui');
            
        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard/transaksi', 'error', $e->getMessage());
        }
    }

    private function validatePaymentStatus(array $data): void
    {
        $validStatus = $this->transaksiModel->getStatusPembayaran();
        
        if (!in_array($data['status_pembayaran'], $validStatus)) {
            throw new Exception("Status pembayaran tidak valid");
        }
    }

    private function validateTransactionStatus(array $data): void
    {
        $validStatus = $this->transaksiModel->getStatusTransaksi();
        
        if (!in_array($data['status_transaksi'], $validStatus)) {
            throw new Exception("Status transaksi tidak valid");
        }
    }

    private function validateStatus(array $data): void
    {
        $validPembayaran = $this->transaksiModel->getStatusPembayaran();
        $validTransaksi = $this->transaksiModel->getStatusTransaksi();
        
        if (!in_array($data['status_pembayaran'], $validPembayaran)) {
            throw new Exception("Status pembayaran tidak valid");
        }
        
        if (!in_array($data['status_transaksi'], $validTransaksi)) {
            throw new Exception("Status transaksi tidak valid");
        }
    }

    private function redirectWithMessage(string $url, string $type, string $message): void
    {
        $_SESSION[$type] = $message;
        header("Location: {$url}");
        exit;
    }
}