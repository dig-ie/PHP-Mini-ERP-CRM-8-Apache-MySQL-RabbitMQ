<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Order
{
    public static function create(int $clientId, string $totalAmount, string $status = 'pending'): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO orders (client_id, total_amount, status) VALUES (:client_id, :total_amount, :status)');
        $stmt->execute([
            'client_id' => $clientId,
            'total_amount' => $totalAmount,
            'status' => $status,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function findAll(): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query('
            SELECT o.*, c.name as client_name 
            FROM orders o 
            LEFT JOIN clients c ON o.client_id = c.id 
            ORDER BY o.created_at DESC
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


