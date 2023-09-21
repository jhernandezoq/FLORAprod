<?php

namespace App\Http\Controllers;

use App\Question;
use App\User;
use App\Response;
use App\Application;
use App\Pdi;
use App\Average;
use App\Ubication;
use App\Performance_pdi_history;
use App\Exports\PdiExport;
use App\Exports\PdiExportMissing;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class PerformanceController extends Controller
{
    
	public function self(Request $request)
	{
		$user = Auth::user();

        if ($user) {
        $application = new Application();
        $modules = $application->getModules($user->id,1);

       

		//verificar si ya realice la autovaloración
		$responses = Response::where('evaluator_id','=',$user->id)
					->where('evaluated_id','=',$user->id)
					->where('active','=',1)
					->orderby('question_id','asc')
					->get();

		if($responses->count() == 0)
		{
			//Verifico si tengo persona a cargo
			$team = User::where('leader_id','=',$user->id)->count();
			
			if($team > 0) //tengo equipo a cargo
			{
                
				$questions = Question::where('type_id','=',1)
							->where('active','=',1)
							->orderby('id','asc')
							->orderby('dimension_id','asc')
							->get();
				
			}
			else
			{
	    
				$questions = Question::where('type_id','=',1)
							->where('leader','=',0)
							->where('active','=',1)
							->orderby('id','asc')
							->orderby('dimension_id','asc')
							->get();
			}
			return view('performance.self',['modules' => $modules,'user' => $user,
				'questions' => $questions,
				'show' => 0]);
		}else{
			
		$responses = Response::where('evaluator_id','=',$user->id)
					->where('evaluated_id','=',$user->id)
					->where('active','=',1)
					->orderby('question_id','desc')
					->get();
		

		return view('performance.self',['modules' => $modules,'user' => $user,'responses' => $responses , 'show' => 1]);
			
		}
        }else{

        $id_user=$request->id_user;

        $application = new Application();
        $modules = $application->getModules($id_user,1);

       

		//verificar si ya realice la autovaloración
		$responses = Response::where('evaluator_id','=',$id_user)
					->where('evaluated_id','=',$id_user)
					->where('active','=',1)
					->orderby('question_id','asc')
					->get();

		if($responses->count() == 0)
		{
			//Verifico si tengo persona a cargo
			$team = User::where('leader_id','=',$id_user)->count();
			
			if($team > 0) //tengo equipo a cargo
			{
                
				$questions = Question::where('type_id','=',1)
							->where('active','=',1)
							->orderby('id','asc')
							->orderby('dimension_id','asc')
							->get();
				
			}
			else
			{
	    
				$questions = Question::where('type_id','=',1)
							->where('leader','=',0)
							->where('active','=',1)
							->orderby('id','asc')
							->orderby('dimension_id','asc')
							->get();
			}
			return view('performance.self',['modules' => $modules,'user' => $user,
				'questions' => $questions,
				'show' => 0]);
		}else{
			
		$responses = Response::where('evaluator_id','=',$id_user)
					->where('evaluated_id','=',$id_user)
					->where('active','=',1)
					->orderby('question_id','desc')
					->get();
		

		return view('performance.self',['modules' => $modules,'user' => $user,'responses' => $responses , 'show' => 1]);
			
		}

        }

	}
	

	public function storeSelfAssessment(Request $request)
	{
		
		$user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,1);


		$input = $request->except('_token','guardar');


		$keys = array_keys($input);

		foreach ($keys as  $value) 
		{
			$aux = explode("-", $value);
			$response = new Response();
			$response->question_id = $value;
			$response->evaluator_id = $user->id;
			$response->evaluated_id = $user->id;
			$response->value = $input[$value];
			$response->save();
		}

		$responses = Response::where('evaluator_id','=',$user->id)
					->where('evaluated_id','=',$user->id)
					->orderby('question_id','asc')
					->get();

		$performance_user_response=DB::UPDATE('UPDATE performance_responses
			                           SET active = 0
			                           WHERE id=?',[$user->id]);
		
        return view('welcome',['modules' => $modules,'user' => $user,'responses' => $responses , 'show' => 1]);
		//return view('performance.self',['modules' => $modules,'user' => $user,'responses' => $responses , 'show' => 1]);
	}

	public function myTeam()
	{
		$user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,1);

		$team = User::where('leader_id','=',$user->id)
		              ->where('active','=',1)
		              ->get();

		$responses = Response::orderby('question_id','asc')
					->groupBy('evaluated_id','question_id','id','evaluator_id','value','created_at','updated_at','active')
					->limit(1)
					->get();

    	$pdi = Pdi::where('evaluator_id','=',$user->id)
    	            ->where('active','=',1)
					->orderby('dimension_id','asc')
					->get();

		$countverificationpdi=count($pdi);

   		$responses=DB::SELECT('SELECT evaluated_id AS evaluated_id
   			                   FROM performance_responses
   			                   WHERE active = 1
   			                   GROUP BY evaluated_id');
   		$countverification=count($responses);
   		/*$responses=DB::SELECT('SELECT u.id AS evaluated_id,
							       count(p.id) AS countactiveresponses
							FROM users u
							LEFT JOIN performance_responses p
							ON p.evaluated_id=u.id
							WHERE p.active = 1 
							GROUP BY u.id,p.evaluated_id');*/
   		//$countverification=count($responses);

		$data=DB::SELECT('SELECT a.id AS evaluated_id,
		       COUNT(b.id) AS amount,
		       COUNT(p.id) AS amountpd
		FROM users a
		LEFT JOIN performance_responses b
		ON b.evaluated_id = a.id
        LEFT JOIN performance_pdis p
        ON p.evaluated_id= b.evaluated_id
		WHERE a.leader_id = ? AND
		      b.active = ? AND
		      p.active = ?
		GROUP BY a.id',[$user->id,1,1]);

		//$countverification=count($data);


		$history=DB::SELECT('SELECT evaluated_id AS evaluated_id,
			                        count(id) AS counthistory
			                        FROM performance_pdi_histories
			                        GROUP BY evaluated_id');

       $mainaverage=DB::SELECT('SELECT evaluated_id AS evaluated_id,
							       dimension_id AS dimension,
							       ROUND(AVG(average),2) AS average
							FROM performance_pdis
							WHERE evaluator_id=? AND
							      active = ?
							GROUP BY id,evaluated_id,evaluator_id,dimension_id',[$user->id,1]);

       $lastverifications=DB::SELECT('SELECT u.id AS evaluated_id,
									       count(r.id) AS count_responses,
									       count(p.id) AS count_pdis
									FROM users u
									LEFT JOIN performance_responses r
									ON r.evaluated_id= u.id
									LEFT JOIN performance_pdis p
									ON p.evaluated_id= r.evaluated_id
									WHERE u.leader_id="'.$user->id.'" OR
									      r.active = 1 OR
									      p.active = 1
									GROUP BY u.id,r.evaluated_id,p.evaluated_id');


       
        
		return view('performance.team',['modules' => $modules,'user' => $user,'team' => $team,'responses'=>$responses,'pdi'=>$pdi,'mainaverage'=>$mainaverage,'data'=>$data,'history'=>$history,'countverification'=>$countverification,'countverificationpdi'=>$countverificationpdi,'lastverifications'=>$lastverifications]);
	}

    public function assessment($person)
    {
    	$user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,1);
        $id_dimensions=array();
        $pdi_description=array();

        //obteber si la persona a calificar es lider
        $leaderamount = User::where('leader_id','=',$person)->get();

    	//obtener la autoevaluación de la persona
    	$responses = Response::where('evaluator_id','=',$person)
					->where('evaluated_id','=',$person)
					->where('active','=',1)
					->orderby('question_id','asc')
					->get();
		$countresponses=count($responses);
		//Obtener los pdi de la persona
    	$pdi = Pdi::where('evaluator_id','=',$user->id)
					->where('evaluated_id','=',$person)
					->where('active','=',1)
					->orderby('dimension_id','asc')
					->get();
        $i=0;
        $dataradio=4;
        foreach ($pdi as $key ) {
        	array_push($id_dimensions, $key->dimension_id);
        	array_push($pdi_description, $key->pdi_description);
        	
        }



		$leaderResponses = Response::where('evaluator_id','=',$user->id)
					->where('evaluated_id','=',$person)
					->where('active','=',1)
					->orderby('question_id','asc')
					->get();

		if($leaderResponses->count() == 0)
		{
			return view('performance.assessment',['modules' => $modules,'user' => $user,'responses' => $responses ,'person' => $person, 'show' => 1,'leaderamount'=>$leaderamount->count(),'id_dimensions'=>$id_dimensions,'pdi_description'=>$pdi_description,'pdi'=>$pdi,'countresponses'=>$countresponses]);
		}
		else
		{
			return view('performance.assessment',['modules' => $modules,'user' => $user,
				'responses' => $responses,'leaderResponses' => $leaderResponses ,'person' => $person, 'show' => 0,'leaderamount'=>$leaderamount->count(),'id_dimensions'=>$id_dimensions,'pdi_description'=>$pdi_description,'pdi'=>$pdi,'countresponses'=>$countresponses]);
		}
		
    }





    public function assessment2($person)
    {
    	$user = $request->id_user;
    	$person = $request->id_colaborator;
        $application = new Application();
        $modules = $application->getModules($user,1);
        $id_dimensions=array();
        $pdi_description=array();

        //obteber si la persona a calificar es lider
        $leaderamount = User::where('leader_id','=',$person)->get();

    	//obtener la autoevaluación de la persona
    	$responses = Response::where('evaluator_id','=',$person)
					->where('evaluated_id','=',$person)
					->where('active','=',1)
					->orderby('question_id','asc')
					->get();
		$countresponses=count($responses);
		//Obtener los pdi de la persona
    	$pdi = Pdi::where('evaluator_id','=',$user)
					->where('evaluated_id','=',$person)
					->where('active','=',1)
					->orderby('dimension_id','asc')
					->get();
        $i=0;
        $dataradio=4;
        foreach ($pdi as $key ) {
        	array_push($id_dimensions, $key->dimension_id);
        	array_push($pdi_description, $key->pdi_description);
        	
        }



		$leaderResponses = Response::where('evaluator_id','=',$user)
					->where('evaluated_id','=',$person)
					->where('active','=',1)
					->orderby('question_id','asc')
					->get();

		if($leaderResponses->count() == 0)
		{
			return view('performance.assessment',['modules' => $modules,'user' => $user,'responses' => $responses ,'person' => $person, 'show' => 1,'leaderamount'=>$leaderamount->count(),'id_dimensions'=>$id_dimensions,'pdi_description'=>$pdi_description,'pdi'=>$pdi,'countresponses'=>$countresponses]);
		}
		else
		{
			return view('performance.assessment',['modules' => $modules,'user' => $user,
				'responses' => $responses,'leaderResponses' => $leaderResponses ,'person' => $person, 'show' => 0,'leaderamount'=>$leaderamount->count(),'id_dimensions'=>$id_dimensions,'pdi_description'=>$pdi_description,'pdi'=>$pdi,'countresponses'=>$countresponses]);
		}
		
    }


    public function storeAssessment(Request $request)
	{
		$values=array();
		$radiovalues=array();
		$user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,1);
        $team = User::where('leader_id','=',$user->id)->get();

		$input = $request->except('_token','guardar','evaluated_id','Ser_objective','Saber_objective','Hacer_objective','Ser_action','Saber_action','Hacer_action','Ser_date','Saber_date','Hacer_date','validate');
		$data = $request->except('_token','guardar','evaluated_id');

		foreach ($input as $radio) {
			array_push($radiovalues, $radio);
		}

      $averages1=0;
      $averages2=0;
      $averages3=0;
      if(count($input) == 21){  
        $Suma1=0;
		for ($i=1; $i <=7 ; $i++) { 
			$Suma1=$Suma1+$input[$i];
		}
		$averages1=$Suma1/7;
        $Suma2=0;
		for ($i=11; $i <=17 ; $i++) { 
			$Suma2=$Suma2+$input[$i];
		}
		$averages2=$Suma2/7;
        $Suma3=0;
		for ($i=21; $i <=27 ; $i++) { 
			$Suma3=$Suma3+$input[$i];
		}
		$averages3=$Suma3/7;
	}else{
        $Suma1=0;
		for ($i=1; $i <=10 ; $i++) { 
			$Suma1=$Suma1+$input[$i];
		}
		$averages1=$Suma1/10;
        $Suma2=0;
		for ($i=11; $i <=20 ; $i++) { 
			$Suma2=$Suma2+$input[$i];
		}
		$averages2=$Suma2/10;
        $Suma3=0;
		for ($i=21; $i <=30 ; $i++) { 
			$Suma3=$Suma3+$input[$i];
		}
		$averages3=$Suma3/10;
	}
    Session::forget('average1');
    Session::forget('average1');
    Session::forget('average1');

    Session::forget('Ser_objective');
    Session::forget('Saber_objective');
    Session::forget('Hacer_objective');

    Session::forget('Ser_action');
    Session::forget('Saber_action');
    Session::forget('Hacer_action');

    Session::forget('Ser_date');
    Session::forget('Saber_date');
    Session::forget('Hacer_date');

	$request->session()->put('average1', round($averages1,2));
	$request->session()->put('average2', round($averages2,2));
	$request->session()->put('average3', round($averages3,2));

	$request->session()->put('Ser_objective', ($request->Ser_objective));
	$request->session()->put('Saber_objective',($request->Saber_objective));
	$request->session()->put('Hacer_objective', ($request->Hacer_objective));

	$request->session()->put('Ser_action', ($request->Ser_action));
	$request->session()->put('Saber_action',($request->Saber_action));
	$request->session()->put('Hacer_action', ($request->Hacer_action));

	$request->session()->put('Ser_date', ($request->Ser_date));
	$request->session()->put('Saber_date',($request->Saber_date));
	$request->session()->put('Hacer_date', ($request->Hacer_date));

	

       
		if ((($averages1 < 4.5) && $request->Ser_objective == NULL) or (($averages2 < 4.5) && $request->Saber_objective == NULL) or (($averages3 < 4.5) && $request->Hacer_objective == NULL)) {
        $fields=[
        	'Ser_objective'=>'required_with:Ser_action,Ser_date',
        	'Ser_action'=>'required_with:Ser_objective,Ser_date',
        	'Ser_date'=>'required_with:Ser_action,Ser_objective',
        	'Saber_objective'=>'required_with:Saber_action,Saber_date',
        	'Saber_action'=>'required_with:Saber_objective,Saber_date',
        	'Saber_date'=>'required_with:Saber_action,Saber_objective',
        	'Hacer_objective'=>'required_with:Hacer_action,Hacer_date',
        	'Hacer_action'=>'required_with:Hacer_objective,Hacer_date',
        	'Hacer_date'=>'required_with:Hacer_action,Hacer_objective',
        	'validate'=>'required|numeric'
        ];
		}else{
        $fields=[
        	'Ser_objective'=>'required_with:Ser_action,Ser_date',
        	'Ser_action'=>'required_with:Ser_objective,Ser_date',
        	'Ser_date'=>'required_with:Ser_action,Ser_objective',
        	'Saber_objective'=>'required_with:Saber_action,Saber_date',
        	'Saber_action'=>'required_with:Saber_objective,Saber_date',
        	'Saber_date'=>'required_with:Saber_action,Saber_objective',
        	'Hacer_objective'=>'required_with:Hacer_action,Hacer_date',
        	'Hacer_action'=>'required_with:Hacer_objective,Hacer_date',
        	'Hacer_date'=>'required_with:Hacer_action,Hacer_objective'
        ];

		}

        $this->validate($request,$fields);

		
		$keys = array_keys($input);
		
		foreach ($keys as  $value) 
		{
			array_push($values, $input[$value]);
			$response = new Response();
			$response->question_id = $value;
			$response->evaluator_id = $user->id;
			$response->evaluated_id = $request->evaluated_id;
			$response->value = $input[$value];
			$response->save();
		}
        
      if(count($input) == 21){  
        $Sum1=0;
		for ($i=0; $i <=6 ; $i++) { 
			$Sum1=$Sum1+$values[$i];
		}
		$average1=$Sum1/7;
        $Sum2=0;
		for ($i=7; $i <=13 ; $i++) { 
			$Sum2=$Sum2+$values[$i];
		}
		$average2=$Sum2/7;
        $Sum3=0;
		for ($i=14; $i <=20 ; $i++) { 
			$Sum3=$Sum3+$values[$i];
		}
		$average3=$Sum3/7;

		$totalaverage=($average1+$average2+$average3)/3;
	}else{
        $Sum1=0;
		for ($i=0; $i <=9 ; $i++) { 
			$Sum1=$Sum1+$values[$i];
		}
		$average1=$Sum1/10;
        $Sum2=0;
		for ($i=10; $i <=19 ; $i++) { 
			$Sum2=$Sum2+$values[$i];
		}
		$average2=$Sum2/10;
        $Sum3=0;
		for ($i=20; $i <=29 ; $i++) { 
			$Sum3=$Sum3+$values[$i];
		}
		$average3=$Sum3/10;

		$totalaverage=($average1+$average2+$average3)/3;	
	}

if ($request->Ser_objective == NULL) {
	$request->Ser_objective = ' ';
}
if ($request->Saber_objective == NULL) {
	$request->Saber_objective = ' ';
}
if ($request->Hacer_objective == NULL) {
	$request->Hacer_objective = ' ';
}
if ($request->Ser_action == NULL) {
	$request->Ser_action = ' ';
}
if ($request->Saber_action == NULL) {
	$request->Saber_action = ' ';
}
if ($request->Hacer_action == NULL) {
	$request->Hacer_action = ' ';
}
if ($request->Ser_date == NULL) {
	$request->Ser_date = ' ';
}
if ($request->Saber_date == NULL) {
	$request->Saber_date = ' ';
}
if ($request->Hacer_date == NULL) {
	$request->Hacer_date = ' ';
}

			$pdi = new Pdi();
			$pdi->dimension_id = 1;
			$pdi->evaluator_id = $user->id;
			$pdi->evaluated_id = $request->evaluated_id;
			$pdi->objective = $request->Ser_objective;
			$pdi->action = $request->Ser_action;
			$pdi->followupdate = $request->Ser_date;
			$pdi->average = round($average1,2);
			$pdi->active = 1;
			$pdi->save();	

			$pdi = new Pdi();
			$pdi->dimension_id = 2;
			$pdi->evaluator_id = $user->id;
			$pdi->evaluated_id = $request->evaluated_id;
			$pdi->objective = $request->Saber_objective;
			$pdi->action = $request->Saber_action;
			$pdi->followupdate = $request->Saber_date;
			$pdi->average = round($average2,2);
			$pdi->active = 1;
			$pdi->save();	

			$pdi = new Pdi();
			$pdi->dimension_id = 3;
			$pdi->evaluator_id = $user->id;
			$pdi->evaluated_id = $request->evaluated_id;
			$pdi->objective = $request->Hacer_objective;
			$pdi->action = $request->Hacer_action;
			$pdi->followupdate = $request->Hacer_date;
			$pdi->average = round($average3,2);
			$pdi->active = 1;
			$pdi->save();

			$pdi = new Pdi();
			$pdi->dimension_id = 4;
			$pdi->evaluator_id = $user->id;
			$pdi->evaluated_id = $request->evaluated_id;
			$pdi->objective = 'N/A';
			$pdi->action = 'N/A';
			$pdi->followupdate = 'N/A';
			$pdi->average = round($totalaverage,2);
			$pdi->active = 1;
			$pdi->save();


			$pdi = new performance_pdi_history();
			$pdi->dimension_id = 1;
			$pdi->evaluator_id = $user->id;
			$pdi->evaluated_id = $request->evaluated_id;
			$pdi->objective = $request->Ser_objective;
			$pdi->action = $request->Ser_action;
			$pdi->followupdate = $request->Ser_date;
			$pdi->average = round($average1,2);
			$pdi->active = 1;
			$pdi->save();	

			$pdi = new performance_pdi_history();
			$pdi->dimension_id = 2;
			$pdi->evaluator_id = $user->id;
			$pdi->evaluated_id = $request->evaluated_id;
			$pdi->objective = $request->Saber_objective;
			$pdi->action = $request->Saber_action;
			$pdi->followupdate = $request->Saber_date;
			$pdi->average = round($average2,2);
			$pdi->active = 1;
			$pdi->save();	

			$pdi = new performance_pdi_history();
			$pdi->dimension_id = 3;
			$pdi->evaluator_id = $user->id;
			$pdi->evaluated_id = $request->evaluated_id;
			$pdi->objective = $request->Hacer_objective;
			$pdi->action = $request->Hacer_action;
			$pdi->followupdate = $request->Hacer_date;
			$pdi->average = round($average3,2);
			$pdi->active = 1;
			$pdi->save();

			$pdi = new performance_pdi_history();
			$pdi->dimension_id = 4;
			$pdi->evaluator_id = $user->id;
			$pdi->evaluated_id = $request->evaluated_id;
			$pdi->objective = 'N/A';
			$pdi->action = 'N/A';
			$pdi->followupdate = 'N/A';
			$pdi->average = round($totalaverage,2);
			$pdi->active = 1;
			$pdi->save();			


   		$responses=DB::SELECT('SELECT evaluated_id AS evaluated_id
   			                   FROM performance_responses
   			                   WHERE active = 1
   			                   GROUP BY evaluated_id');
   		$countverification=count($responses);

       $mainaverage=DB::SELECT('SELECT evaluated_id AS evaluated_id,
							       dimension_id AS dimension,
							       ROUND(AVG(average),1) AS average
							FROM performance_pdis
							WHERE evaluator_id=? AND
							      active = ?
							GROUP BY id,evaluated_id,evaluator_id,dimension_id',[$user->id,1]);

       	$data=DB::SELECT('SELECT a.id AS evaluated_id,
		       COUNT(b.id) AS amount,
		       COUNT(p.id) AS amountpd
		FROM users a
		LEFT JOIN performance_responses b
		ON b.evaluated_id = a.id
        LEFT JOIN performance_pdis p
        ON p.evaluated_id= b.evaluated_id
		WHERE a.leader_id = ? AND
		      b.active = ? AND
		      p.active = ?
		GROUP BY a.id',[$user->id,1,1]);



		$history=DB::SELECT('SELECT evaluated_id AS evaluated_id,
	                        count(id) AS counthistory
	                        FROM performance_pdi_histories
	                        WHERE active = 1
	                        GROUP BY evaluated_id');

    	$pdi = Pdi::where('evaluator_id','=',$user->id)
    	            ->where('active','=',1)
					->orderby('dimension_id','asc')
					->get();


		$countverificationpdi=count($pdi);

       $lastverifications=DB::SELECT('SELECT u.id AS evaluated_id,
									       count(r.id) AS count_responses,
									       count(p.id) AS count_pdis
									FROM users u
									LEFT JOIN performance_responses r
									ON r.evaluated_id= u.id
									LEFT JOIN performance_pdis p
									ON p.evaluated_id= r.evaluated_id
									WHERE u.leader_id="'.$user->id.'" OR
									      r.active = 1 OR
									      p.active = 1
									GROUP BY u.id,r.evaluated_id,p.evaluated_id');





		return view('performance.team',['modules' => $modules,'user' => $user,'team' => $team,'responses'=>$responses,'mainaverage'=>$mainaverage,'data'=>$data,'history'=>$history,'countverification'=>$countverification,'countverificationpdi'=>$countverificationpdi,'lastverifications'=>$lastverifications,'average1'=>round($average1,2),'average2'=>round($average2,2),'average3'=>round($average3,2)]);

	}


	public function report(Request $request)
	{
		$user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,6);
        $names=array();

        $users = User::where('active','=',1)->get();
        //$ubications = User::where('active','=',1)->get();  
        $ubications=DB::SELECT('SELECT ubication_name as ubication_name from users group by ubication_name');

       

        $leaders=DB::SELECT('SELECT u.name AS name,
								   u.id AS id,
							       us.leader_id as leader_id
								   FROM users u
							INNER JOIN users us 
							ON us.leader_id= u.id
							GROUP BY u.id,u.name,us.leader_id');

       $data=DB::SELECT('SELECT YEAR(p.created_at) AS year,
									u.name AS name,
									u.id AS id,
									us.name AS jefe,
									usl.name AS leader,
									ub.name AS area
							FROM users u
							INNER JOIN performance_pdi_histories p
							ON p.evaluated_id= u.id
							INNER JOIN users us
							ON us.id = p.evaluator_id
							INNER JOIN users usl
							ON usl.id = u.leader_id
							INNER JOIN ubications ub
							ON ub.id= u.ubication_id
					GROUP BY p.created_at,u.id,u.name,us.name,ub.name
					ORDER BY u.id ASC,YEAR(p.created_at) DESC');

       $datapdiser=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       YEAR(created_at) AS year,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ?
						ORDER BY evaluated_id ASC,YEAR(created_at) DESC',[1]);
       $datapdisaber=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       YEAR(created_at) AS year,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ?
						ORDER BY evaluated_id ASC,YEAR(created_at) DESC',[2]);
       $datapdihacer=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       YEAR(created_at) AS year,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ?
						ORDER BY evaluated_id ASC,YEAR(created_at) DESC',[3]);
       $datapditotal=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       YEAR(created_at) AS year,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ?
						ORDER BY evaluated_id ASC,YEAR(created_at) DESC',[4]);
	$request->session()->put('data', $data);
	$request->session()->put('datapdiser', $datapdiser);
	$request->session()->put('datapdisaber', $datapdisaber);
	$request->session()->put('datapdihacer', $datapdihacer);
	$request->session()->put('datapditotal', $datapditotal);

	return view('performance.report',['modules' => $modules,'user' => $user,'data'=>$data,'datapdiser'=>$datapdiser,'datapdisaber'=>$datapdisaber,'datapdihacer'=>$datapdihacer,'datapditotal'=>$datapditotal,'users'=>$users,'ubications'=>$ubications,'leaders'=>$leaders]);
	}

    

	public function seeker(Request $request){

	$user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,6);
    $values=array();

    $users = User::where('active','=',1)->get();
    //$ubications = User::where('active','=',1)->get();
    $ubications=DB::SELECT('SELECT ubication_name as ubication_name from users group by ubication_name');

     $input = $request->except('_token');
     $id_user=$request->user;
     $id_boss=$request->leaders;
     $id_area=$request->ubication;
     $year=$request->year;
     


        $leaders=DB::SELECT('SELECT u.name AS name,
								   u.id AS id,
							       us.leader_id as leader_id
								   FROM users u
							INNER JOIN users us 
							ON us.leader_id= u.id
							GROUP BY u.id,u.name,us.leader_id');


     	$datas='SELECT
     	         YEAR(p.created_at) AS year, 
     		     u.name AS name,
    	         u.id AS id,
                 us.name AS jefe,
                 usl.name AS leader,
                 u.ubication_name AS area
		FROM users u
		INNER JOIN performance_pdi_histories p
		ON p.evaluated_id= u.id
		INNER JOIN users us
		ON us.id = p.evaluator_id
		INNER JOIN users usl
		ON usl.id = u.leader_id	
		WHERE u.active = 1 ';


		if ($id_user != 0) {
		$datas.='AND u.id = ? ';
		}
		if ($id_boss != '0') {
		$datas.='AND u.leader_id = ? ';
		}
		if ($id_area != '0') {
		$datas.='AND u.ubication_name = ? ';
		}
		if ($year != 0) {
		$datas.='AND YEAR(p.created_at) = ? ';
		}
		$datas.='GROUP BY p.created_at,u.id,u.name,us.name,u.ubication_name
		ORDER BY u.id ASC,YEAR(p.created_at) DESC';

		if ($id_user != 0) {
			array_push($values,$id_user);
		} 

		if ($id_boss != '0') {
			array_push($values,$id_boss);
		} 

		if ($id_area != '0') {
			array_push($values,$id_area);
		}

		if ($year != 0) {
			array_push($values,$year);
		} 
		
	
		$data=DB::SELECT($datas,$values); 


       $datapdiser=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       YEAR(created_at) AS year,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ?',[1]);
       $datapdisaber=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       YEAR(created_at) AS year,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ?',[2]);
       $datapdihacer=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       YEAR(created_at) AS year,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ?',[3]);
       $datapditotal=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       YEAR(created_at) AS year,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ?',[4]);
	$request->session()->put('data', $data);
	$request->session()->put('datapdiser', $datapdiser);
	$request->session()->put('datapdisaber', $datapdisaber);
	$request->session()->put('datapdihacer', $datapdihacer);
	$request->session()->put('datapditotal', $datapditotal);

	return view('performance.report',['modules' => $modules,'user' => $user,'data'=>$data,'datapdiser'=>$datapdiser,'datapdisaber'=>$datapdisaber,'datapdihacer'=>$datapdihacer,'datapditotal'=>$datapditotal,'users'=>$users,'ubications'=>$ubications,'leaders'=>$leaders]);

	}


	public function export(Request $request){
		
		return Excel::download(new PdiExport, 'pdi.xlsx');
	}

	public function exportmissing(Request $request){
		
		return Excel::download(new PdiExportMissing, 'missing.xlsx');
	}


	public function history(Request $request){
	$user = Auth::user();

	if ($user) {
    $application = new Application();
    $modules = $application->getModules($user->id,1);
    $person = $request->id_user;

       $data=DB::SELECT('SELECT u.name AS name,
						        u.id AS id,
							    us.name AS jefe,
							    ub.name AS area,
							    DATE(p.created_at) AS date,
							    YEAR(DATE(p.created_at)) AS year,
							    MONTH(DATE(p.created_at)) AS month
						FROM users u
						INNER JOIN users us
						ON us.id = u.leader_id
						INNER JOIN performance_pdi_histories p
						ON p.evaluated_id= u.id
						INNER JOIN ubications ub
						ON ub.id= u.ubication_id
						WHERE u.id=? AND
						      p.dimension_id=?
						GROUP BY DATE(p.created_at),u.name,u.id,us.name,ub.name,p.created_at
				        ORDER BY YEAR(p.created_at) DESC,u.id DESC',[$person,1]);

       $datapdiser=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       DATE(created_at) AS date,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id=?
						ORDER BY id DESC'
						,[1,$person]);
       $datapdisaber=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						        DATE(created_at) AS date,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id = ?
						ORDER BY id DESC',[2,$person]);
       $datapdihacer=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       DATE(created_at) AS date,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id = ?
						ORDER BY id DESC',[3,$person]);
       $datapditotal=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       DATE(created_at) AS date,
						       followupdate AS followupdate,
						       file AS file
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id = ?
						ORDER BY id DESC',[4,$person]);

	return view('performance.userhistory',['modules' => $modules,'data'=>$data,'datapdiser'=>$datapdiser,'datapdisaber'=>$datapdisaber,'datapdihacer'=>$datapdihacer,'datapditotal'=>$datapditotal]);     
	}else{
    $person=$request->id_user;
    $application = new Application();
    $modules = $application->getModules($person,0);


    $data=DB::SELECT('SELECT u.name AS name,
						        u.id AS id,
							    us.name AS jefe,
							    ub.name AS area,
							    DATE(p.created_at) AS date,
							    YEAR(DATE(p.created_at)) AS year,
							    MONTH(DATE(p.created_at)) AS month
						FROM users u
						INNER JOIN users us
						ON us.id = u.leader_id
						INNER JOIN performance_pdi_histories p
						ON p.evaluated_id= u.id
						INNER JOIN ubications ub
						ON ub.id= u.ubication_id
						WHERE u.id=? AND
						      p.dimension_id=?
						GROUP BY DATE(p.created_at),u.name,u.id,us.name,ub.name,p.created_at
				        ORDER BY YEAR(p.created_at) DESC,u.id DESC',[$person,1]);

       $datapdiser=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       DATE(created_at) AS date,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id=?
						ORDER BY id DESC'
						,[1,$person]);
       $datapdisaber=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						        DATE(created_at) AS date,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id = ?
						ORDER BY id DESC',[2,$person]);
       $datapdihacer=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       DATE(created_at) AS date,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id = ?
						ORDER BY id DESC',[3,$person]);
       $datapditotal=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       DATE(created_at) AS date,
						       followupdate AS followupdate,
						       file AS file
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id = ?
						ORDER BY id DESC',[4,$person]);

	return view('performance.userhistory',['modules' => $modules,'data'=>$data,'datapdiser'=>$datapdiser,'datapdisaber'=>$datapdisaber,'datapdihacer'=>$datapdihacer,'datapditotal'=>$datapditotal]);		
	}


		//var_dump($person);
	}


	public function enable(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,1);
    return view('performance.enable',['modules' => $modules,'user' => $user]);


	}


	public function storeEnable(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,1);

    
    $state='';
    $input = $request->enable;
	    if ($input == 'Si') {
	    	$state=1;
            $updatereponses=Response::truncate();
            $updatepdis=Pdi::truncate();
        }

	   /* }else{
	    	$state=0;
            $updateusers=DB::UPDATE('UPDATE users
       	                             SET performance_on = ?',[$state]);
            $updatereponses=DB::UPDATE('UPDATE performance_responses
       	                        SET active = ?',[$state]);
            $updatepdis=DB::UPDATE('UPDATE performance_pdis
       	                        SET active = ?',[$state]);
	    }*/


        return view('yo',['modules' => $modules,'user' => $user]);


		
	}


public function users(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,1);

	$users=User::where('active','=','1')->get();

	 return view('performance.users',['modules' => $modules,'users' => $users]);
}


public function editUser(Request $request){

$user = Auth::user();
$application = new Application();
$modules = $application->getModules($user->id,1);


$userdata= User::where('id','=',$request->id);

$userdata=DB::SELECT('SELECT a.id AS id,
	                         a.name AS name,
	                         a.first_name AS first_name,
                             a.last_name AS last_name,
                             a.email AS email,
                             a.email_aux as email_aux,
                             a.extension AS extension,
                             a.phone AS phone,
                             b.name AS cargo,
                             c.name AS ubication
	                  FROM users a
	                  LEFT JOIN positions b
	                  ON b.id=a.position_id
	                  LEFT JOIN ubications c
	                  ON c.id=a.ubication_id
	                  WHERE a.id=?',[$request->id]);

$users=User::where('active','=','1')->get();



$id_leader=DB::SELECT('SELECT leader_id AS leader_id FROM users
	                   WHERE id=?',[$request->id]);


$leader=DB::SELECT('SELECT name AS leader_name,
	                       id AS leader_id
	                FROM users 
	                WHERE id=?',[$id_leader[0]->leader_id]);
/*$leader=DB::SELECT('SELECT users.name AS leader_name,
					       users.id AS leader_id
					       FROM users
					INNER JOIN profile_role
					ON profile_role.profile_id=users.profile_id
					WHERE profile_role.role_id=?',['11']);*/

        $leaders=DB::SELECT('SELECT u.name AS name,
								   u.id AS id,
							       us.leader_id as leader_id
								   FROM users u
							INNER JOIN users us 
							ON us.leader_id= u.id
							GROUP BY u.id,u.name,us.leader_id');


return view('performance.edit',['modules' => $modules,'user' => $user,'userdata'=>$userdata,'users'=>$users,'leader'=>$leader,'leaders'=>$leaders]);

}

public function edition(Request $request){

$user = Auth::user();
$application = new Application();
$modules = $application->getModules($user->id,1);

$users=User::where('active','=','1')->get();


$UPDATE=DB::UPDATE('UPDATE users
 	                 SET leader_id=?
 	                 WHERE id=?',[$request->leader_id,$request->id]);

 return view('performance.users',['modules' => $modules,'users' => $users]);

}


public function missing(Request $request){

		$user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,6);
        $names=array();

        $users = User::where('active','=',1)->get();
        $ubications = Ubication::where('active','=',1)->get();

        $leaders=DB::SELECT('SELECT u.name AS name,
								   u.id AS id,
							       us.leader_id as leader_id
								   FROM users u
							INNER JOIN users us 
							ON us.leader_id= u.id
							GROUP BY u.id,u.name,us.leader_id');

        $data=DB::SELECT('SELECT t1.name AS name,
						       t1.cedula AS cedula,
						       t1.profile_name AS profile,
						       us.name AS lider,
						       t1.ubication_name AS ubication
						  FROM users t1
						  LEFT JOIN ubications u
						  ON u.id=t1.ubication_id
						  LEFT JOIN profiles p
						  ON p.id= t1.profile_id
						  LEFT JOIN users us
						  ON us.id=t1.leader_id
						  LEFT JOIN performance_pdis t2
						 ON t2.evaluated_id = t1.id
						 WHERE t2.evaluated_id IS NULL AND
						       t1.active=?',[1]);

   	$request->session()->put('data', $data);

	return view('performance.reportmissing',['modules' => $modules,'user' => $user,'data'=>$data,'users'=>$users,'leaders'=>$leaders,'ubications'=>$ubications]);

}

public function files(Request $request){
	if ($request->hasFile('file')) {
		$file=$request->file('file');
		$name=time().$file->getClientOriginalName();
		$file->move(public_path().'/files/',$name);

$updatefiles=DB::UPDATE('UPDATE performance_pdi_histories
		                     SET file=? 
		                     WHERE YEAR(created_at)=? AND
		                           evaluated_id = ? AND
		                           dimension_id = ?',[time().$file->getClientOriginalName(),$request->year,$request->id_user,4]);


	$user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,1);

       $data=DB::SELECT('SELECT u.name AS name,
						        u.id AS id,
							    us.name AS jefe,
							    ub.name AS area,
							    DATE(p.created_at) AS date,
							    YEAR(DATE(p.created_at)) AS year,
							    MONTH(DATE(p.created_at)) AS month
						FROM users u
						INNER JOIN users us
						ON us.id = u.leader_id
						INNER JOIN performance_pdi_histories p
						ON p.evaluated_id= u.id
						INNER JOIN ubications ub
						ON ub.id= u.ubication_id
						WHERE u.id=? AND
						      p.dimension_id=?
						GROUP BY DATE(p.created_at),u.name,u.id,us.name,ub.name,p.created_at
				        ORDER BY YEAR(p.created_at) DESC,u.id DESC',[$request->id_user,1]);

       $datapdiser=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       DATE(created_at) AS date,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id=?
						ORDER BY id DESC'
						,[1,$request->id_user]);
       $datapdisaber=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						        DATE(created_at) AS date,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id = ?
						ORDER BY id DESC',[2,$request->id_user]);
       $datapdihacer=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       DATE(created_at) AS date,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id = ?
						ORDER BY id DESC',[3,$request->id_user]);
       $datapditotal=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       DATE(created_at) AS date,
						       followupdate AS followupdate,
						       file AS file
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id = ?
						ORDER BY id DESC',[4,$request->id_user]);

	return view('performance.userhistory',['modules' => $modules,'data'=>$data,'datapdiser'=>$datapdiser,'datapdisaber'=>$datapdisaber,'datapdihacer'=>$datapdihacer,'datapditotal'=>$datapditotal]);	
		
	}else{
	$user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,1);

       $data=DB::SELECT('SELECT u.name AS name,
						        u.id AS id,
							    us.name AS jefe,
							    ub.name AS area,
							    DATE(p.created_at) AS date,
							    YEAR(DATE(p.created_at)) AS year,
							    MONTH(DATE(p.created_at)) AS month
						FROM users u
						INNER JOIN users us
						ON us.id = u.leader_id
						INNER JOIN performance_pdi_histories p
						ON p.evaluated_id= u.id
						INNER JOIN ubications ub
						ON ub.id= u.ubication_id
						WHERE u.id=? AND
						      p.dimension_id=?
						GROUP BY DATE(p.created_at),u.name,u.id,us.name,ub.name,p.created_at
				        ORDER BY YEAR(p.created_at) DESC,u.id DESC',[$request->id_user,1]);

       $datapdiser=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       DATE(created_at) AS date,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id=?
						ORDER BY id DESC'
						,[1,$request->id_user]);
       $datapdisaber=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						        DATE(created_at) AS date,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id = ?
						ORDER BY id DESC',[2,$request->id_user]);
       $datapdihacer=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       DATE(created_at) AS date,
						       followupdate AS followupdate
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id = ?
						ORDER BY id DESC',[3,$request->id_user]);
       $datapditotal=DB::SELECT('SELECT evaluated_id AS id,
						       average AS average,
						       objective AS objective,
						       action AS action,
						       DATE(created_at) AS date,
						       followupdate AS followupdate,
						       file AS file
						FROM performance_pdi_histories
						WHERE dimension_id = ? AND
						      evaluated_id = ?
						ORDER BY id DESC',[4,$request->id_user]);

	return view('performance.userhistoryerror',['modules' => $modules,'data'=>$data,'datapdiser'=>$datapdiser,'datapdisaber'=>$datapdisaber,'datapdihacer'=>$datapdihacer,'datapditotal'=>$datapditotal]);
	}

 }


}
