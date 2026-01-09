<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferPosition extends Model
{
    protected $fillable = [
        'offer_id',
        'title',
        'description',
        'pricing_type',
        'budget_hours',
        'budget_amount_cents',
        'currency',
        'status',
        'approved_at',
        'approved_by_user_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
