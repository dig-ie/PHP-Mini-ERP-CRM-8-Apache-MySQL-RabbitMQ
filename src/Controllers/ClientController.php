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

    public static function delete(): void
    {
        self::ensureAuth();
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: /dashboard?error=1');
            exit;
        }

        $success = Client::delete($id);
        if ($success) {
            header('Location: /dashboard?deleted=client');
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

        $client = Client::findById($id);
        if (!$client) {
            header('Location: /dashboard?error=3');
            exit;
        }

        require __DIR__ . '/../../views/clients/edit.php';
    }

    public static function update(): void
    {
        self::ensureAuth();
        $id = (int)($_POST['id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));

        if ($id <= 0 || $name === '') {
            header('Location: /clients/edit?id=' . $id . '&error=1');
            exit;
        }

        $success = Client::update($id, $name, $email, $phone);
        if ($success) {
            header('Location: /dashboard?updated=client');
        } else {
            header('Location: /clients/edit?id=' . $id . '&error=2');
        }
        exit;
    }
}


