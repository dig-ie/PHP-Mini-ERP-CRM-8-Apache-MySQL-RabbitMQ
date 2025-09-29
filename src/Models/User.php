<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}


