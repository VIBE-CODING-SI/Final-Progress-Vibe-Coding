<?php
namespace App\Controllers;

use App\Models\ArmadaModel;
use Exception;

class ArmadaController
{
    private ArmadaModel $armadaModel;

    public function __construct()
    {
        $this->armadaModel = new ArmadaModel();
    }

    public function index(): void
    {
        try {
            // Cek login
            if (empty($_SESSION['user_id'])) {
                $this->redirectWithMessage('/login', 'error', 'Silakan login terlebih dahulu');
                return;
            }

            // Ambil data
            $armada = $this->armadaModel->getAll();
            $statuses = $this->armadaModel->getStatuses();
            $types = $this->armadaModel->getTypes();

            // Load view
            include __DIR__ . '/../views/backsite/layouts/header.php';
            include __DIR__ . '/../views/backsite/layouts/sidebar.php';
            include __DIR__ . '/../views/backsite/layouts/topbar.php';
            
            echo '<main class="p-6">';
            include __DIR__ . '/../views/backsite/armada/index.php';
            echo '</main>';
            
            include __DIR__ . '/../views/backsite/layouts/footer.php';

        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard', 'error', 'Gagal memuat data armada: ' . $e->getMessage());
        }
    }

    public function store(): void
    {
        try {
            $requiredFields = ['plat_kendaraan', 'nama_kendaraan', 'tipe_kendaraan',
                              'kapasitas_kendaraan', 'harga_sewa', 'status_kendaraan'];
            $this->validateInput($requiredFields);

            // Handle file upload
            $gambar = $this->handleFileUpload();

            // Create armada
            $this->armadaModel->create(
                $_POST['plat_kendaraan'],
                $_POST['nama_kendaraan'],
                $_POST['tipe_kendaraan'],
                (float)$_POST['kapasitas_kendaraan'],
                (float)$_POST['harga_sewa'],
                $_POST['status_kendaraan'],
                $gambar
            );

            $this->redirectWithMessage('/dashboard/armada', 'success', 'Armada berhasil ditambahkan');

        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard/armada', 'error', $e->getMessage());
        }
    }

    public function update(int $id): void
    {
        try {
            $existing = $this->armadaModel->findById($id);
            if (!$existing) {
                throw new Exception('Data armada tidak ditemukan');
            }

            $requiredFields = ['plat_kendaraan', 'nama_kendaraan', 'tipe_kendaraan',
                            'kapasitas_kendaraan', 'harga_sewa', 'status_kendaraan'];
            $this->validateInput($requiredFields);

            // Handle file upload jika ada
            $gambar = $this->handleFileUpload($existing['gambar_kendaraan']);

            // Update data
            $this->armadaModel->update(
                $id,
                $_POST['plat_kendaraan'],
                $_POST['nama_kendaraan'],
                $_POST['tipe_kendaraan'],
                (float)$_POST['kapasitas_kendaraan'],
                (float)$_POST['harga_sewa'],
                $_POST['status_kendaraan'],
                $gambar
            );

            $this->redirectWithMessage('/dashboard/armada', 'success', 'Data armada berhasil diperbarui');

        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard/armada', 'error', $e->getMessage());
        }
    }

