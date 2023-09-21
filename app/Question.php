<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table="performance_questions";

    public function dimension()
    {
        return $this->belongsTo('App\Dimension');
    }
}
