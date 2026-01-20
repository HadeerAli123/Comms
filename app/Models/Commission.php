<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{

    protected $fillable = [
        'marketer_id',
        'site_id',
        'visitors',
        'dishes','delivery_code',
        'cheque_amount','discount_rate',
        'commission_amount','employee_id',
        'invoice_amount','invoice_number','invoice_image',
        'type','created_by','updated_by','received','attach','promo_code','promo_image',
    ];

    public function marketer()
    {
        return $this->belongsTo(Marketer::class);
    }


public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function updater()
{
    return $this->belongsTo(User::class, 'updated_by');
}
    public function marketingEmployee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }


    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function site()
{
    return $this->belongsTo(Site::class);
}
}