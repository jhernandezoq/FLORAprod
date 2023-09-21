<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketLog extends Model
{
    protected $table = 'ticket_log';

    protected $fillable = [
        'ticket_id', 'user_id', 'text','active',
    ];

}
