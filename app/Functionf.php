<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Functionf extends Model
{
    protected $table = 'functions';

    public function module()
    {
        return $this->belongsTo('App\Module');
    }
}
