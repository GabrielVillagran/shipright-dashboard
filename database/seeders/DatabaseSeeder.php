<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'staff@shipright.test'],
            [
                'name' => 'ShipRight Staff',
                'password' => Hash::make('password'),
            ]
        );
        Product::factory()->count(20)->create();

        $this->createOrdersForStatus(OrderStatus::PENDING, 8);
        $this->createOrdersForStatus(OrderStatus::APPROVED, 8);
        $this->createOrdersForStatus(OrderStatus::PACKED, 6);
        $this->createOrdersForStatus(OrderStatus::SHIPPED, 6);
        $this->createOrdersForStatus(OrderStatus::DELIVERED, 5);
        $this->createOrdersForStatus(OrderStatus::CANCELLED, 3);
    }

        private function createOrdersForStatus(OrderStatus $status, int $count): void
    {
        Order::factory()
            ->count($count)
            ->state(['status' => $status->value])
            ->create()
            ->each(function (Order $order) use ($status) {
                $items = OrderItem::factory()
                    ->count(fake()->numberBetween(1, 4))
                    ->make();

                $subtotal = 0;

                foreach ($items as $item) {
                    $subtotal += $item->total_cents;
                    $order->lineItems()->save($item);
                }

                $shipping = 999;
                $tax = (int) round($subtotal * 0.0825);

                $timestamps = match ($status) {
                    OrderStatus::PENDING => [],
                    OrderStatus::APPROVED => [
                        'approved_at' => now()->subHours(3),
                    ],
                    OrderStatus::PACKED => [
                        'approved_at' => now()->subHours(6),
                        'packed_at' => now()->subHours(2),
                    ],
                    OrderStatus::SHIPPED => [
                        'approved_at' => now()->subDay(),
                        'packed_at' => now()->subHours(12),
                        'shipped_at' => now()->subHours(6),
                    ],
                    OrderStatus::DELIVERED => [
                        'approved_at' => now()->subDays(3),
                        'packed_at' => now()->subDays(2),
                        'shipped_at' => now()->subDay(),
                        'delivered_at' => now()->subHours(2),
                    ],
                    OrderStatus::CANCELLED => [
                        'cancelled_at' => now()->subHours(4),
                    ],
                };

                $order->update([
                    'subtotal_cents' => $subtotal,
                    'shipping_cents' => $shipping,
                    'tax_cents' => $tax,
                    'total_cents' => $subtotal + $shipping + $tax,
                    ...$timestamps,
                ]);
            });
    }
}
