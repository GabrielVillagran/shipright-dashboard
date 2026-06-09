<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(2500, 50000);
        $shipping = fake()->numberBetween(500, 2500);
        $tax = (int) round($subtotal * 0.0825);
        return [
            'order_number' => 'SR-' . fake()->unique()->numberBetween(100000, 999999),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'status' => fake()->randomElement(OrderStatus::cases())->value,
            'subtotal_cents' => $subtotal,
            'shipping_cents' => $shipping,
            'tax_cents' => $tax,
            'total_cents' => $subtotal + $shipping + $tax,
        ];
    }

    public function pending(): static {
        return $this->state(fn () => [
            'status' => OrderStatus::PENDING->value,
        ]);
    }

    public function approved(): static {
        return $this->state(fn () => [
            'status' => OrderStatus::APPROVED->value,
            'approved_at' => now(),
        ]);
    }

    public function packed(): static {
        return $this->state(fn() => [
            'status' => OrderStatus::PACKED->value,
            'approved_at' => now()->subHours(4),
            'packed_at' => now()->subHours(1),
        ]);
    }

    public function shipped(): static {
        return $this->state(fn() => [
            'status' => OrderStatus::SHIPPED->value,
            'approved_at' => now()->subDays(1),
            'packed_at' => now()->subHours(12),
            'shipped_at' => now()->subHours(6),
        ]);
    }

    public function delivered(): static {
        return $this->state(fn () => [
            'status' => OrderStatus::Delivered->value,
            'approved_at' => now()->subDays(3),
            'packed_at' => now()->subDays(2),
            'shipped_at' => now()->subDay(),
            'delivered_at' => now(),
        ]);
    }

    public function cancelled(): static {
        return $this->state(fn () => [
            'status' => OrderStatus::Cancelled->value,
            'cancelled_at' => now(),
        ]);
    }
}
