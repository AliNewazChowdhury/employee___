<?php

namespace App\Models\Payment;

use App\Models\FarmerProfile\FarmerBasicInfos;
use Illuminate\Database\Eloquent\Model;

class IrrigationPayment extends Model
{
    protected $table = 'irrigation_payments';
    protected $guarded = [];

    public function farmerBasicInfos(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FarmerBasicInfos::class, 'farmer_id', 'farmer_id');
    }

    public function refundDeducts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FarRefundDeduct::class, 'payment_id');
    }

    /*public function b1()
    {
        return $this->hasMany('B', 'prop1', 'prop1')
    }
    public function b2()
    {
        return $this->hasMany('B', 'prop2', 'prop2')
    }
    public function b3()
    {
        return $this->hasMany('B', 'prop3', 'prop3')
    }

   public function getBsAttribute()
    {
        $data = collect([$this->b1, $this->b2, $this->b3]);
        return $data->unique();
    }*/


}
