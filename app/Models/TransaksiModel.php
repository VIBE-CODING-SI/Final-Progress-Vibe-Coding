<?php
namespace App\Models;

use App\Models\Database;

class TransaksiModel
{
    private Database $db;
    private string $table = 'transaksi';

    public function __construct()
    {
        $this->db = new Database();
    }

    private function getType($value): string
    {
        if (is_int($value)) {
            return 'i';
        } elseif (is_double($value) || is_float($value)) {
            return 'd';
        } else {
            return 's';
        }
    }

    public function getAll(): array
    {
        $stmt = $this->db->execute(
            "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY tanggal DESC"
        );
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getOngoing(): array
    {
        $stmt = $this->db->execute(
            "SELECT * FROM {$this->table} 
            WHERE (status_transaksi != 'selesai' OR created_at > NOW() - INTERVAL 3 DAY)
            AND deleted_at IS NULL
            ORDER BY created_at DESC"
        );
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function create(array $data): int
    {
        $query = "INSERT INTO {$this->table} 
            (tanggal, customer, armada_id, nomor_container, nama_kapal, 
            tanggal_mulai, tanggal_selesai, lokasi_penjemputan,
            nominal_yang_dibayarkan, dpp, ppm, nomor_referensi, 
            status_pembayaran, status_transaksi, bukti_pembayaran)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['tanggal'],
            $data['customer'],
            $data['armada_id'],
            $data['nomor_container'],
            $data['nama_kapal'],
            $data['tanggal_mulai'],
            $data['tanggal_selesai'],
            $data['lokasi_penjemputan'],
            $data['nominal_yang_dibayarkan'],
            $data['dpp'] ?? 0,
            $data['ppm'] ?? 0,
            $data['nomor_referensi'],
            $data['status_pembayaran'],
            $data['status_transaksi'],
            $data['bukti_pembayaran'] ?? null
        ];

        // SESUAIKAN TYPE STRING (14 PARAMETER: 13 string + 1 integer)
        $stmt = $this->db->execute($query, 'ssisssssddsssss', $params);
        return $this->db->getConnection()->insert_id;
    }

    public function findByReference(string $reference): ?array
    {
        $query = "SELECT 
                    t.*, 
                    a.harga_sewa,
                    DATEDIFF(t.tanggal_selesai, t.tanggal_mulai) + 1 AS durasi
                FROM {$this->table} t
                JOIN armada a ON t.armada_id = a.id
                WHERE t.nomor_referensi = ? 
                LIMIT 1";
        
        $stmt = $this->db->execute($query, 's', [$reference]);
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function updateByReference(string $reference, array $data): void
    {
        $fields = [];
        $types = '';
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $types .= $this->getType($value);
            $values[] = $value;
        }
        
        $values[] = $reference;
        
        $query = "UPDATE {$this->table} 
                SET " . implode(', ', $fields) . " 
                WHERE nomor_referensi = ?";
        
        $this->db->execute($query, $types . 's', $values);
    }


    public function findById(int $id): ?array
    {
        $query = "SELECT 
                    t.*, 
                    a.nama_kendaraan,
                    a.plat_kendaraan,
                    DATEDIFF(t.tanggal_selesai, t.tanggal_mulai) + 1 AS durasi
                FROM {$this->table} t
                LEFT JOIN armada a ON t.armada_id = a.id
                WHERE t.id = ? 
                LIMIT 1";
        
        $stmt = $this->db->execute($query, 'i', [$id]);
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function updateStatus(int $id, array $status): void
    {
        $this->db->execute(
            "UPDATE {$this->table} 
            SET status_pembayaran = ?, 
                status_transaksi = ? 
            WHERE id = ?",
            'ssi',
            [$status['pembayaran'], $status['transaksi'], $id]
        );
    }

    public function updatePaymentStatus(int $id, string $status): void
    {
        $this->db->execute(
            "UPDATE {$this->table} 
            SET status_pembayaran = ?
            WHERE id = ?",
            'si',
            [$status, $id]
        );
    }

    public function updateTransactionStatus(int $id, string $status): void
    {
        $this->db->execute(
            "UPDATE {$this->table} 
            SET status_transaksi = ?
            WHERE id = ?",
            'si',
            [$status, $id]
        );
    }

    public function importFromCSV(string $file): void
    {
        $handle = fopen($file, 'r');
        $header = fgetcsv($handle);
        
        while(($row = fgetcsv($handle)) !== FALSE) {
            $this->db->execute(
                "INSERT INTO {$this->table} 
                (tanggal, customer, nomor_container, nama_kapal, 
                nominal_yang_dibayarkan, dpp, ppm, nomor_referensi)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                'ssssddds',
                [
                    $row[0], $row[1], $row[2], $row[3],
                    (float)$row[4], (float)$row[5], (float)$row[6], $row[7]
                ]
            );
        }
        
        fclose($handle);
    }

    public function getStatusPembayaran(): array
    {
        return ['lunas', 'belum_lunas'];
    }

    public function getStatusTransaksi(): array
    {
        return ['belum_dimulai', 'diproses', 'selesai'];
    }

    public function getPaginatedSales(int $limit, int $offset, string $search = ''): array
    {
        $query = "SELECT 
                    t.*,
                    a.nama_kendaraan,
                    a.plat_kendaraan,
                    DATEDIFF(t.tanggal_selesai, t.tanggal_mulai) + 1 as durasi
                FROM {$this->table} t
                LEFT JOIN armada a ON t.armada_id = a.id
                WHERE t.deleted_at IS NULL 
                AND (
                    t.status_pembayaran = 'lunas' 
                    OR t.status_transaksi = 'selesai' 
                    OR t.tanggal < DATE_SUB(NOW(), INTERVAL 2 MONTH)
                )
                AND (t.customer LIKE ? OR t.nomor_container LIKE ?)
                ORDER BY t.tanggal DESC
                LIMIT ? OFFSET ?";

        $searchTerm = "%{$search}%";
        $stmt = $this->db->execute($query, 'ssii', [$searchTerm, $searchTerm, $limit, $offset]);
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    
    public function getPaginatedTransactions(int $limit, int $offset, string $search = ''): array
    {
        $query = "SELECT 
                    t.*,
                    a.nama_kendaraan,
                    a.plat_kendaraan,
                    DATEDIFF(t.tanggal_selesai, t.tanggal_mulai) + 1 as durasi
                FROM {$this->table} t
                LEFT JOIN armada a ON t.armada_id = a.id
                WHERE t.deleted_at IS NULL 
                AND t.status_transaksi != 'selesai'
                AND t.tanggal >= DATE_SUB(NOW(), INTERVAL 2 MONTH)
                AND (t.customer LIKE ? OR t.nomor_container LIKE ?)
                ORDER BY t.tanggal DESC
                LIMIT ? OFFSET ?";

        $searchTerm = "%{$search}%";
        $stmt = $this->db->execute($query, 'ssii', [$searchTerm, $searchTerm, $limit, $offset]);
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function countAll(string $search = ''): int
    {
        $query = "SELECT COUNT(*) as total 
            FROM {$this->table} 
            WHERE deleted_at IS NULL 
            AND status_transaksi != 'selesai'
            AND tanggal >= DATE_SUB(NOW(), INTERVAL 2 MONTH)
            AND (customer LIKE ? OR nomor_container LIKE ?)";
        
        $searchTerm = "%{$search}%";
        $stmt = $this->db->execute($query, 'ss', [$searchTerm, $searchTerm]);
        
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    public function countAllSales(string $search = ''): int
    {
        $query = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE deleted_at IS NULL 
                AND (
                    status_pembayaran = 'lunas' 
                    OR status_transaksi = 'selesai' 
                    OR tanggal < DATE_SUB(NOW(), INTERVAL 2 MONTH)
                )
                AND (customer LIKE ? OR nomor_container LIKE ?)";
        $searchTerm = "%{$search}%";
        $stmt = $this->db->execute($query, 'ss', [$searchTerm, $searchTerm]);
        
        return $stmt->get_result()->fetch_assoc()['total'];
    }
    public function getByMonthYear(int $month, int $year): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
        $stmt = $this->db->execute($sql, 'ii', [$month, $year]);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}