<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\OrderService;

class OrderController
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
        require __DIR__ . '/../../views/orders/create.php';
    }

    public static function store(): void
    {
        self::ensureAuth();
        $clientId = (int)($_POST['client_id'] ?? 0);
        $total = (string)($_POST['total_amount'] ?? '0');

        if ($clientId <= 0) {
            header('Location: /orders/create?error=1');
            exit;
        }

        $orderId = OrderService::createAndPublish($clientId, $total, [
            'user_id' => (int)($_SESSION['user_id'] ?? 0),
        ]);

        header('Location: /dashboard?order_id=' . $orderId);
        exit;
    }
}


