<?php

namespace App\Http\Controllers;

use App\User;
use App\Application;
use App\Ubication;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
       
        return view('home');
    }
    public function yo()
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,1);
        return view('yo',['modules' => $modules,'user' => $user]);
    }

    public function service()
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,3);
        return view('service',['modules' => $modules,'user' => $user]);
    }

    public function process()
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,4);
        return view('process',['modules' => $modules]);
    }

    public function autogestion()
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,5);
        return view('invoice/autogestion',['modules' => $modules]);
    }


    public function informes()
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,6);

        $ubications= Ubication::where('active','=',1)
                                ->orderBy('id','ASC')
                                ->get();
        $reports=DB::SELECT('SELECT id AS id,
                                    report_name AS report,
                                    report_route AS route
                            FROM reports
                            WHERE ubication_id=? AND
                                  active=?',[$user->ubication_id,1]);

        return view('informes',['modules' => $modules,'user' => $user,'ubications'=>$ubications,'reports'=>$reports]);


    }


   public function informesrtc()
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,6);

        $ubications= Ubication::where('active','=',1)
                                ->orderBy('id','ASC')
                                ->get();
        $reports=DB::SELECT('SELECT id AS id,
                                    report_name AS report,
                                    report_route AS route
                            FROM reports
                            WHERE ubication_id=? AND
                                  active=?',[$user->ubication_id,1]);
        return view('informesrtc',['modules' => $modules,'user' => $user,'ubications'=>$ubications,'reports'=>$reports]);


    }


    public function admin()
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,7);

        $ubications= Ubication::where('active','=',1)
                                ->orderBy('id','ASC')
                                ->get();
        $users= User::where('active','=',1)
                    ->orderBy('id','ASC')
                    ->get();
        $reports=DB::SELECT('SELECT id AS id,
                                    report_name AS report,
                                    report_route AS route
                            FROM reports
                            WHERE ubication_id=? AND
                                  active=?',[$user->ubication_id,1]);
        $applications=DB::SELECT("SELECT id AS id,
                                          name AS name
                                  FROM applications
                                  WHERE active=?",[1]);
        $modulesfinal=DB::SELECT("SELECT id AS id,
                                    name AS name
                                  FROM modules
                                  WHERE active=?",[1]);
        $functions=DB::SELECT("SELECT id AS id,
                                    name AS name
                                  FROM functions
                                  WHERE active=?",[1]);

        $routes=DB::SELECT("SELECT route AS name
                                  FROM permission
                                  WHERE active=?
                                  GROUP BY route
                                  ",[1]);

        return view('admin',['modules' => $modules,'user' => $user,'ubications'=>$ubications,'reports'=>$reports,'users'=>$users,'applications'=>$applications,'modulesfinal'=>$modulesfinal,'functions'=>$functions,'routes'=>$routes]);


    }


    public function admindata()
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,7);

        $ubications= Ubication::where('active','=',1)
                                ->orderBy('id','ASC')
                                ->get();
        $users= User::where('active','=',1)
                    ->orderBy('id','ASC')
                    ->get();
        $reports=DB::SELECT('SELECT id AS id,
                                    report_name AS report,
                                    report_route AS route
                            FROM reports
                            WHERE ubication_id=? AND
                                  active=?',[$user->ubication_id,1]);
        $applications=DB::SELECT("SELECT id AS id,
                                          name AS name
                                  FROM applications
                                  WHERE active=?",[1]);
        $modulesfinal=DB::SELECT("SELECT id AS id,
                                    name AS name
                                  FROM modules
                                  WHERE active=?",[1]);
        $functions=DB::SELECT("SELECT id AS id,
                                    name AS name
                                  FROM functions
                                  WHERE active=?",[1]);

        $routes=DB::SELECT("SELECT route AS name
                                  FROM permission
                                  WHERE active=?
                                  GROUP BY route
                                  ",[1]);

        return view('admindata',['modules' => $modules,'user' => $user,'ubications'=>$ubications,'reports'=>$reports,'users'=>$users,'applications'=>$applications,'modulesfinal'=>$modulesfinal,'functions'=>$functions,'routes'=>$routes]);


    }



    public function adminpermissions(Request $request)
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,7);


        $input = $request->except('_token');

        $aplication_name=DB::SELECT('SELECT name AS name FROM applications
                                     WHERE id=?',[$request->Aplicaciones]);

        $module_name=DB::SELECT('SELECT name AS name FROM modules
                                     WHERE id=?',[$request->Modulos]);

        $function_name=DB::SELECT('SELECT name AS name FROM functions
                                     WHERE id=?',[$request->Funciones]);



        $permission_configuration=DB::SELECT('SELECT aplication_id AS aplication,
                                                     module_id AS module,
                                                     function_id AS functions
                                              FROM routes
                                              WHERE active=?',[1]);

      
       $route_function=DB::SELECT('SELECT route as route 
                                    FROM routes 
                                    WHERE function_name =?',[$function_name[0]->name]);

        
        $insert=DB::INSERT('INSERT INTO permission (id_user, aplication_id, aplication_name, module_id,module_name,function_id, function_name,route,active)
            VALUES (?,?,?,?,?,?,?,?,?);',[$request->id_user,$request->Aplicaciones,$aplication_name[0]->name,$request->Modulos,$module_name[0]->name,$request->Funciones,$function_name[0]->name,$route_function[0]->route,1]);

        $ubications= Ubication::where('active','=',1)
                                ->orderBy('id','ASC')
                                ->get();
        $users= User::where('active','=',1)
                    ->orderBy('id','ASC')
                    ->get();
        $reports=DB::SELECT('SELECT id AS id,
                                    report_name AS report,
                                    report_route AS route
                            FROM reports
                            WHERE ubication_id=? AND
                                  active=?',[$user->ubication_id,1]);
        $applications=DB::SELECT("SELECT id AS id,
                                          name AS name
                                  FROM applications
                                  WHERE active=?",[1]);
        $modulesfinal=DB::SELECT("SELECT id AS id,
                                    name AS name
                                  FROM modules
                                  WHERE active=?",[1]);
        $functions=DB::SELECT("SELECT id AS id,
                                    name AS name
                                  FROM functions
                                  WHERE active=?",[1]);

        $routes=DB::SELECT("SELECT route AS name
                                  FROM permission
                                  WHERE active=?
                                  GROUP BY route
                                  ",[1]);

        return view('admindata',['modules' => $modules,'user' => $user,'ubications'=>$ubications,'reports'=>$reports,'users'=>$users,'applications'=>$applications,'modulesfinal'=>$modulesfinal,'functions'=>$functions,'routes'=>$routes]);


    }

    public function VerificacionModal(Request $request){

      /*  $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,0);


        if ($user->modal_state == 1) {
          return view('welcome');
        }else{
          return view('/updatedata',['modules' => $modules,'user'=>$user]);
        }*/


        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,0);

        if (($user->modal_state == 1) && ($user->active == 1)) {
          return view('welcome');
        }elseif(($user->modal_state == 0) && ($user->active == 1)){
          return view('/updatedata',['modules' => $modules,'user'=>$user]);
        }else{
              $error = 1;

              return view('auth/login',['modules'=>$modules,'error'=>$error]);
        }
        


        
    }

    public function updateuserdata(Request $request){
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,0);

       if ($request->id_user) {
        $id_user=$request->id_user;

        $telefono_fijo='';

        $nombre_unidad='';
        $apto ='';
        $cantidad_hijos = '';
        $estudio_actual = '';
        $cantidad_mascotas = '';
        $especies_mascotas = '';
        $nombres_mascotas = '';
        $experiencia_conduccion='';
        $tipo_vehiculo = '';
        $categoria_licencia = '';
        $vencimiento_licencia = '';
        $placa_vehiculo = '';
        $vehiculo_marca = '';
        $vehiculo_modelo = '';
        $sede_final='';
        $estrato='';
        $vacunaste='';

        $cantidad_hijos_ingresados=$request->countfieldsadd;

        if ($request->sede_labora == 'Otro') {
          $sede_final = $request->otra_sede;
        }else{
          $sede_final = $request->sede_labora;
        }

        if ($request->mascotas == 'Si') {
            $fields=[
              'mascotas'=>'required_with:cantidad_mascotas,especies_mascotas,nombres_mascotas'
            ];
        }
        if ($request->mascotas == 'No') {
          $cantidad_mascotas = 0;
          $especies_mascotas = 'N/A';
          $nombres_mascotas = 'N/A';
        }

        if ($request->telefono_fijo) {
          $telefono_fijo='N/A';
        }

    if ($request->mascotas == 'Si') {
      $this->validate($request,$fields);
    }


        if ($request->nombre_unidad == '') {
          $nombre_unidad='N/A';
          $apto = 'N/A';
        }

        if ($request->posee_hijos == 'No') {

          $cantidad_hijos = 0;
        }

        if ($request->EstudiaActualmente == 'No') {
          $estudio_actual = 'N/A';
        }



        if ($request->vehiculo == 'No') {
          $experiencia_conduccion = 'N/A';
          $tipo_vehiculo = 'N/A';
          $categoria_licencia = 'N/A';
          $vencimiento_licencia = 'N/A';
          $placa_vehiculo = 'N/A';
          $vehiculo_marca = 'N/A';
          $vehiculo_modelo = 'N/A';
        }



      if ($request->posee_hijos == 'Si') {
            for ($i=1; $i <= $cantidad_hijos_ingresados; $i++) {
              $nombre_hijo='nombre_hijo'.$i;
              $tipo_documento_hijo='TipoDocumentoHijo'.$i;
              $numero_documento_hijo='numero_documento_hijo'.$i;
              $fecha_nacimiento_hijo='fecha_nacimiento_hijo'.$i; 
              $insert_hijos =DB::INSERT("INSERT INTO hijos_usuarios
                                         (id_user,nombre_hijo,tipo_documento_hijo,numero_documento_hijo,fecha_nacimiento_hijo) VALUES (?,?,?,?,?)",[$request->id_user,$request->$nombre_hijo,$request->$tipo_documento_hijo,$request->$numero_documento_hijo,$request->$fecha_nacimiento_hijo]);
            }

      }else{
      	 	$insert_hijos =DB::table('hijos_usuarios')->where('id_user',$id_user)->delete();
      }

        


        $updateuser=DB::UPDATE("UPDATE users
                                SET gender = ?,
                                    telefono_fijo = ?,
                                    celular_personal = ?,
                                    direccion_residencia=?,
                                    barrio=?,
                                    nombre_unidad=?,
                                    apto = ?,
                                    ciudad =?,
                                    estado_civil = ?,
                                    email_aux = ?,
                                    tipo_vivienda =?,
                                    conyuge = ?,
                                    grupo_sanguineo = ?,
                                    posee_hijos =?,
                                    cantidad_hijos =?,
                                    contacto_emergencia =?,
                                    parentesco_contacto_emergencia =?,
                                    celular_contacto_emergencia =?,
                                    nivel_estudios =?,
                                    formacion_actual =?,
                                    estudia_actualmente =?,
                                    estudio_actual =?,
                                    Empresa = ?,
                                    eps = ?,
                                    fondo_pensiones = ?,
                                    fondo_cesantias = ?,
                                    grupo_trabajo = ?,
                                    mascotas = ?,
                                    cantidad_mascotas = ?,
                                    especie_mascotas = ?,
                                    nombre_mascotas = ?,
                                    vehiculo = ?,
                                    experiencia_conduccion = ?,
                                    tipo_vehiculo = ?,
                                    categoria_licencia = ?,
                                    vencimiento_licencia = ?,
                                    placa_vehiculo = ?,
                                    vehiculo_marca = ?,
                                    vehiculo_modelo = ?,
                                    medio_transporte = ?,
                                    tiempo_promedio_trayecto = ?,
                                    accidentes_transito = ?,
                                    accidentes_grave = ?,
                                    tipo_trabajo = ?,
                                    factores_riesgo_humanos = ?,
                                    factores_riesgo_entorno = ?,
                                    factores_riesgo_vehiculo = ?,
                                    propuestas_internas = ?,
                                    sede_labora = ?,
                                    email = ?,
                                    phone = ?,
                                    extension = ?,
                                    modal_state = ?,
                                    estrato=?,
                                    vacunado_covid=?
                                WHERE id = ?",[$request->genero,$request->telefono_fijo,$request->celular_personal,$request->direccion_residencia,$request->barrio,$request->nombre_unidad,$request->apto,$request->ciudad,$request->estado_civil,$request->correo_personal,$request->tipo_vivienda,$request->conyuge,$request->grupo_sanguineo,$request->posee_hijos,$request->cantidad_hijos,$request->contacto_emergencia,$request->parentesco_contacto_emergencia,$request->celular_contacto_emergencia,$request->nivel_estudios,$request->formacion_actual,$request->EstudiaActualmente,$request->estudio_actual,$request->Empresa,$request->eps,$request->fondo_pensiones,$request->fondo_cesantias,$request->grupo_trabajo,$request->mascotas,$request->cantidad_mascotas,$request->especies_mascotas,$request->nombres_mascotas,$request->vehiculo,$request->experiencia_conduccion,$request->tipo_vehiculo,$request->categoria_licencia,$request->vencimiento_licencia,$request->placa_vehiculo,$request->vehiculo_marca,$request->vehiculo_modelo,$request->medio_transporte,$request->tiempo_promedio_trayecto,$request->accidentes_transito,$request->accidente_grave,$request->tipo_trabajo,$request->factores_riesgo_humanos,$request->factores_riesgo_entorno,$request->factores_riesgo_vehiculo,$request->propuestas_internas,$sede_final,$request->correo_corporativo,$request->celular_corporativo,$request->extension,1,$request->estrato,$request->vacuna,$request->id_user]);
       }else{
        $telefono_fijo='';

        $nombre_unidad='';
        $apto ='';
        $cantidad_hijos = '';
        $estudio_actual = '';
        $cantidad_mascotas = '';
        $especies_mascotas = '';
        $nombres_mascotas = '';
        $experiencia_conduccion='';
        $tipo_vehiculo = '';
        $categoria_licencia = '';
        $vencimiento_licencia = '';
        $placa_vehiculo = '';
        $vehiculo_marca = '';
        $vehiculo_modelo = '';
        $sede_final='';

        

        $cantidad_hijos_ingresados=$request->countfieldsadd;

        if ($request->sede_labora == 'Otro') {
          $sede_final = $request->otra_sede;
        }else{
          $sede_final = $request->sede_labora;
        }

        if ($request->mascotas == 'Si') {
            $fields=[
              'mascotas'=>'required_with:cantidad_mascotas,especies_mascotas,nombres_mascotas'
            ];
        }
        if ($request->mascotas == 'No') {
          $cantidad_mascotas = 0;
          $especies_mascotas = 'N/A';
          $nombres_mascotas = 'N/A';
        }

        if ($request->telefono_fijo) {
          $telefono_fijo='N/A';
        }

    if ($request->mascotas == 'Si') {
      $this->validate($request,$fields);
    }


        if ($request->nombre_unidad == '') {
          $nombre_unidad='N/A';
          $apto = 'N/A';
        }

        if ($request->posee_hijos == 'No') {
          $cantidad_hijos = 0;
        }

        if ($request->EstudiaActualmente == 'No') {
          $estudio_actual = 'N/A';
        }



        if ($request->vehiculo == 'No') {
          $experiencia_conduccion = 'N/A';
          $tipo_vehiculo = 'N/A';
          $categoria_licencia = 'N/A';
          $vencimiento_licencia = 'N/A';
          $placa_vehiculo = 'N/A';
          $vehiculo_marca = 'N/A';
          $vehiculo_modelo = 'N/A';
        }

      if ($request->posee_hijos == 'Si') {
            for ($i=1; $i <= $cantidad_hijos_ingresados; $i++) {
              $nombre_hijo='nombre_hijo'.$i;
              $tipo_documento_hijo='TipoDocumentoHijo'.$i;
              $numero_documento_hijo='numero_documento_hijo'.$i;
              $fecha_nacimiento_hijo='fecha_nacimiento_hijo'.$i; 
              $insert_hijos =DB::INSERT("INSERT INTO hijos_usuarios
                                         (id_user,nombre_hijo,tipo_documento_hijo,numero_documento_hijo,fecha_nacimiento_hijo) VALUES (?,?,?,?,?)",[$user->id,$request->$nombre_hijo,$request->$tipo_documento_hijo,$request->$numero_documento_hijo,$request->$fecha_nacimiento_hijo]);
            }

      }else{
      	 	$insert_hijos =DB::table('hijos_usuarios')->where('id_user',$user->id)->delete();
      }


        


        $updateuser=DB::UPDATE("UPDATE users
                                SET gender = ?,
                                    telefono_fijo = ?,
                                    celular_personal = ?,
                                    direccion_residencia=?,
                                    barrio=?,
                                    nombre_unidad=?,
                                    apto = ?,
                                    ciudad =?,
                                    estado_civil = ?,
                                    email_aux = ?,
                                    tipo_vivienda =?,
                                    conyuge = ?,
                                    grupo_sanguineo = ?,
                                    posee_hijos =?,
                                    cantidad_hijos =?,
                                    contacto_emergencia =?,
                                    parentesco_contacto_emergencia =?,
                                    celular_contacto_emergencia =?,
                                    nivel_estudios =?,
                                    formacion_actual =?,
                                    estudia_actualmente =?,
                                    estudio_actual =?,
                                    Empresa = ?,
                                    eps = ?,
                                    fondo_pensiones = ?,
                                    fondo_cesantias = ?,
                                    grupo_trabajo = ?,
                                    mascotas = ?,
                                    cantidad_mascotas = ?,
                                    especie_mascotas = ?,
                                    nombre_mascotas = ?,
                                    vehiculo = ?,
                                    experiencia_conduccion = ?,
                                    tipo_vehiculo = ?,
                                    categoria_licencia = ?,
                                    vencimiento_licencia = ?,
                                    placa_vehiculo = ?,
                                    vehiculo_marca = ?,
                                    vehiculo_modelo = ?,
                                    medio_transporte = ?,
                                    tiempo_promedio_trayecto = ?,
                                    accidentes_transito = ?,
                                    accidentes_grave = ?,
                                    tipo_trabajo = ?,
                                    factores_riesgo_humanos = ?,
                                    factores_riesgo_entorno = ?,
                                    factores_riesgo_vehiculo = ?,
                                    propuestas_internas = ?,
                                    sede_labora = ?,
                                    email = ?,
                                    phone = ?,
                                    extension = ?,
                                    modal_state = ?,
                                    estrato=?,
                                    vacunado_covid=?
                                WHERE id = ?",[$request->genero,$request->telefono_fijo,$request->celular_personal,$request->direccion_residencia,$request->barrio,$request->nombre_unidad,$request->apto,$request->ciudad,$request->estado_civil,$request->correo_personal,$request->tipo_vivienda,$request->conyuge,$request->grupo_sanguineo,$request->posee_hijos,$request->cantidad_hijos,$request->contacto_emergencia,$request->parentesco_contacto_emergencia,$request->celular_contacto_emergencia,$request->nivel_estudios,$request->formacion_actual,$request->EstudiaActualmente,$request->estudio_actual,$request->Empresa,$request->eps,$request->fondo_pensiones,$request->fondo_cesantias,$request->grupo_trabajo,$request->mascotas,$request->cantidad_mascotas,$request->especies_mascotas,$request->nombres_mascotas,$request->vehiculo,$request->experiencia_conduccion,$request->tipo_vehiculo,$request->categoria_licencia,$request->vencimiento_licencia,$request->placa_vehiculo,$request->vehiculo_marca,$request->vehiculo_modelo,$request->medio_transporte,$request->tiempo_promedio_trayecto,$request->accidentes_transito,$request->accidente_grave,$request->tipo_trabajo,$request->factores_riesgo_humanos,$request->factores_riesgo_entorno,$request->factores_riesgo_vehiculo,$request->propuestas_internas,$sede_final,$request->correo_corporativo,$request->celular_corporativo,$request->extension,1,$request->estrato,$request->vacuna,$user->id]);
       }

        return view('welcome');
    }


    public function UpdateSelfUserData(Request $request){

        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,0);
        return view('/updatedata',['modules' => $modules,'user'=>$user]);

    }

    public function UpdateUsersData(){
          $user = Auth::user();
          $application = new Application();
          $modules = $application->getModules($user->id,1);
          $users=User::where('active','=','1')->get();
          return view('performance.usersdata',['modules' => $modules,'users' => $users]);
    }

    public function UpdateDataOthersUsers(Request $request){

        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,0);

        $userupdate=$request->id;

        $userupdatename=DB::SELECT('SELECT name AS name
                                    FROM users
                                    WHERE id=?',[$userupdate]);

        $userdata=User::where('id','=',$userupdate)->get();


         
        return view('/updateotherusersdata',['modules' => $modules,'user'=>$user,'userupdate'=>$userupdate,'username'=>$userupdatename[0]->name,'userdata'=>$userdata]);

    }


  public function empleados(Request $request){
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,7);

        return view('/empleados',['modules' => $modules,'user'=>$user]);

  }

  public function actualizacionjefes(Request $request){

        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,7);
        
        header("Location: https://flora.tierragro.com/storage/prueba.php",true,303);  
        exit();  
        //require_once('https://flora.tierragro.com/storage/prueba.php');


  }

  public function descontinuados(Request $request){

        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,7);
        
        return view('/empleadosdescontinuados',['modules' => $modules,'user'=>$user]); 
        //require_once('https://flora.tierragro.com/storage/prueba.php');


  }


  public function actualizacionempleadossalientes(Request $request){

        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,7);
        
        header("Location: https://flora.tierragro.com/storage/prueba2.php",true,303);  
        exit();  
        //require_once('https://flora.tierragro.com/storage/prueba.php');


  }


  public function empleadosnuevos(Request $request){

        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,7);
        
        return view('/empleadosnuevos',['modules' => $modules,'user'=>$user]); 
        //require_once('https://flora.tierragro.com/storage/prueba.php');


  }


  public function actualizacionempleadosnuevos(Request $request){

        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,7);
        
        header("Location: https://flora.tierragro.com/storage/prueba3.php",true,303);  
        exit();  

  }

    //Nueva funcion para la actualizacion de campos de: cargo, centro de costos y jefe
  
    public function actualizacioncamposempleados(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,7);
    
    return view('/actualizacioncamposempleados',['modules' => $modules,'user'=>$user]); 
  


}  

public function actualizacioncamposempleadosfinal(Request $request){

  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,7);
  
  
  header("Location: https://flora.tierragro.com/storage/prueba4.php",true,303);  
        exit();  


} 


  public function procesoterminado(Request $request){

        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,7);
        
        return view('/procesoterminado',['modules' => $modules,'user'=>$user]); 
        //require_once('https://flora.tierragro.com/storage/prueba.php');


  }  


}
