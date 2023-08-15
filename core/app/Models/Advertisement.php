<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    public function fiat()
    {
        return $this->belongsTo(Fiat::class);
    }

    public function fiatGateway()
    {
        return $this->belongsTo(FiatGateway::class);
    }

    public function crypto()
    {
        return $this->belongsTo(Crypto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tradeRequests()
    {
        return $this->hasMany(TradeRequest::class);
    }
}
