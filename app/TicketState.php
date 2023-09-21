<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketState extends Model
{
    protected $table = 'ticket_states';

    protected $fillable = [
        'name', 'active',
    ];
}
