<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiatGateway extends Model
{
    protected $casts = [
        'code' => 'array'
    ];

    use HasFactory;

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }

    public function tradeRequests()
    {
        return $this->hasMany(TradeRequest::class);
    }
}
