<?php
namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case PACKED = 'packed';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}