<?php
namespace App\Controllers;

use App\Models\ArmadaModel;
use App\Models\TransaksiModel;
use Exception;
use DateTime;

class OrderController
{
    private ArmadaModel $armadaModel;
    private TransaksiModel $transaksiModel;

    public function __construct()
    {
        $this->armadaModel = new ArmadaModel();
        $this->transaksiModel = new TransaksiModel();
    }

    public function index(int $id): void
    {
        try {
            $armada = $this->armadaModel->findById($id);
            if (!$armada) {
                throw new Exception('Armada tidak ditemukan');
            }

            include __DIR__ . '/../views/layouts/header.php';
            include __DIR__ . '/../views/layanan/order/index.php';
            include __DIR__ . '/../views/layouts/footer.php';

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /");
            exit;
        }
    }

    public function store(int $id): void
    {
        try {
            $this->validateOrder($_POST); // Validasi tanpa armada_id
            $armada = $this->armadaModel->findById($id);

            // Generate kode booking
            $kodeBooking = 'TRX-' . date('Ymd') . '-' . strtoupper(uniqid());

            $this->transaksiModel->create([
                'tanggal' => date('Y-m-d'),
                'customer' => $_POST['nama'],
                'tanggal_mulai' => $_POST['tanggal_mulai'], // Tambahkan ini
                'tanggal_selesai' => $_POST['tanggal_selesai'], // Tambahkan ini
                'lokasi_penjemputan' => $_POST['lokasi_penjemputan'], // Tambahkan ini
                'armada_id' => $id,
                'nomor_container' => $_POST['nomor_container'],
                'nama_kapal' => $_POST['nama_kapal'],
                'nominal_yang_dibayarkan' => $this->calculateTotal(
                    $armada['harga_sewa'],
                    $_POST['tanggal_mulai'],
                    $_POST['tanggal_selesai']
                ),
                'nomor_referensi' => $kodeBooking,
                'status_pembayaran' => 'belum_lunas',
                'status_transaksi' => 'belum_dimulai'
            ]);

            $_SESSION['current_booking'] = $kodeBooking;
            header("Location: /payment/{$kodeBooking}");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /order/{$id}");
            exit;
        }
    }

    public function payment(int $id): void
    {
        try {
            if (empty($_SESSION['current_booking'])) {
                throw new Exception('Session booking tidak ditemukan');
            }
            
            $transaksi = $this->transaksiModel->findByReference($_SESSION['current_booking']);
            if (!$transaksi) {
                throw new Exception('Data transaksi tidak ditemukan');
            }

            include __DIR__ . '/../views/layouts/header.php';
            include __DIR__ . '/../views/layanan/order/payment.php';
            include __DIR__ . '/../views/layouts/footer.php';

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /order/{$id}");
            exit;
        }
    }

    public function paymentByReference(string $reference): void
    {
        try {
            if (empty($_SESSION['current_booking']) || $_SESSION['current_booking'] !== $reference) {
                throw new Exception("Akses ditolak atau sesi tidak valid");
            }

            $transaksi = $this->transaksiModel->findByReference($reference);
            if (!$transaksi) {
                throw new Exception('Transaksi tidak ditemukan');
            }

            $id = $transaksi['armada_id'];
            $armada = $this->armadaModel->findById($id);

            // Hitung DPP dan PPM secara langsung dari nominal
            $transaksi['dpp'] = $this->calculateDPP($transaksi['nominal_yang_dibayarkan']);
            $transaksi['ppm'] = $this->calculatePPM($transaksi['nominal_yang_dibayarkan']);

            renderView(
                'layanan/order/payment',
                true,
                [
                    'transaksi' => $transaksi,
                    'armada' => $armada
                ]
            );

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /");
            exit;
        }
    }

    public function processPayment(string $reference): void
    {
        try {
            // Validasi session
            if (empty($_SESSION['current_booking']) || $_SESSION['current_booking'] !== $reference) {
                throw new Exception("Akses tidak sah");
            }
            
            // Get transaction data first
            $transaksi = $this->transaksiModel->findByReference($reference);
            if (!$transaksi) {
                throw new Exception("Transaksi tidak ditemukan");
            }
            
            $this->validatePayment($_POST);
            
            $data = [
                'dpp' => $this->calculateDPP($transaksi['nominal_yang_dibayarkan']),
                'ppm' => $this->calculatePPM($transaksi['nominal_yang_dibayarkan'])
            ];

            if ($_POST['metode'] === 'transfer') {
                $uploadDir = __DIR__ . '/../../public/uploads/payments/';
                $fileName = uniqid() . '_' . basename($_FILES['bukti_pembayaran']['name']);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $targetPath)) {
                    $data['bukti_pembayaran'] = $fileName;
                } else {
                    throw new Exception("Gagal mengupload bukti pembayaran");
                }
            }

            $this->transaksiModel->updateByReference($reference, $data);
        
            header("Location: /order/invoice/{$reference}");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /payment/{$reference}");
            exit;
        }
    }

    private function validatePayment(array $data): void
    {
        if (empty($data['metode'])) {
            throw new Exception("Pilih metode pembayaran");
        }
        
        if ($data['metode'] === 'transfer') {
            if (empty($_FILES['bukti_pembayaran']['name'])) {
                throw new Exception("Harap upload bukti transfer");
            }
            
            // Validasi tipe file
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            $fileType = $_FILES['bukti_pembayaran']['type'];
            
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Hanya menerima file JPEG, PNG, atau PDF");
            }
        }
    }

    public function invoice(string $reference): void
    {
        try {
            if (empty($_SESSION['current_booking']) || $_SESSION['current_booking'] !== $reference) {
                throw new Exception("Akses invoice tidak diizinkan");
            }

            $transaksi = $this->transaksiModel->findByReference($reference);
            if (!$transaksi) {
                throw new Exception("Transaksi tidak ditemukan");
            }

            // Tambahkan ini untuk mendapatkan data armada
            $armada = $this->armadaModel->findById($transaksi['armada_id']);
            if (!$armada) {
                throw new Exception("Data armada tidak ditemukan");
            }

            unset($_SESSION['current_booking']);

            renderView(
                'layanan/order/invoice',
                true,
                [
                    'transaksi' => $transaksi,
                    'armada' => $armada // Kirim data armada ke view
                ]
            );

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /");
            exit;
        }
    }

    private function validateOrder(array $data): void
    {
        $required = [
            'nama', 
            'nomor_container', 
            'nama_kapal',
            'tanggal_mulai', 
            'tanggal_selesai'
        ];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field {$field} wajib diisi");
            }
        }

        $startDate = strtotime($data['tanggal_mulai']);
        $endDate = strtotime($data['tanggal_selesai']);
        
        if ($startDate === false || $endDate === false) {
            throw new Exception("Format tanggal tidak valid");
        }
        
        if ($endDate < $startDate) {
            throw new Exception("Tanggal selesai tidak boleh sebelum tanggal mulai");
        }
    }

    private function calculateTotal($hargaSewa, $startDate, $endDate): float
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        
        if ($start > $end) {
            throw new Exception("Tanggal selesai tidak valid");
        }
        
        $interval = $start->diff($end);
        $days = $interval->days + 1; // Hari pertama dihitung 1 hari
        
        return $hargaSewa * $days;
    }

    private function calculateDPP($total): float
    {
        return $total * 0.2; // DPP 20%
    }

    private function calculatePPM($total): float
    {
        return $total * 0.11; // PPM 11%
    }
}