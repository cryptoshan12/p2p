<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeRequest extends Model
{
    use HasFactory;

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

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

    public function buyer()
    {
        return $this->belongsTo(User::class,'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class,'seller_id');
    }
}
