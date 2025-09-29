<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Client;

class ClientController
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
        require __DIR__ . '/../../views/clients/create.php';
    }

    public static function store(): void
    {
        self::ensureAuth();
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));

        if ($name === '') {
            header('Location: /clients/create?error=1');
            exit;
        }

        $clientId = Client::create($name, $email, $phone);
        header('Location: /dashboard?client_id=' . $clientId);
        exit;
    }
}


