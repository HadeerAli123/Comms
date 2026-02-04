<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;

    protected $fillable = [
        'username',
        'name',
        'email',
        'table_commission',
        'password',
        'prec',
        'marketing_code',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function marketers()
    {
        return $this->hasMany(Marketer::class, 'employee_id');
    }

    // Using hasManyThrough to access commissions related to the user via marketers
    public function commissions()
    {
        return $this->hasManyThrough(
            \App\Models\Commission::class,
            \App\Models\Marketer::class,
            'employee_id',
            'marketer_id',
            'id',
            'id'
        );
    }
}
