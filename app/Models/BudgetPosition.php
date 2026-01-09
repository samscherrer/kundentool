<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetPosition extends Model
{
    protected $fillable = [
        'order_id',
        'title',
        'budget_hours',
        'budget_amount_cents',
        'currency',
        'source_offer_position_id',
    ];
}
