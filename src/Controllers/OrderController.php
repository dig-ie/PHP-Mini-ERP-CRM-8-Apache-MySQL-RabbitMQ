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

    public static function delete(): void
    {
        self::ensureAuth();
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: /dashboard?error=1');
            exit;
        }

        $success = \App\Models\Order::delete($id);
        if ($success) {
            header('Location: /dashboard?deleted=order');
        } else {
            header('Location: /dashboard?error=2');
        }
        exit;
    }

    public static function showEdit(): void
    {
        self::ensureAuth();
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: /dashboard?error=1');
            exit;
        }

        $order = \App\Models\Order::findById($id);
        if (!$order) {
            header('Location: /dashboard?error=3');
            exit;
        }

        // Buscar lista de clientes para o select
        $clients = \App\Models\Client::findAll();
        
        require __DIR__ . '/../../views/orders/edit.php';
    }

    public static function update(): void
    {
        self::ensureAuth();
        $id = (int)($_POST['id'] ?? 0);
        $clientId = (int)($_POST['client_id'] ?? 0);
        $total = (string)($_POST['total_amount'] ?? '0');
        $status = (string)($_POST['status'] ?? 'pending');

        if ($id <= 0 || $clientId <= 0) {
            header('Location: /orders/edit?id=' . $id . '&error=1');
            exit;
        }

        $success = \App\Models\Order::update($id, $clientId, $total, $status);
        if ($success) {
            header('Location: /dashboard?updated=order');
        } else {
            header('Location: /orders/edit?id=' . $id . '&error=2');
        }
        exit;
    }
}


