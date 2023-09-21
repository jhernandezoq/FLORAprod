<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DianResolution extends Model
{
    protected $table = 'resolutions_dian';
    public $timestamps = false;

    protected $fillable = [
        'company_id', 
        'type',
        'resolution',
        'active',
        'start',
        'end',
        'current',
        'date_due',
        'prefix',
        'date_alert_due',
        'alert_due',
    ];

    public function initRow( $data = NULL ){
        $row = new \stdClass();
        $row->id = 0;
        $row->company_id = '';
        $row->type = '';
        $row->resolution = '';
        $row->active = 1;
        $row->start = '';
        $row->end = '';
        $row->current = '';
        $row->date_due = '';
        $row->prefix = '';
        $row->date_alert_due = '';
        $row->alert_due = '';
    
        if( $data !== NULL ){
            $row->id = $data->id;
            $row->company_id = $data->company_id;
            $row->type = $data->type;
            $row->resolution = $data->resolution;
            $row->start = $data->start;
            $row->end = $data->end;
            $row->current = $data->current;
            $row->date_due = $data->date_due;
            $row->prefix = $data->prefix;
            $row->date_alert_due = $data->date_alert_due;
            $row->alert_due = $data->alert_due;
        }
        return $row;
    }

    public function getAll( $type = '' ){
        $rows = DB::select("SELECT r.id,
            r.company_id,
            r.type, 
            r.resolution,
            r.active,
            r.start,
            r.end, 
            r.current,
            r.date_due,
            r.prefix, 
            r.date_alert_due,
            r.alert_due,
            c.name AS company
        FROM resolutions_dian r
        JOIN companies c ON c.id = r.company_id
        WHERE 
            c.active = 1
        ORDER BY r.id DESC");
        return $rows;
    }

    public function getAllActives( $type = '' ){
        $rows = DB::select("SELECT r.id,
            r.company_id,
            r.type, 
            r.resolution,
            r.active,
            r.start,
            r.end, 
            r.current,
            r.date_due,
            r.prefix, 
            r.date_alert_due,
            r.alert_due,
            c.name AS company
        FROM resolutions_dian r
        JOIN companies c ON c.id = r.company_id
        WHERE 
            c.active = 1 AND r.active = 1 
        ORDER BY r.id DESC");
        return $rows;
    }

    public function getOne( $id ){
        $rows = DB::select("SELECT r.*,
            c.name AS company
        FROM resolutions_dian r
        JOIN companies c ON c.id = r.company_id
        WHERE 
            c.active = 1 AND r.id = ".$id."
        ");
        return $rows;
    }

    public function getOneByCompanyId( $company_id ){
        $rows = DB::select("SELECT r.* 
        FROM resolutions_dian r
        WHERE 
            r.company_id = ".$company_id." AND r.active = 1 
        ");
        if( count( $rows ) != 0 ) return $rows[ 0 ];
        else return false;
    }

}
