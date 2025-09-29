<?php
declare(strict_types=1);

session_start();

// Autoload do Composer (se existir)
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require $autoloadPath;
}

use App\Controllers\AuthController;
use App\Controllers\ClientController;
use App\Controllers\OrderController;

// Roteamento simples baseado em método/URI
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

header('Content-Type: text/html; charset=utf-8');

// Rotas
if ($uri === '/login' && $method === 'GET') {
    AuthController::showLogin();
    exit;
}
if ($uri === '/login' && $method === 'POST') {
    AuthController::login();
    exit;
}
if ($uri === '/logout') {
    AuthController::logout();
    exit;
}
if ($uri === '/clients/create' && $method === 'GET') {
    ClientController::showCreate();
    exit;
}
if ($uri === '/clients/store' && $method === 'POST') {
    ClientController::store();
    exit;
}
if ($uri === '/clients/delete' && $method === 'GET') {
    ClientController::delete();
    exit;
}
if ($uri === '/clients/edit' && $method === 'GET') {
    ClientController::showEdit();
    exit;
}
if ($uri === '/clients/update' && $method === 'POST') {
    ClientController::update();
    exit;
}
if ($uri === '/orders/create' && $method === 'GET') {
    OrderController::showCreate();
    exit;
}
if ($uri === '/orders/store' && $method === 'POST') {
    OrderController::store();
    exit;
}
if ($uri === '/orders/delete' && $method === 'GET') {
    OrderController::delete();
    exit;
}
if ($uri === '/orders/edit' && $method === 'GET') {
    OrderController::showEdit();
    exit;
}
if ($uri === '/orders/update' && $method === 'POST') {
    OrderController::update();
    exit;
}

// Dashboard com tabelas
if ($uri === '/' || $uri === '/dashboard') {
    if (!isset($_SESSION['user_id'])) {
        echo '<h1>Mini ERP/CRM</h1>';
        echo '<p><a href="/login">Fazer login</a></p>';
        exit;
    }
    
    // Buscar dados para o dashboard
    $clients = \App\Models\Client::findAll();
    $orders = \App\Models\Order::findAll();
    
    require __DIR__ . '/../views/dashboard.php';
    exit;
}

http_response_code(404);
echo '<h2>404 Not Found</h2>';


