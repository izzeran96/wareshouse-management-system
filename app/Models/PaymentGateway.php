<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $table = 'payment_gateways';

    protected $fillable = [
        'secret_key',
        'category_code',
        'is_sandbox',
        'is_active',
    ];

    protected $casts = [
        'is_sandbox' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * The single active gateway configuration (falls back to env defaults).
     */
    public static function current(): ?self
    {
        return static::query()->latest('id')->first();
    }
}
