<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake() -> randomElement([
                'wireless mouse',
                'gaming keyboard',
                'HD monitor',
                'USB-C hub',
                'external hard drive',
                'webcam',
                'noise-cancelling headphones',
                'laptop stand',
                'ergonomic chair',
                'desk lamp',
                'Wireless Barcode Scanner',
                'Thermal Label Printer',
                'Packing Tape Roll',
                'Shipping Scale',
                'Poly Mailer Pack',
                'Warehouse Gloves',
                'USB-C Charging Cable',
                'Inventory Tablet Stand',
            ]),
            'sku' => strtoupper(fake()->bothify('SKU-#####-??')),
            'description' => fake()->sentence(),
            'price_cents' => fake()->numberBetween(1000, 100000), // $10.00 - $1000.00
            'stock_quantity' => fake()->numberBetween(0, 500),
            'is_active' => true, 
        ];
    }
}
