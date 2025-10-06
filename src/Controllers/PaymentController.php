<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Services\AsaasService;

class PaymentController
{
    private static function ensureAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function showCreate(): void
    {
        self::ensureAuth();
        $clientId = (int)($_GET['client_id'] ?? 0);
        $client = $clientId > 0 ? Client::findById($clientId) : null;
        require __DIR__ . '/../../views/payments/create.php';
    }

    public static function store(): void
    {
        self::ensureAuth();
        $clientId = (int)($_POST['client_id'] ?? 0);
        $billingType = (string)($_POST['billing_type'] ?? 'PIX');
        $value = (string)($_POST['value'] ?? '0');
        $dueDate = (string)($_POST['due_date'] ?? '');
        $description = (string)($_POST['description'] ?? '');

        if ($clientId <= 0 || $value === '0' || $dueDate === '') {
            header('Location: /payments/create?client_id=' . $clientId . '&error=1');
            exit;
        }

        $client = Client::findById($clientId);
        if (!$client) {
            header('Location: /dashboard?error=3');
            exit;
        }

        $asaas = new AsaasService();
        $asaas->setDebugMode(true);

        // Garante que existe customer no Asaas
        $asaasCustomerId = $client['asaas_customer_id'] ?? '';
        if ($asaasCustomerId === '' || $asaasCustomerId === null) {
            // Precisa de name e cpfCnpj
            $cpfCnpj = (string)($client['cpf_cnpj'] ?? '');
            if ($cpfCnpj === '') {
                header('Location: /payments/create?client_id=' . $clientId . '&error=2');
                exit;
            }
            $customerPayload = [
                'name' => $client['name'],
                'cpfCnpj' => $cpfCnpj,
            ];
            if (!empty($client['email'])) { $customerPayload['email'] = $client['email']; }
            if (!empty($client['phone'])) { $customerPayload['phone'] = $client['phone']; }

            $createdCustomer = $asaas->createCustomer($customerPayload);
            $asaasCustomerId = $createdCustomer['id'] ?? '';
            if ($asaasCustomerId === '') {
                header('Location: /payments/create?client_id=' . $clientId . '&error=3');
                exit;
            }
            // Atualiza cliente localmente
            Client::update($clientId, $client['name'], (string)($client['email'] ?? ''), (string)($client['phone'] ?? ''), (string)($client['cpf_cnpj'] ?? ''), $asaasCustomerId);
        }

        // Cria a cobrança
        $paymentRequest = [
            'customer' => $asaasCustomerId,
            'billingType' => $billingType,
            'value' => (float)$value,
            'dueDate' => $dueDate,
        ];
        if ($description !== '') {
            $paymentRequest['description'] = $description;
        }

        $created = $asaas->createPayment($paymentRequest);

        $localId = Payment::create(
            $clientId,
            (string)($created['id'] ?? ''),
            (string)($created['billingType'] ?? $billingType),
            (string)($created['value'] ?? $value),
            (string)($created['dueDate'] ?? $dueDate),
            (string)($created['status'] ?? 'PENDING'),
            (string)($created['description'] ?? $description),
            (string)($created['invoiceUrl'] ?? ''),
            (string)($created['bankSlipUrl'] ?? ''),
            (string)($created['pixQrCode'] ?? '')
        );

        header('Location: /dashboard?payment_id=' . $localId);
        exit;
    }

    public static function showByClient(): void
    {
        self::ensureAuth();
        $clientId = (int)($_GET['client_id'] ?? 0);
        
        if ($clientId <= 0) {
            header('Location: /dashboard?error=1');
            exit;
        }

        $client = Client::findById($clientId);
        if (!$client) {
            header('Location: /dashboard?error=3');
            exit;
        }

        $payments = Payment::findByClientId($clientId);
        require __DIR__ . '/../../views/payments/list.php';
    }
}


