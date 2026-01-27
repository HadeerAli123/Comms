<?php

namespace App\Models;
use App\Models\Marketer;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
    ];

    public function children()
    {
        return $this->hasMany(Site::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Site::class, 'parent_id');
    }

    public function marketers()
    {
        return $this->hasMany(\App\Models\Marketer::class, 'site_id');
    }

    public function clients()
    {
        return $this->hasMany(\App\Models\Client::class);
    }
}
