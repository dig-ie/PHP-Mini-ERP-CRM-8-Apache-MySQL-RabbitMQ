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

    if ($data['type'] === 'order.created') {
        
        echo " [📧] Sending order confirmation email to client ID: {$data['client_id']}\n";
        echo " [📧] Order #{$data['order_id']} - Total: R$ {$data['total_amount']}\n";
        echo " [📧] Email sent successfully!\n";
        
        echo " [🔔] Notifying admin about new order #{$data['order_id']}\n";
        
        echo " [📦] Updating inventory for order #{$data['order_id']}\n";
        
        usleep(500 * 1000); // 500ms
    }

    $msg->ack();
    echo " [✅] Order processing completed!\n\n";
};

$channel->basic_consume($queue, '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();


