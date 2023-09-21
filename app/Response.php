<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
	protected $table="performance_responses";

    public function question()
    {
        return $this->belongsTo('App\Question');

    }
}
