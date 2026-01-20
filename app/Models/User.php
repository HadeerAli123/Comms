<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;



class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
     use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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
public function commissions()
{
    return $this->hasManyThrough(
        \App\Models\Commission::class, // النهائي
        \App\Models\Marketer::class,   // الوسيط
        'employee_id',                 // marketers.employee_id → users.id
        'marketer_id',                 // commissions.marketer_id → marketers.id
        'id',                          // users.id
        'id'                           // marketers.id
    );
}
}