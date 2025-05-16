<?php
namespace App\Models;

use App\Models\Database;

class ArmadaModel
{
    private Database $db;
    private string $table = 'armada';

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll(): array
    {
        $stmt = $this->db->execute(
            "SELECT id, plat_kendaraan, nama_kendaraan, tipe_kendaraan, 
                    kapasitas_kendaraan, harga_sewa, status_kendaraan, gambar_kendaraan 
             FROM {$this->table}"
        );
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->execute(
            "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1",
            'i',
            [$id]
        );
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function findByPlat(string $plat): ?array
    {
        $stmt = $this->db->execute(
            "SELECT * FROM {$this->table} WHERE plat_kendaraan = ? LIMIT 1",
            's',
            [$plat]
        );
        $armada = $stmt->get_result()->fetch_assoc();
        return $armada ?: null;
    }

    public function create(
        string $plat,
        string $nama,
        string $tipe,
        float $kapasitas,
        float $harga,
        string $status,
        string $gambar
    ): int {
        $this->db->execute(
            "INSERT INTO {$this->table} 
                (plat_kendaraan, nama_kendaraan, tipe_kendaraan, 
                 kapasitas_kendaraan, harga_sewa, status_kendaraan, gambar_kendaraan) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            'sssdsss',
            [$plat, $nama, $tipe, $kapasitas, $harga, $status, $gambar]
        );
        return $this->db->getConnection()->insert_id;
    }

    public function existsPlat(string $plat): bool
    {
        $stmt = $this->db->execute(
            "SELECT 1 FROM {$this->table} WHERE plat_kendaraan = ? LIMIT 1",
            's',
            [$plat]
        );
        return (bool)$stmt->get_result()->fetch_row();
    }

    public function update(
        int $id,
        string $plat,
        string $nama,
        string $tipe,
        float $kapasitas,
        float $harga,
        string $status,
        ?string $gambar = null
    ): void {
        $query = "UPDATE {$this->table} 
                SET plat_kendaraan = ?, 
                    nama_kendaraan = ?,
                    tipe_kendaraan = ?, 
                    kapasitas_kendaraan = ?,
                    harga_sewa = ?,
                    status_kendaraan = ?" 
                    . ($gambar ? ", gambar_kendaraan = ?" : "") . 
                " WHERE id = ?";
        
        $params = [$plat, $nama, $tipe, $kapasitas, $harga, $status];
        $types = 'sssdss';
        
        if ($gambar) {
            $params[] = $gambar;
            $types .= 's';
        }
        
        $params[] = $id;
        $types .= 'i';
        
        $this->db->execute($query, $types, $params);
    }

    public function delete(int $id): void
    {
        $this->db->execute(
            "DELETE FROM {$this->table} WHERE id = ?",
            'i',
            [$id]
        );
    }

    public function deleteImage(string $filename): void
    {
        $uploadDir = __DIR__ . '/../../public/uploads/armada/';
        $filePath = $uploadDir . $filename;
        
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    public function getStatuses(): array
    {
        return ['tersedia', 'digunakan', 'maintenance', 'nonaktif'];
    }

    public function getTypes(): array
    {
        return ['tronton', 'trailer'];
    }

    public function getTrontonArmada(): array
    {
        $stmt = $this->db->execute(
         "SELECT * FROM armada WHERE tipe_kendaraan = ?",
            's',
            ['tronton']
        );
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $data;
    }

    public function getTrailerArmada(): array
    {
        $stmt = $this->db->execute(
         "SELECT * FROM armada WHERE tipe_kendaraan = ?",
            's',
            ['trailer']
        );
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $data;
    }

    public function getFilteredTrontonArmada(string $capacity = 'Semua', string $price = 'Semua', string $status = 'Semua'): array
    {
        $query = "SELECT * FROM {$this->table} WHERE tipe_kendaraan = ?";
        $params = ['tronton'];
        $types = 's';

        // Filter kapasitas
        if ($capacity !== 'Semua') {
            if ($capacity === '10-15 ton') {
                $query .= " AND kapasitas_kendaraan BETWEEN 10 AND 15";
            } elseif ($capacity === '15-20 ton') {
                $query .= " AND kapasitas_kendaraan BETWEEN 15 AND 20";
            } elseif ($capacity === '20+ ton') {
                $query .= " AND kapasitas_kendaraan > 20";
            }
        }

        // Filter status
        if ($status !== 'Semua') {
            $query .= " AND status_kendaraan = ?";
            $params[] = strtolower($status);
            $types .= 's';
        }

        $stmt = $this->db->execute($query, $types, $params);
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Sort berdasarkan harga
        if ($price === 'Terendah') {
            usort($data, fn($a, $b) => $a['harga_sewa'] <=> $b['harga_sewa']);
        } elseif ($price === 'Tertinggi') {
            usort($data, fn($a, $b) => $b['harga_sewa'] <=> $a['harga_sewa']);
        }

        return $data;
    }

    public function getFilteredTrailerArmada(string $capacity = 'Semua', string $price = 'Semua', string $status = 'Semua'): array
    {
        $query = "SELECT * FROM {$this->table} WHERE tipe_kendaraan = ?";
        $params = ['trailer'];
        $types = 's';

        if ($capacity !== 'Semua') {
            if ($capacity === '10-15 ton') {
                $query .= " AND kapasitas_kendaraan BETWEEN 10 AND 15";
            } elseif ($capacity === '15-20 ton') {
                $query .= " AND kapasitas_kendaraan BETWEEN 15 AND 20";
            } elseif ($capacity === '20+ ton') {
                $query .= " AND kapasitas_kendaraan > 20";
            }
        }

        if ($status !== 'Semua') {
            $query .= " AND status_kendaraan = ?";
            $params[] = strtolower($status);
            $types .= 's';
        }

        $stmt = $this->db->execute($query, $types, $params);
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Sorting berdasarkan harga
        if ($price === 'Terendah') {
            usort($data, fn($a, $b) => $a['harga_sewa'] <=> $b['harga_sewa']);
        } elseif ($price === 'Tertinggi') {
            usort($data, fn($a, $b) => $b['harga_sewa'] <=> $a['harga_sewa']);
        }

        return $data;
    }

}