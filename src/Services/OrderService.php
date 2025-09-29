<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Queue\RabbitMQPublisher;

class OrderService
{
    public static function createAndPublish(int $clientId, string $totalAmount, array $meta = []): int
    {
        $orderId = Order::create($clientId, $totalAmount);

        $payload = [
            'type' => 'order.created',
            'order_id' => $orderId,
            'client_id' => $clientId,
            'total_amount' => $totalAmount,
            'meta' => $meta,
            'timestamp' => time(),
        ];

        RabbitMQPublisher::publish('orders', 'orders.created', $payload);

        return $orderId;
    }
}


