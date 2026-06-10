<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscribePackage extends Model
{
    use HasFactory;

    protected $table = 'subscribe_packages';

    protected $fillable = [
        'title',
        'description',
        'price',
        'duration_days',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function subscribes()
    {
        return $this->hasMany(Subscride::class, 'subscribe_package_id', 'id');
    }
}
