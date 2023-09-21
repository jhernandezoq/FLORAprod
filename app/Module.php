<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'modules';

    public function application()
    {
        return $this->belongsTo('App\Application');
    }

}
