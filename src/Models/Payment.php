<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Payment
{
    public static function create(
        int $clientId,
        string $asaasPaymentId,
        string $billingType,
        string $value,
        string $dueDate,
        string $status,
        string $description = '',
        string $invoiceUrl = '',
        string $bankSlipUrl = '',
        string $pixQrCode = ''
    ): int {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO payments (client_id, asaas_payment_id, billing_type, value, due_date, status, description, invoice_url, bank_slip_url, pix_qr_code) VALUES (:client_id, :asaas_payment_id, :billing_type, :value, :due_date, :status, :description, :invoice_url, :bank_slip_url, :pix_qr_code)');
        $stmt->execute([
            'client_id' => $clientId,
            'asaas_payment_id' => $asaasPaymentId,
            'billing_type' => $billingType,
            'value' => $value,
            'due_date' => $dueDate,
            'status' => $status,
            'description' => $description,
            'invoice_url' => $invoiceUrl,
            'bank_slip_url' => $bankSlipUrl,
            'pix_qr_code' => $pixQrCode,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM payments WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function findByClientId(int $clientId): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM payments WHERE client_id = :client_id ORDER BY id DESC');
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


