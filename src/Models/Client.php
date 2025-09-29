<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Client
{
    public static function create(string $name, string $email = '', string $phone = ''): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO clients (name, email, phone) VALUES (:name, :email, :phone)');
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function findAll(): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query('SELECT * FROM clients ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('DELETE FROM clients WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM clients WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function update(int $id, string $name, string $email = '', string $phone = ''): bool
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE clients SET name = :name, email = :email, phone = :phone WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);
    }
}


