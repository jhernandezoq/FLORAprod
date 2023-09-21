<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Approver extends Model
{
    protected $table = 'invoice_approvers';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
