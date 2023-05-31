<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    //
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Plan::class,'plan_id');
    }
}
