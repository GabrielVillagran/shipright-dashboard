<?php

namespace App\Actions\Orders;

use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderTransitionException;
use App\Models\Order;
use App\Models\User;
use App\Services\Orders\OrderStatusMachine;
use Illuminate\Support\Facades\DB;

class TransitionOrderStatusAction 
{
    public function __construct(
        private readonly OrderStatusMachine $statusMachine
    ) {}    

    public function execute(Order $order, OrderStatus $newStatus, ?User $actor = null, ?string $reason = null): Order
    {
        $currentStatus = $order->status;

        if (! $this->statusMachine->canTransition($currentStatus, $newStatus)) {
            throw new InvalidOrderTransitionException(
                from: $currentStatus,
                to: $newStatus,
                message: $this->statusMachine->explanation($currentStatus, $newStatus)
            );
        }    

        return DB::transaction(function () use ($order, $newStatus, $currentStatus,$actor, $reason) {
            $timestampColumn = $this->timestampColumnFor($newStatus);

            $order->update([
                'status' => $newStatus->value,
                $timestampColumn => now(),
            ]);

            $order->auditLogs()->create([
                'user_id' => $actor?->id,
                'action' => 'order.status_changed',
                'old_values' => ['status' => $currentStatus->value],
                'new_values' => ['status' => $newStatus->value, $timestampColumn => $order->{$timestampColumn}?->toISOString(),],
                'reason' => $reason,
            ]);
            return $order->fresh(['lineItems', 'auditLogs']);
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