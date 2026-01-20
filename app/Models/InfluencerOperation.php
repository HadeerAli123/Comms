<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfluencerOperation extends Model
{

    protected $fillable = [
        'influencer_id',
        'operation_type',
        'amount',
        'notes',
        'employee_id',
    ];

    public function influencer()
    {
        return $this->belongsTo(Influencer::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}