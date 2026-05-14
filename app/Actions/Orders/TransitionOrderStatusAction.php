<?php

namespace App\Actions\Orders;

use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderTransitionException;
use App\Models\Order;
use App\Services\Orders\OrderStatusMachine;
use Illuminate\Support\Facades\DB;

class TransitionOrderStatusAction 
{
    public function __construct(
        private readonly OrderStatusMachine $statusMachine
    ) {}    

    public function execute(Order $order, OrderStatus $newStatus): Order
    {
        $currentStatus = $order->status;

        if (! $this->statusMachine->canTransition($currentStatus, $newStatus)) {
            throw new InvalidOrderTransitionException(
                from: $currentStatus,
                to: $newStatus,
                message: $this->statusMachine->explanation($currentStatus, $newStatus)
            );
        }    

        return DB::transaction(function () use ($order, $newStatus) {
            $order->update([
                'status' => $newStatus->value,
                $this->timestampColumnFor($newStatus) => now(),
            ]);

            return $order->fresh();
        });
    }

    private function timestampColumnFor(OrderStatus $status): string
    {
        return match ($status) {
            OrderStatus::APPROVED => 'approved_at',
            OrderStatus::PACKED => 'packed_at',
            OrderStatus::SHIPPED => 'shipped_at',
            OrderStatus::DELIVERED => 'delivered_at',
            OrderStatus::CANCELLED => 'cancelled_at',
            OrderStatus::PENDING => 'created_at',
        };
    }
}