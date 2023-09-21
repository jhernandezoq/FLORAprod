<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $fillable = [
        'name', 'active','owner_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }
}
