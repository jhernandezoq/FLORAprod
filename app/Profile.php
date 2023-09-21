<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles';

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'profile_rol', 'profile_id', 'role_id');
    }
}
