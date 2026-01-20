<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Influencer extends Model
{
    protected $fillable = [
        'name',
        'ads_link',
        'country_id',
        'snap',
        'snap_link',
        'phone',
        'pdf',
        'whatsapp_link',
        'instagram_link',
        'tiktok_link',
        'employee_id',
        'balance',
        'basic_balance',
        'notes'
    ];
public function operations() {
    return $this->hasMany(InfluencerOperation::class);
}
public function latestOperation() {
    return $this->hasOne(InfluencerOperation::class)->latestOfMany();
}
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
    public function country() {
    return $this->belongsTo(Country::class);
}
public function visits() {
    return $this->hasMany(Visit::class);
}
}
