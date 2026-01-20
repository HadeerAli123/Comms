<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'site_id',   // FK إلى جدول sites
        // أي حقول إضافية
    ];

    /**
     * علاقة العميل بالموقع الذي ينتمي إليه.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}