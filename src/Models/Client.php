<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Client
{
    public static function create(string $name, string $email = '', string $phone = '', string $cpfCnpj = '', string $asaasCustomerId = null): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO clients (name, email, phone, cpf_cnpj, asaas_customer_id) VALUES (:name, :email, :phone, :cpf_cnpj, :asaas_customer_id)');
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'cpf_cnpj' => $cpfCnpj,
            'asaas_customer_id' => $asaasCustomerId,
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

    public static function update(int $id, string $name, string $email = '', string $phone = '', string $cpfCnpj = '', ?string $asaasCustomerId = null): bool
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE clients SET name = :name, email = :email, phone = :phone, cpf_cnpj = :cpf_cnpj, asaas_customer_id = :asaas_customer_id WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'cpf_cnpj' => $cpfCnpj,
            'asaas_customer_id' => $asaasCustomerId,
        ]);
    }

    public static function findByAsaasCustomerId(string $asaasCustomerId): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM clients WHERE asaas_customer_id = :asaas_customer_id LIMIT 1');
        $stmt->execute(['asaas_customer_id' => $asaasCustomerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}


