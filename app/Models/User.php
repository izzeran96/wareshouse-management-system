<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Uuid, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'email_verified_at',
        'remember_token',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Always encrypt password when it is updated.
     *
     * @param $value
     * @return string
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function subscribes()
    {
        return $this->hasMany(Subscride::class, 'user_id', 'id');
    }

    public function activeSubscribe()
    {
        return $this->hasOne(Subscride::class, 'user_id', 'id')
            ->where('status', Subscride::STATUS_ACTIVE)
            ->where('expired_date', '>=', now())
            ->latest('expired_date');
    }

    /**
     * Whether the user currently has access to the system.
     *
     * Super Admins (platform owner) and Workers (staff working under an
     * already-paying account) always have access. The account owner
     * (e.g. Warehouse Admin) needs an active, unexpired subscription.
     */
    public function hasActiveSubscription(): bool
    {
        if ($this->hasRole('Super Admin') || $this->hasRole('Worker')) {
            return true;
        }

        return Subscride::query()
            ->where('user_id', $this->id)
            ->active()
            ->exists();
    }

}
