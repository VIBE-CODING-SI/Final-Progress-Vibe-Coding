<?php
namespace App\Controllers;

use App\Models\TransaksiModel;
use Exception;

class PenjualanController
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

            $transaksi = $this->transaksiModel->getAll();

            include __DIR__ . '/../views/backsite/layouts/header.php';
            include __DIR__ . '/../views/backsite/layouts/sidebar.php';
            include __DIR__ . '/../views/backsite/layouts/topbar.php';
            
            echo '<main class="p-6">';
            include __DIR__ . '/../views/backsite/penjualan/index.php';
            echo '</main>';
            
            include __DIR__ . '/../views/backsite/layouts/footer.php';

        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard/penjualan', 'error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    public function import(): void
    {
        try {
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('File CSV tidak valid');
            }

            $file = $_FILES['csv_file']['tmp_name'];
            $fileType = mime_content_type($file);
            
            if (!in_array($fileType, ['text/csv', 'text/plain'])) {
                throw new Exception('Format file harus CSV');
            }

            $this->transaksiModel->importFromCSV($file);
            $this->redirectWithMessage('/dashboard/penjualan', 'success', 'Data berhasil diimpor');

        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard/penjualan', 'error', $e->getMessage());
        }
    }

    private function redirectWithMessage(string $url, string $type, string $message): void
    {
        $_SESSION[$type] = $message;
        header("Location: {$url}");
        exit;
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

            $data = $this->transaksiModel->getPaginatedSales(
                $request['length'],
                $request['start'],
                $request['search']
            );

            $total = $this->transaksiModel->countAllSales($request['search']);

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

            header('Content-Type: application/json');
            echo json_encode($transaksi);
            exit;

        } catch (Exception $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    public function chartData(): void
    {
        try {
            $transaksi = $this->transaksiModel->getAll();

            $monthlyCounts = array_fill(1, 12, 0); // Jan=1 ... Dec=12

            foreach ($transaksi as $trx) {
                $month = (int)date('n', strtotime($trx['tanggal']));
                $monthlyCounts[$month]++;
            }

            header('Content-Type: application/json');
            echo json_encode(array_values($monthlyCounts)); // indexed array
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    public function monthlyChartData(): void
    {
        try {
            // Ambil parameter month dari query string, default bulan ini
            $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
            $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

            // Ambil semua transaksi di bulan dan tahun tersebut
            $transaksi = $this->transaksiModel->getByMonthYear($month, $year);

            // Hitung jumlah hari di bulan ini
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            // Inisialisasi array 0 untuk setiap hari
            $dailySales = array_fill(0, $daysInMonth, 0);

            // Hitung penjualan total per hari (jumlahkan total setiap transaksi)
            foreach ($transaksi as $trx) {
                $day = (int)date('j', strtotime($trx['tanggal'])); // tanggal hari ke berapa
                $dailySales[$day - 1] += isset($trx['total']) ? (float)$trx['total'] : 1;
            }

            header('Content-Type: application/json');
            echo json_encode($dailySales);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
}