    public function destroy(int $id): void
    {
        try {
            // Validasi keberadaan data
            $armada = $this->armadaModel->findById($id);
            if (!$armada) {
                throw new Exception('Data armada tidak ditemukan');
            }

            if (!empty($existing['gambar_kendaraan'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/armada/';
                $filePath = $uploadDir . $existing['gambar_kendaraan'];
                
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $this->armadaModel->delete($id);
            $this->redirectWithMessage('/dashboard/armada', 'success', 'Armada berhasil dihapus');

        } catch (Exception $e) {
            $this->redirectWithMessage('/dashboard/armada', 'error', $e->getMessage());
        }
    }

    private function validateInput(array $fields): void
    {
        foreach ($fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Field {$field} wajib diisi");
            }
        }

        // Validasi numerik
        if (!is_numeric($_POST['kapasitas_kendaraan'])) {
            throw new Exception("Kapasitas harus berupa angka");
        }

        if (!is_numeric($_POST['harga_sewa'])) {
            throw new Exception("Harga sewa harus berupa angka");
        }

        // Validasi enum
        $validStatus = $this->armadaModel->getStatuses();
        if (!in_array($_POST['status_kendaraan'], $validStatus)) {
            throw new Exception("Status kendaraan tidak valid");
        }

        $validTypes = $this->armadaModel->getTypes();
        if (!in_array($_POST['tipe_kendaraan'], $validTypes)) {
            throw new Exception("Tipe kendaraan tidak valid");
        }
    }

    private function handleFileUpload(?string $existingFile = null): ?string
    {
        $uploadDir = __DIR__ . '/../../public/uploads/armada/';

        if (!empty($_FILES['gambar_kendaraan']['name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/armada/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = uniqid() . '_' . basename($_FILES['gambar_kendaraan']['name']);
            $targetPath = $uploadDir . $fileName;

            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileType = mime_content_type($_FILES['gambar_kendaraan']['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Hanya file gambar (JPEG, PNG, WEBP) yang diperbolehkan');
            }

            if (move_uploaded_file($_FILES['gambar_kendaraan']['tmp_name'], $targetPath)) {
                if ($existingFile && file_exists($uploadDir . $existingFile)) {
                    unlink($uploadDir . $existingFile);
                }
                return $fileName;
            }

            if ($existingFile && file_exists($uploadDir . $existingFile)) {
                unlink($uploadDir . $existingFile);
            }
            
            throw new Exception('Gagal mengupload gambar');
        }
        return $existingFile;
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

            $data = $this->armadaModel->getPaginated(
                $request['length'],
                $request['start'],
                $request['search']
            );

            $total = $this->armadaModel->countAll($request['search']);

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

    private function redirectWithMessage(string $url, string $type, string $message): void
    {
        $_SESSION[$type] = $message;
        header("Location: {$url}");
        exit;
    }

    public function categoryTronton()
    {
        $capacityFilter = $_GET['capacity'] ?? 'Semua';
        $priceFilter = $_GET['price'] ?? 'Semua';
        $statusFilter = $_GET['status'] ?? 'Semua';

        $trontonList = $this->armadaModel->getFilteredTrontonArmada($capacityFilter, $priceFilter, $statusFilter);

        // Mapping warna untuk status kendaraan
        $statusColors = [
            'tersedia' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
            'nonaktif' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
            'digunakan' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
            'maintenance' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
        ];

        // Tambahkan kelas warna ke setiap armada
        foreach ($trontonList as &$armada) {
            $status = strtolower($armada['status_kendaraan']);
            $armada['bgClass'] = $statusColors[$status]['bg'] ?? 'bg-gray-100';
            $armada['textClass'] = $statusColors[$status]['text'] ?? 'text-gray-800';
        }
        unset($armada);

        include __DIR__ . '/../views/layouts/header.php';

        echo '<main class="p-6">';
        include __DIR__ . '/../views/layanan/tronton/category_tronton.php';
        echo '</main>';

        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function showDetail($id) {
        $armada = $this->armadaModel->findById($id);
        if (!$armada) {
            http_response_code(404);
            echo "Armada tidak ditemukan";
            return;
        }

        // Misal passing data ke view dengan cara include
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/layanan/tronton/detail_tronton.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function categoryTrailer()
    {
        $capacityFilter = $_GET['capacity'] ?? 'Semua';
        $priceFilter = $_GET['price'] ?? 'Semua';
        $statusFilter = $_GET['status'] ?? 'Semua';

        // Ambil data armada trailer yang difilter
        $trailerList = $this->armadaModel->getFilteredTrailerArmada($capacityFilter, $priceFilter, $statusFilter);

        $statusColors = [
            'tersedia' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
            'nonaktif' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
            'digunakan' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
            'maintenance' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
        ];

        foreach ($trailerList as &$armada) {
            $status = strtolower($armada['status_kendaraan']);
            $armada['bgClass'] = $statusColors[$status]['bg'] ?? 'bg-gray-100';
            $armada['textClass'] = $statusColors[$status]['text'] ?? 'text-gray-800';
        }
        unset($armada);

        include __DIR__ . '/../views/layouts/header.php';

        echo '<main class="p-6">';
        include __DIR__ . '/../views/layanan/trailer/category_trailer.php';
        echo '</main>';

        include __DIR__ . '/../views/layouts/footer.php';
    }

}