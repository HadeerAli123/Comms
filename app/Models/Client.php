<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'site_id',
        // Add additional fields here if needed
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
