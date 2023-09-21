<?php

namespace App\Http\Controllers;

use App\User;
use App\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;



class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $modules = [];
        return view('user.show',['modules' => $modules,'user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
         
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();


        if ( isset($input['notification']) ) {
            if ( $input['notification'] == 'on' ) {
                $input['notification']= 1;
            }else{
               $input['notification']= 0;
            }
        }else{
         $input['notification']= 0;   
        }

        $user = Auth::user();

        $user->email = $input['email'];
        $user->extension = $input['extension'];
        $user->phone = $input['phone'];
        $user->email_aux = $input['email_aux'];
        $user->notification = $input['notification'];

        $user->save();

        return view('welcome');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function directory()
    {
        
    }

    public function resetinside(){
        $modules = [];
        $error=0;
        return view('resetinside',['modules'=>$modules,'error'=>$error]);
    }

    public function updatepasswordinside(Request $request){
        $correo=$request->email;
        $modules = [];
        $user = Auth::user();

        $verification=DB::SELECT('SELECT id AS user_id,
                                         first_name AS name,
                                  count(id) AS cantidad
                                  FROM users
                                  WHERE email = ? OR
                                         email_aux = ?
                                  GROUP BY id,first_name',[$correo,$correo]);
        $cantidafinal=count($verification);
        if ($cantidafinal > 0) {
            $Type='updatepassword';
        $data=[$verification[0]->name,$Type,$verification[0]->user_id];
        $request->session()->put('User', $verification[0]->name);
        $request->session()->put('User_id', $verification[0]->user_id);
        Mail::to($correo)->send(new SendMail($data));
             $error = 0;
             return view('/updatepassword',['modules'=>$modules,'error'=>$error]);
        }else{
            $error = 1;
            return view('/updatepassword',['modules'=>$modules,'error'=>$error]);
        }

        /*$finalpassword = Hash::make($request->password);
        if ($password != $repassword) {
            $error = 1;
         return view('resetinside',['modules'=>$modules,'error'=>$error]);
        }else{
            $error = 3;
            $password_change= DB::UPDATE('UPDATE users
                                          SET password = ?
                                          WHERE id=?',[$finalpassword,$user->id]);
            return view('resetinside',['modules'=>$modules,'error'=>$error]);
        }*/


    }

public function cambiocontraseña(Request $request){
    $id=$request->id;
    $error=$request->error;

    return view('/cambiocontraseñafinal',['id'=>$id,'error'=>$error]);
 }

public function updatepasswordfinal(Request $request){
    $modules = [];
    $id=$request->id_usuario;
    $password=$request->password;
    $repassword=$request->password_confirmation;
    $finalpassword = Hash::make($request->password);
        if ($password != $repassword) {
            $error = 1;
             return view('/cambiocontraseñafinal',['modules'=>$modules,'error'=>$error,'id'=>$id]);
        }else{
            $error = 3;
            $password_change= DB::UPDATE('UPDATE users
                                          SET password = ?
                                          WHERE id=?',[$finalpassword,$id]);
            return view('/cambiocontraseñafinal',['modules'=>$modules,'error'=>$error,'id'=>$id]);
        }
 }


 public function salidalogin(){
    $modules = [];
    $user = Auth::user();
    $error = 0;
    return view('resetinside',['modules'=>$modules,'error'=>$error]);
 }

 public function actualizacionpassword(Request $request){
    $modules = [];
    $user = Auth::user();
    $password = $request->password;
    $repassword = $request->password_confirmation;

    $finalpassword = Hash::make($request->password);
        if ($password != $repassword) {
            $error = 1;
         return view('resetinside',['modules'=>$modules,'error'=>$error]);
        }else{
            $error = 3;
            $password_change= DB::UPDATE('UPDATE users
                                          SET password = ?
                                          WHERE id=?',[$finalpassword,$user->id]);
            return view('resetinside',['modules'=>$modules,'error'=>$error]);
        }
 }


 public function users(Request $request){

    if ($request->has('active')) {
       // $users=User::where('active',true)->get();
       $users=DB::SELECT("SELECT us.id AS id,
                                us.name AS nombre,
                                us.cedula AS cedula,
                                us.email AS correo_corporativo,
                                us.email_aux AS correo_personal,
                                us.ubication_name AS area,
                                REGEXP_REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(us.profile_name,'Í', 'I'),
                                                '[^a-zA-Z0-9-]+',
                                                ' '),'Ó','O'),'É','E'),'Ú','U'),'Á','A'),'Ñ','N') AS cargo,
                                us.leader_id AS id_jefe, 
                                u.name AS jefe FROM  users us
                            INNER JOIN users u
                            ON u.id=us.leader_id
                            WHERE us.active=?",[1]);
    }else{
        $users=DB::SELECT("SELECT us.id AS id,
                                us.name AS nombre,
                                us.cedula AS cedula,
                                us.email AS correo_corporativo,
                                us.email_aux AS correo_personal,
                                us.ubication_name AS area,
                                REGEXP_REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(us.profile_name,'Í', 'I'),
                                                '[^a-zA-Z0-9-]+',
                                                ' '),'Ó','O'),'É','E'),'Ú','U'),'Á','A'),'Ñ','N') AS cargo,
                                us.leader_id AS id_jefe, 
                                u.name AS jefe FROM  users us
                            INNER JOIN users u
                            ON u.id=us.leader_id
                            WHERE us.active=?",[1]);
      
    }
    

    return response()->json($users);

 }

 public function usersFinder(Request $request){
    $lookfor=$request->busqueda;


    $users=DB::SELECT("SELECT   us.id AS id,
                                us.name AS nombre,
                                us.cedula AS cedula,
                                us.email AS correo_corporativo,
                                us.email_aux AS correo_personal,
                                us.ubication_name AS area,
                                REGEXP_REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(us.profile_name,'Í', 'I'),
                                                '[^a-zA-Z0-9-]+',
                                                ' '),'Ó','O'),'É','E'),'Ú','U'),'Á','A'),'Ñ','N') AS cargo,
                                us.leader_id AS id_jefe, 
                                u.name AS jefe FROM  users us
                        INNER JOIN users u
                        ON u.id=us.leader_id
                        WHERE us.active=? AND
                                (us.cedula LIKE ? OR
                                us.email LIKE ? OR
                                us.email_aux LIKE ? OR
                                us.ubication_name LIKE ? OR
                                us.name LIKE ?)",[1,"%".$lookfor."%","%".$lookfor."%","%".$lookfor."%","%".$lookfor."%","%".$lookfor."%"]);

   return response()->json($users);

 }

 public function login(Request $request){
    
    $response =["status"=>400,'msg'=>''];

    $data=json_decode($request->getContent());

    $user = User::where('cedula',$data->cedula)->first();

    if ($user) {
        if (Hash::check($data->password,$user->password)) {
            $token = $user->createToken("example");
            $response['status']=200;
            $response['msg']=$token->plainTextToken;
        }else{
            $response['msg'] = "Credenciales erradas";
        }
    }else{
        $response['msg']= "Usuario no encontrado";
    }

    return response()->json($response);
 }



 public function suppliers(Request $request){

    if ($request->has('active')) {
       $suppliers=DB::SELECT("SELECT nit AS nit,
                            sap_code AS sap_code,
                            name AS name
                        FROM suppliers
                        WHERE active =?",[1]);
    }else{
        $suppliers=DB::SELECT("SELECT nit AS nit,
                                sap_code AS sap_code,
                                name AS name
                        FROM suppliers
                        WHERE active =?",[1]);
                            
    }
    

    return response()->json($suppliers);

 }


 public function finderSupplier(Request $request){


    if ($request->nit != '') {

        $suppliers = Supplier::where('nit', 'LIKE', '%'.$request->nit.'%')
		->get();

       /* $suppliers=DB::SELECT("SELECT nit AS nit,
                                    sap_code AS sap_code,
                                    name AS name
                                FROM suppliers
                                WHERE nit LIKE ?",[$request->nit]);*/
        return response()->json($suppliers);
    }elseif($request->name != ''){
        $suppliers = Supplier::where('name', 'LIKE', '%'.$request->name.'%')
        ->get();
        return response()->json($suppliers);

    }else{
        $mensaje="Debe seleccionar la busqueda por nit o por nombre";
        return response()->json($mensaje);
    }

   /* if ($request->has('active')) {
       $suppliers=DB::SELECT("SELECT nit AS nit,
                            sap_code AS sap_code,
                            name AS name
                        FROM suppliers
                        WHERE active =?",[1]);
    }else{
        $suppliers=DB::SELECT("SELECT nit AS nit,
                                sap_code AS sap_code,
                                name AS name
                        FROM suppliers
                        WHERE active =?",[1]);
                            
    }
    

    return response()->json($suppliers);*/

 }


 public function findUserMail(Request $request){

    $email = User::where('email', 'LIKE', '%'.$request->username.'%')
    ->get();

   /* $suppliers=DB::SELECT("SELECT nit AS nit,
                                sap_code AS sap_code,
                                name AS name
                            FROM suppliers
                            WHERE nit LIKE ?",[$request->nit]);*/
    return response()->json($email);


 }


}
