<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::query()->inRandomOrder() -> first() ?? Product::factory()->create();
        $quantity = fake()->numberBetween(1, 5);
        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'unit_price_cents' => $product->price_cents,
            'quantity' => $quantity,
            'total_cents' => $product->price_cents * $quantity,
        ];
    }
}
