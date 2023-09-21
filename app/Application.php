<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Application extends Model
{
    protected $table = 'applications';

    public function getModules($id_user,$application)
    {
        $fingreso = DB::select("SELECT TIMESTAMPDIFF(MONTH, start_date, CURDATE()) AS meses,
		                               leader_id AS leader,
		                               id AS user_sr
                                FROM users 
								WHERE id=?",[$id_user]);

        $meses=intval($fingreso[0]->meses);
		$leader=intval($fingreso[0]->leader);
		$user_sr=intval($fingreso[0]->user_sr);

        if (($meses < 6 ) && ($application == 1) && ($leader != 301) &&($user_sr != 1935)) {
    	 $modules = DB::select('SELECT module_id AS module_id,
								       module_name AS module_name,
								       function_id AS function_id,
								       function_name AS function_name,
								       route AS function_route
								FROM permission
								WHERE id_user=? AND aplication_id=? AND active = ? AND function_id <> ?
								ORDER BY  module_id ASC',[$id_user,$application,1,6]);
        }else if(($meses < 6 ) && ($application == 1) && ($leader == 301) && ($user_sr == 1935) ){
							$modules = DB::select('SELECT module_id AS module_id,
							module_name AS module_name,
							function_id AS function_id,
							function_name AS function_name,
							route AS function_route
					FROM permission
					WHERE id_user=? AND aplication_id=? AND active = ?
					ORDER BY module_id ASC',[$id_user,$application,1]);
        }else{	
    	$modules = DB::select('SELECT module_id AS module_id,
								       module_name AS module_name,
								       function_id AS function_id,
								       function_name AS function_name,
								       route AS function_route
								FROM permission
								WHERE id_user=? AND aplication_id=? AND active = ?
								ORDER BY module_id ASC',[$id_user,$application,1]);
        }




    	return $modules;
    }
}
