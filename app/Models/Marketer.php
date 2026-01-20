<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Marketer extends Model
{
    protected $fillable = ['name','employee_id','marketing_code','phone','site_id','branch_id'];

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function marketingEmployee()
    {
        return $this->belongsTo(MarketingEmployee::class, 'employee_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}