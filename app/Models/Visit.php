<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Visit extends Model
{
    protected $fillable = [
        'influencer_id',
        'amount',
        'is_announced',
        'notes',
        'people_count',
        'user_id',
        'rating',
        'accept_notes',
        'media'
    ];

    public function influencer()
    {
        return $this->belongsTo(Influencer::class);
    }

    public function getStatusLabelAttribute()
    {
        return $this->is_announced ? 'تم الإعلان' : 'لم يتم الإعلان';
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}