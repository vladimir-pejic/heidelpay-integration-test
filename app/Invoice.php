<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //
    protected $fillable = ['invoice_no', 'user_id', 'subscription_id', 'price', 'date_from', 'date_to', 'credit_card_id', 'paid'];
}
