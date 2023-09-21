<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'invoice_logg';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function next_user()
    {
        return $this->belongsTo('App\User','next_user_id');
    }

    public function state()
    {
        return $this->belongsTo('App\State');
    }
}
