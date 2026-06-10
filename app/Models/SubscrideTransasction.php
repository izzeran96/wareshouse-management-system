<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscrideTransasction extends Model
{
    use HasFactory;

    protected $table = 'subscribe_transasctions';

    protected $fillable = [
        'user_id',
        'subscribe_package_id',
        'bill_code',
        'pay_id',
        'status',
        'transaction_description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function package()
    {
        return $this->belongsTo(SubscribePackage::class, 'subscribe_package_id', 'id');
    }
}
