<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipping_address_id',
        'order_number',
        'status',
        'subtotal_amount',
        'shipping_amount',
        'grand_total_amount',
        'shipping_recipient_name',
        'shipping_phone',
        'shipping_address_line',
        'shipping_province_id',
        'shipping_province_name',
        'shipping_city_id',
        'shipping_city_name',
        'shipping_district_id',
        'shipping_district_name',
        'shipping_postal_code',
        'courier',
        'courier_service',
        'courier_etd',
        'customer_note',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_amount' => 'integer',
            'shipping_amount' => 'integer',
            'grand_total_amount' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (blank($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        do {
            $candidate = 'ORD-' . $date . '-' . strtoupper(Str::random(6));
        } while (self::where('order_number', $candidate)->exists());

        return $candidate;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function displayGrandTotal(): string
    {
        return 'Rp ' . number_format($this->grand_total_amount, 0, ',', '.');
    }
}

