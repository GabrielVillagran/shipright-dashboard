<?php

namespace App\Services\Orders;

use App\Enums\OrderStatus;

class OrderStatusMachine
{
    public function canTransition(OrderStatus $from, OrderStatus $to): bool
    {
        return in_array($to, $this->allowedTransitions($from), true);
    }

        public function allowedTransitions(OrderStatus $status): array
    {
        return match ($status) {
            OrderStatus::PENDING => [
                OrderStatus::APPROVED,
                OrderStatus::CANCELLED,
            ],

            OrderStatus::APPROVED => [
                OrderStatus::PACKED,
                OrderStatus::CANCELLED,
            ],

            OrderStatus::PACKED => [
                OrderStatus::SHIPPED,
                OrderStatus::CANCELLED,
            ],

            OrderStatus::SHIPPED => [
                OrderStatus::DELIVERED,
            ],

            OrderStatus::DELIVERED,
            OrderStatus::CANCELLED => [],
        };
    }

    public function explanation(OrderStatus $from, OrderStatus $to): string
    {
        return "Order cannot move from {$from->value} to {$to->value}.";
    }
}