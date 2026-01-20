<?php

namespace App\Models;
use App\Models\Marketer;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        // أضيفي حقولاً أخرى إذا لزم
    ];

    /**
     * العلاقة للفروع (المواقع الفرعية) التابعة لهذا الموقع.
     */
    public function children()
    {
        return $this->hasMany(Site::class, 'parent_id');
    }


    /**
     * العلاقة للموقع الرئيسي (الذي ينتمي إليه هذا الموقع).
     */
    public function parent()
    {
        return $this->belongsTo(Site::class, 'parent_id');
    }

    /**
     * العلاقة بالمسوقين المرتبطين بهذا الموقع.
     */
    public function marketers()
    {
        return $this->hasMany(\App\Models\Marketer::class, 'site_id');
    }

    /**
     * العلاقة بالعملاء المرتبطين مباشرةً بهذا الموقع.
     */
    public function clients()
    {
        return $this->hasMany(\App\Models\Client::class);
    }
}