<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'status',
        'subtotal_cents',
        'shipping_cents',
        'tax_cents',
        'total_cents',
        'approved_at',
        'packed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected function casts(): array {
        return [
            'status' => OrderStatus::class,
            'subtotal_cents' => 'integer',
            'shipping_cents' => 'integer',
            'tax_cents' => 'integer',
            'total_cents' => 'integer',
            'approved_at' => 'datetime',
            'packed_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function formattedSubtotal(): string
    {
        return $this->formatMoney($this->subtotal_cents);
    }

    public function formattedShipping(): string
    {
        return $this->formatMoney($this->shipping_cents);
    }

    public function formattedTax(): string
    {
        return $this->formatMoney($this->tax_cents);
    }

    public function formattedTotal(): string
    {
        return $this->formatMoney($this->total_cents);
    }

    private function formatMoney(int $amountInCents): string
    {
        return '$' . number_format($amountInCents / 100, 2);
    }
}
