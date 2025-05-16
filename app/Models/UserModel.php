<?php
namespace App\Models;

use App\Models\Database;

class UserModel
{
    private Database $db;
    private string $table = 'users';

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll(): array
    {
        $stmt = $this->db->execute("SELECT id, nama, no_telp, alamat, email FROM {$this->table}");
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->execute(
            "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1",
            's',
            [$email]
        );
        $user = $stmt->get_result()->fetch_assoc();
        return $user ?: null;
    }

    public function verifyPassword(string $passwordPlain, string $passwordHash): bool
    {
        return password_verify($passwordPlain, $passwordHash);
    }

    public function create(string $nama, string $no_telp, string $alamat, string $email, string $passwordPlain): int
    {
        $hashed = password_hash($passwordPlain, PASSWORD_BCRYPT);
        $this->db->execute(
            "INSERT INTO {$this->table} (nama, no_telp, alamat, email, password) VALUES (?, ?, ?, ?, ?)",
            'sssss',
            [$nama, $no_telp, $alamat, $email, $hashed]
        );
        return $this->db->getConnection()->insert_id;
    }

    public function existsName(string $nama): bool
    {
        $stmt = $this->db->execute(
            "SELECT 1 FROM {$this->table} WHERE nama = ? LIMIT 1",
            's',
            [$nama]
        );
        return (bool)$stmt->get_result()->fetch_row();
    }

    public function update(int $id, string $nama, string $no_telp, string $alamat, string $email, ?string $passwordPlain = null): void
    {
        $fields = ['nama = ?', 'no_telp = ?', 'alamat = ?', 'email = ?'];
        $types  = 'ssssi';
        $params = [$nama, $no_telp, $alamat, $email, $id];

        if ($passwordPlain) {
            // sisipkan sebelum id
            $fields[] = 'password = ?';
            $hash     = password_hash($passwordPlain, PASSWORD_BCRYPT);
            // ubah urutan types dan params: kita pindah id ke akhir setelah semua
            array_splice($params, 4, 0, $hash);
            $types = 'sssss' . 'i';
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $this->db->execute($sql, $types, $params);
    }

    public function delete(int $id): void
    {
        $this->db->execute(
            "DELETE FROM {$this->table} WHERE id = ?",
            'i',
            [$id]
        );
    }
}
