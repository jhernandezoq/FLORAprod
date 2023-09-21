<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'tool_id', 'agent_id','status_id', 'text', 'active',
    ];

    public function agent()
    {
        return $this->belongsTo('App\User', 'agent_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function tool()
    {
        return $this->belongsTo('App\Tool');
    }

    public function log()
    {
        return $this->hasMany('App\TicketLog');
    }

    public function state()
    {
        return $this->belongsTo('App\TicketState','status_id');
    }
}
