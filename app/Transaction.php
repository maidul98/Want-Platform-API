<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'want_id', 'fulfiller_id', 'amount_paid', 'status'];
}
