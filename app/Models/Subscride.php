<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A user's subscription instance (SaaS access to the WMS).
 */
class Subscride extends Model
{
    use HasFactory;

    protected $table = 'subscribes';

    protected $fillable = [
        'user_id',
        'subscribe_package_id',
        'period',
        'started_at',
        'expired_date',
        'price',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expired_date' => 'datetime',
        'price' => 'decimal:2',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function package()
    {
        return $this->belongsTo(SubscribePackage::class, 'subscribe_package_id', 'id');
    }

    /**
     * Active subscriptions that have not yet expired.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('expired_date', '>=', now());
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->expired_date
            && $this->expired_date->greaterThanOrEqualTo(now());
    }
}
