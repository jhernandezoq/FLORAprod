<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    public function functions()
    {
        return $this->belongsToMany('App\Functionf', 'role_function', 'role_id', 'function_id');
    }
}
