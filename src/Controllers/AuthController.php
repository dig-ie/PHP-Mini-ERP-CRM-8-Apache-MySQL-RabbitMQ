<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    public static function showLogin(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        require __DIR__ . '/../../views/login.php';
    }

    public static function login(): void
    {
        $email = (string)($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            header('Location: /login?error=1');
            exit;
        }

        $user = User::findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = (string)$user['name'];
            header('Location: /dashboard');
            exit;
        }

        header('Location: /login?error=1');
        exit;
    }

    public static function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}


