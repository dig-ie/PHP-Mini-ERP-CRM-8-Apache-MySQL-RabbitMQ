#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$host = getenv('RABBITMQ_HOST') ?: 'rabbitmq';
$port = (int)(getenv('RABBITMQ_PORT') ?: 5672);
$user = getenv('RABBITMQ_USER') ?: 'guest';
$pass = getenv('RABBITMQ_PASS') ?: 'guest';

$exchange = 'orders';
$queue = 'orders.created.q';
$routingKey = 'orders.created';

$connection = new AMQPStreamConnection($host, $port, $user, $pass);
$channel = $connection->channel();

$channel->exchange_declare($exchange, 'topic', false, true, false);
$channel->queue_declare($queue, false, true, false, false);
$channel->queue_bind($queue, $exchange, $routingKey);
$channel->basic_qos(null, 1, null);

echo " [*] Waiting for messages in {$queue}. To exit press CTRL+C\n";

$callback = function ($msg) {
    $body = (string)$msg->getBody();
    $data = json_decode($body, true);

    echo ' [x] Received: ' . $body . PHP_EOL;

    // Simula processamento: envio de email, atualização de estoque, etc.
    usleep(300 * 1000); // 300ms

    $msg->ack();
    echo " [x] Done and ack'ed\n";
};

$channel->basic_consume($queue, '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();


