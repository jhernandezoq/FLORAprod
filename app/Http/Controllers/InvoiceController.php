<?php

namespace App\Http\Controllers;

use App\Mail\NofiticationInvoiceMail;
use App\Mail\NofiticationInvoiceMailCost;
use App\Invoice;
use App\Flow;
use App\Supplier;
use App\Log;
use App\Company;
use App\Approver;
use App\CostCenter;
use App\Distribution;
use App\Application;
use App\User;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use PDF;
//use RealRashid\SweetAlert\Facades\Alert;



class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $input = $request->all();

        $user = Auth::user();
        $application = new Application();
       
        if ($user) {

        $modules = $application->getModules($user->id,4);


        $invoice = new Invoice();
        $invoices = $invoice->getActives(Auth::id());
        $countInvoices = count($invoices);


        return view('invoice.index',['modules' => $modules,'user' => $user,'invoices' => $invoices,'countInvoices' => $countInvoices]);
      }else{
         return view('welcome');
      }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,4);

        $companies = Company::where('active','=',1)->get();
        
        $flows = DB::SELECT('SELECT f.id AS id,
                    f.name AS name
              FROM invoice_flows f
              INNER JOIN invoice_approvers a
              ON a.flow_id = f.id 
              WHERE f.active=? AND
                    a.user_id = ? 
            GROUP BY f.id', [
              1,
              $user->id,
            ]);

        $cuentas = DB::SELECT('SELECT id AS id, Cuenta AS cuenta FROM cuentas_cecos');

        $suppliers = Supplier::select('suppliers.id', 'suppliers.nit', 'suppliers.name')
        ->where('active', 1)
        ->get();    

        $day = intval(date("j"));

        $typeerror=0;

        if ($day> 25) {
            $typeerror=1;
        }
        return view('invoice.create',[
          'modules' => $modules,
          'user' => $user,
          'companies' => $companies,
          'flows' => $flows,
          'suppliers' => $suppliers,
          'typeerror'=>$typeerror,
          'cuentas'=>$cuentas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $supplier_info='';
        $typeerror='';

        if ($input['supplier_id'] == '') {
            $supplier_info = $input['id_supplier'];
        }
        else{
            $supplier_info = $input['supplier_id'];
        }

        $validate = DB::SELECT('SELECT COUNT(id) AS amount FROM 
              invoices 
              WHERE number = ? AND
              supplier_id = ?', [ 
                $input['number'], 
                $supplier_info,
        ]);

        if( ( $validate[0]->amount ) != 0 ){
                $user = Auth::user();
                $application = new Application();
                $modules = $application->getModules($user->profile->id,4);

                $companies = Company::where('active','=',1)->get();
                $flows = DB::SELECT('SELECT f.id AS id,
                                    f.name AS name
                             FROM invoice_flows f
                             INNER JOIN invoice_approvers a
                             ON a.flow_id = f.id 
                             WHERE f.active=? AND
                                   a.user_id = ? 
                            GROUP BY f.id',[1,$user->id]);

                $suppliers = Supplier::where('active','=',1)
                            ->orderby('name','asc')
                            ->get();

                $typeerror=2;

                $cuentas = DB::SELECT('SELECT id AS id, Cuenta AS cuenta FROM cuentas_cecos');

                return view('invoice.duplicate', [
                  'modules' => $modules,
                  'user' => $user,
                  'companies' => $companies,
                  'flows' => $flows,
                  'suppliers' => $suppliers,
                  'typeerror'=>$typeerror,
                  'cuentas' => $cuentas,
                ]);
        }
        else{
                $user = Auth::user();
                
                $file = $request->file('file');
                if ($request->hasFile('file')) 
                {
                    
                    $subtotal=str_replace('.','',$input['subtotal']);
                    $iva=str_replace('.','',$input['iva']);
                    $total=str_replace('.','',$input['total']);

                    $invoice = new Invoice();
                    $invoice->number = $input['number'];
                    $invoice->flow_id = $input['flow_id'];
                    $invoice->supplier_id = $supplier_info;
                    $invoice->create_date = $input['create_date'];
                    $invoice->due_date = $input['due_date'];
                    $invoice->company = $input['company_id'];
                    // $invoice->cuenta = $input['cuenta'];

                    $invoice->concept = $input['concept'];
                    $invoice->subtotal = str_replace(',','.',$subtotal);
                    $invoice->iva = str_replace(',','.',$iva);
                    $invoice->total = str_replace(',','.',$total);
                    $invoice->currency = $input['currency'];
                    $invoice->priority = $input['priority'];            
                    
                    $ext = $file->getClientOriginalExtension();
                    $nombre = $input['supplier_id']."_".$input['number']."_".Str::random(6).".".$ext;
                    $invoice->file = $nombre;
                    \Storage::disk('facturas')->put($nombre,  \File::get($file));


                    $invoice->save();

                    
                    $log = new Log();
                    $log->invoice_id = $invoice->id;
                    $log->user_id = Auth::id();
                    $log->state_id = $input['state_id'];
                    if ($input['description'] != null) {
                        $log->description = $input['description'];
                    }else{
                        $log->description = 'Factura en proceso...';
                    }

                    $approver = Approver::where('flow_id','=',$input['flow_id'])
                                        ->where('order','=',1)->first();

                    
                    // $log->next_user_id = $approver->user_id;
                    $log->next_user_id = $user->id;
                    $log->save();

                    return redirect()->route('invoices');
                }
                else{
                    $user = Auth::user();
                    $application = new Application();
                    $modules = $application->getModules($user->profile->id,4);

                    $companies = Company::where('active','=',1)->get();
                    $flows = Flow::where('active','=',1)->get();
                    $suppliers = Supplier::where('active','=',1)
                                ->orderby('name','asc')
                                ->get();
                    return view('invoice.error', [
                      'modules' => $modules,
                      'user' => $user,
                      'companies' => $companies,
                      'flows' => $flows,
                      'suppliers' => $suppliers
                    ]);            
                }

        }


    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $id_user = $request->id_user;
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($id_user,4);

        //$validador_contable_aprueba = config('app.global')['validador_contable_aprueba'];
        $validador_contable_aprueba = 1650;

        $id=$request->id;


        $supplier_name=$request->supplier_name;
        $invoice = Invoice::find($id);
        $prov = $supplier_name;


        $invoiceCC =DB::SELECT('SELECT c.name AS name
                                  FROM cost_centers c
                                  INNER JOIN distributionscc d
                                  ON d.cost_center_id= c.id
                                  WHERE d.invoice_id=?',[$id]);

        $invoiceCCAutorizations =DB::SELECT('SELECT autorizacion AS name
        FROM invoice_logg 
        WHERE invoice_id=?',[$id]);
        $totalusers=DB::SELECT('SELECT id AS id, 
                                      name AS name 
                                FROM users
                                WHERE active=?',[1]);

        $totalubications=DB::SELECT('SELECT ubication_name AS ubication_name 
        FROM users
        GROUP BY ubication_name');
        
        $flow = $invoice->flow;

        if ($flow->id != 60) {
                            $approver = Approver::where('user_id','=',$id_user)
                            ->where('flow_id','=',$flow->id)
                            ->first();



                  // $init = $approver->order - 2;
                  // $end = $approver->order + 3;

                  $number_interventions=DB::SELECT('SELECT COUNT(next_user_id) AS cantidad
                                                FROM invoice_logg
                                                WHERE next_user_id=? AND
                                                      invoice_id=?',[$id_user,$id]);

                  $maxorder=DB::SELECT('SELECT max(a.order) AS orden 
                                      FROM invoice_approvers a
                                      WHERE a.flow_id=?',[$flow->id]);


                  $typeapprover='';
                  if ($number_interventions[0]->cantidad == 1) {
                    $typeapprover=DB::SELECT('SELECT min(a.role_id) AS typeapprover,
                                              a.order AS orden
                                            FROM invoice_approvers a
                                            WHERE a.flow_id=? AND
                                                  a.user_id=? AND
                                                  a.active = ?
                                            GROUP BY a.role_id,a.order',[$flow->id,$id_user,1]);
                  }else{
                    $typeapprover=DB::SELECT('SELECT a.role_id AS typeapprover,
                                                      a.order AS orden
                                                    FROM invoice_approvers a
                                                    WHERE a.flow_id=? AND
                                                        a.user_id=? AND
                                                        a.active = ? AND
                                                        a.id=(SELECT MAX(id) FROM invoice_approvers WHERE user_id=? AND flow_id=?)
                                                    GROUP BY a.role_id,a.order',[$flow->id,$id_user,1,$id_user,$flow->id]);
                                          
                                              
                  }

                  $diference=$maxorder[0]->orden - $typeapprover[0]->orden;

                  $approvers_up = Approver::where('user_id','<>',Auth::id())
                                          ->where('flow_id','=',$flow->id)
                                          ->where('order','>',$typeapprover[0]->orden)
                                          ->where('active','=',1)
                                          ->orderby('order','asc')->get();

                  $approvers_down = Approver::where('user_id','<>',Auth::id())
                                        ->where('flow_id','=',$flow->id)
                                        ->where('order','<=',$typeapprover[0]->orden)
                                        ->where('active','=',1)
                                        ->orderby('order','asc')->get();

                  if (Auth::id() != 129) {
                  $approvers = Approver::where('user_id','<>',Auth::id())
                                          ->where('flow_id','=',$flow->id)
                                          ->where('active','=',1)
                                          ->orderby('order','asc')->get();

                  // Aca se estaban llenando: $approvers_up y $approvers_down


                  }
                  else{
                  $validador_contable_aprueba = 2030;
                  $approvers = Approver::where('user_id','=', $validador_contable_aprueba )
                                        ->where('flow_id','=',$flow->id)
                                        ->where('active','=',1)
                                        ->orderby('order','asc')->get();          
                  }
      }

        $costCenters = CostCenter::where('active','=',1)
                       ->orderby('name','asc')->get();
               
        

        if ($flow->id != 60) {
          return view('invoice.show',[
            'modules' => $modules,
            'user' => $id_user,
            'invoice' => $invoice,
            'approvers' => $approvers,
            'costCenters' => $costCenters,
            'approver' => $approver,
            'typeapprover'=>$typeapprover,
            'diference'=>$diference,
            'approvers_up'=>$approvers_up,
            'approvers_down'=>$approvers_down,
            'flow_id'=>$flow->id,
            'totalusers'=>$totalusers,
            'totalubications'=>$totalubications,
            'invoiceCCS'=>$invoiceCC,
            'invoiceCCAutorizations'=>$invoiceCCAutorizations
          ]);
        }else{  
          $approvers=[]; 
          $typeapprover=DB::SELECT('SELECT 1 AS typeapprover,
                                           1 AS orden;');  

          $approver = Approver::where('flow_id','=',$flow->id)
          ->first();

          $flows = DB::SELECT('SELECT f.id AS id,
                                      f.name AS name
                               FROM invoice_flows f
                               INNER JOIN invoice_approvers a
                               ON a.flow_id = f.id 
                               WHERE f.active=? AND
                                     a.user_id = ? 
                               GROUP BY f.id',[1,$user->id]);

        return view('invoice.show',[
          'modules' => $modules,
          'user' => $id_user,
          'invoice' => $invoice,
          'approvers' => $approvers,
          'costCenters' => $costCenters,
          'approver' => $approver,
          'typeapprover'=>$typeapprover,
          'diference'=> 0,
          'flow_id'=>$flow->id,
          'flows'=>$flows,
          'totalusers'=>$totalusers,
          'totalubications'=>$totalubications,
          'invoiceCCS'=>$invoiceCC,
          'invoiceCCAutorizations'=>$invoiceCCAutorizations
        ]);
      }
    }

    public function log($id)
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,4);

        $invoice = Invoice::find($id);
        
        $costCenters = CostCenter::where('active','=',1)
                       ->orderby('name','asc')->get();

        return view('invoice.log',['modules' => $modules,'user' => $user,'invoice' => $invoice,'costCenters' => $costCenters,]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
        echo "Ingreso aqui";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    public function listPendingInvoices()
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,4);

        $invoice = new Invoice();
        $invoices = $invoice->getPendingInvoices();
        $countInvoices = count($invoices);

        return view('invoice.pending',['modules' => $modules,'user' => $user,'invoices' => $invoices,'countInvoices' => $countInvoices]);
    }

    public function Notify($id)
    {
        $invoice = Invoice::find($id);

        $log = Log::where('invoice_id','=',$id)
                    ->orderby('created_at','desc')
                    ->first();            
                    
        $user = $log->next_user;
           
        Mail::to($user->email)->send(new NofiticationInvoiceMail($user->name,$invoice,$user->id));

        return redirect()->route('invoice.pending');
    }


    public function resolutionscreate(){
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,4);

        $companies = Company::where('active','=',1)->get();
        $flows = Flow::where('active','=',1)->get();
        $suppliers = Supplier::where('active','=',1)
                    ->orderby('name','asc')
                    ->get();

        $error=0;
        $error_fecha=0;

        return view('invoice.resolutions',['modules' => $modules,'user' => $user,'companies' => $companies,'flows' => $flows,'suppliers' => $suppliers,'error'=>$error,'error_fecha'=>$error_fecha]);
    }

    public function resolutionsinit(){
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,4);

        $resolutions=DB::SELECT('SELECT * FROM resolutions');

        $cantidad_resolutions=count($resolutions);


        return view('invoice.resolutionsinit',['modules' => $modules,'user' => $user,'resolutions'=>$resolutions,'cantidad_resolutions'=>$cantidad_resolutions]);

    }


    public function resolutionsinactive(Request $request){
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,4);

        $id_resolution=$request->id_resolution;

        $inactivate=DB::UPDATE('UPDATE resolutions
                                SET active= 0
                                WHERE id=?',[$id_resolution]);

        $resolutions=DB::SELECT('SELECT * FROM resolutions');

        $cantidad_resolutions=count($resolutions);


        return view('invoice.resolutionsinit',['modules' => $modules,'user' => $user,'resolutions'=>$resolutions,'cantidad_resolutions'=>$cantidad_resolutions]);

    }


    public function resolutionsstore(Request $request){
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,4);
        $input = $request->all();
        $error_fecha=0;
        $cantidad= DB::SELECT("SELECT count(id) AS cantidad FROM resolutions
                               WHERE id_company=? AND active = ?",[$input['company_id'],1]);

        if ($request->begin_date > $request->end_date) {
          $error_fecha=1;
          $error = 0;
        $companies = Company::where('active','=',1)->get();
        $flows = Flow::where('active','=',1)->get();
        $suppliers = Supplier::where('active','=',1)
                    ->orderby('name','asc')
                    ->get();
        

        return view('invoice.resolutions',['modules' => $modules,'user' => $user,'companies' => $companies,'flows' => $flows,'suppliers' => $suppliers,'error'=>$error,'error_fecha'=>$error_fecha]);
        }elseif(($cantidad[0]->cantidad > 0)) {
        $companies = Company::where('active','=',1)->get();
        $flows = Flow::where('active','=',1)->get();
        $suppliers = Supplier::where('active','=',1)
                    ->orderby('name','asc')
                    ->get();
        $error_fecha=0;
        $error=1;

        return view('invoice.resolutions',['modules' => $modules,'user' => $user,'companies' => $companies,'flows' => $flows,'suppliers' => $suppliers,'error'=>$error,'error_fecha'=>$error_fecha]);
        }else{

        $insert= DB::INSERT('INSERT INTO resolutions (id_company, resolution_number, 
                                         begin_date,finish_date,int_number,end_number,active,prefijo)
                              VALUES (?,?,?,?,?,?,?,?)',[$input['company_id'],$input['number'],$request->begin_date,$request->end_date,$request->int_number,$request->end_number,1,$input['prefijo']]);
         return view('process',['modules' => $modules]);

        }
    }


    public function equivalente(Request $request){
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,5);
        $companies = Company::where('active','=',1)->get();
        $error=0;
        return view('invoice/equivalente',['modules' => $modules,'companies' => $companies,'error'=>$error]);
    }


    public function logequivalent(Request $request){
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,5);
        $companies = Company::where('active','=',1)->get();
        $input = $request->all();
        $cantidad=intval($request->countfields);
        $cantidadadjuntos=intval($request->countfieldsadd);
        $validacion_consecutivo='';
        $consecutivo=DB::SELECT('SELECT (l.id_consecutive+1) 
                                       AS consecutivo_actual
                                            FROM equivalent_log l
                                            WHERE l.id = (SELECT max(id) FROM equivalent_log WHERE company = ?)',[$input['compañia1']]);
        $cantidad_consecutivo=count($consecutivo);
        if ($cantidad_consecutivo == 0) {
          $validacion_consecutivo=0;
        }else{
          $validacion_consecutivo= intval($consecutivo[0]->consecutivo_actual);
        }
        $consecutivo_f=DB::SELECT('SELECT end_number AS consecutivo_final
                                  FROM resolutions
                                  WHERE id_company=?',[$input['compañia1']]);
        $calculo=intval($consecutivo_f[0]->consecutivo_final) - $validacion_consecutivo;
        $consecutivofinal=$validacion_consecutivo;
        $resolution_id= DB::SELECT('SELECT id AS id
                                    FROM resolutions
                                    WHERE active=1 AND
                                    id_company=?',[$input['compañia1']]);
        if (count($resolution_id) == 0) {
        $error=1;
        return view('invoice/equivalente',['modules' => $modules,'companies' => $companies,'error'=>$error]);
        }elseif($calculo < 0){
        $error=2;
        return view('invoice/equivalente',['modules' => $modules,'companies' => $companies,'error'=>$error]);          
        }else{
          $error=0;
          $maxid=DB::SELECT('SELECT max(id) AS maxid FROM equivalent_log WHERE company = ?',[$input['compañia1']]);

              if ($maxid[0]->maxid == NULL) {
                $consecutivofinal=1;
              }else{
                $consecutivo=DB::SELECT('SELECT (l.id_consecutive+1) 
                                           AS consecutivo_actual
                                            FROM equivalent_log l
                                            WHERE l.id = (SELECT max(id) FROM equivalent_log WHERE company = ?)',[$input['compañia1']]);
                $consecutivofinal=$consecutivo[0]->consecutivo_actual;
              }
        
                 $guardar=DB::INSERT('INSERT INTO equivalent_log (company,city,supplier,address,created_date,id_supplier,phone,id_consecutive,id_resolution) 
                  VALUES(?,?,?,?,?,?,?,?,?)',[$input['compañia1'],$input['ciudad'],$input['supplier_name'],$input['address'],$input['date_data'],$input['supplier_id'],$input['phone'],$consecutivofinal,$resolution_id[0]->id]);

                $maxidlog=DB::SELECT('SELECT max(id) AS id_equivalent FROM equivalent_log');


                 for ($i=1; $i <=$cantidad; $i++) {
                  $dato_valor=str_replace('.','',$input['valor'.$i]);
                  $dato_valor_final=str_replace(',','.',$dato_valor);
                 $guardar_flujo=DB::INSERT('INSERT INTO equivalent_flow (id_user,equivalent_state,next_user_id,equivalent_id,equivalent_description,equivalent_value,currency,created_at) 
                  VALUES(?,?,?,?,?,?,?,?)',[$user->id,1,$user->id,$maxidlog[0]->id_equivalent,$input['concept'.$i],$dato_valor_final,$input['currency'.$i],date('Y-m-d H:i:s')]);
                 }

                 $flow_users_insert=DB::INSERT("INSERT INTO  equivalent_users_flow (user_id,next_user_id,state,id_equivalent,equivalent_text,created_at) VALUES (?,?,?,?,?,?)",[$user->id,$user->id,1,$maxidlog[0]->id_equivalent,$request->descripton,date('Y-m-d')]);

                  $pendingDocuments=DB::SELECT('SELECT l.id AS id, 
                                                  CONCAT(r.prefijo,l.id_consecutive) AS 
                                                   numero_documento,
                                                   l.supplier AS proveedor,
                                                   l.id_supplier AS id_proveedor,
                                                   l.created_date AS fecha_documento,
                                                   SUM(f.equivalent_value) AS Total,
                                                   s.name AS estado,
                                            (SELECT MAX(id) FROM equivalent_users_flow ef WHERE ef.id_equivalent = l.id) LOG
                                            FROM equivalent_log l
                                            INNER JOIN equivalent_flow f 
                                            ON f.equivalent_id=l.id 
                                            INNER JOIN resolutions r
                                            ON r.id = l.id_resolution
                                            INNER JOIN equivalent_users_flow ef 
                                            ON ef.id_equivalent = l.id
                                            INNER JOIN equivalent_states s
                                            ON s.id=ef.state
                                            WHERE ef.next_user_id = ? AND
                                                  ef.id =(SELECT MAX(id) FROM equivalent_users_flow ef WHERE ef.id_equivalent = l.id) AND
                                                  ef.state <> 6
                                            GROUP BY ef.id_equivalent
                                            ORDER BY l.id DESC',[$user->id]);
                  $countDocuments=count($pendingDocuments);


                            $file = $request->file('file1');
                            if ($request->hasFile('file1')) 
                            {
                               for ($i=1; $i <=$cantidadadjuntos; $i++) {
                                $file = $request->file('file'.$i);
                                      $ext = $file->getClientOriginalExtension();
                                      $nombre = Str::random(6).".".$ext;
                                      \Storage::disk('equivalentes')->put($nombre,  \File::get($file));
                                  $guardado_datos=DB::INSERT("INSERT INTO attacheds(files,id_relation,name_module, created_at) VALUES (?,?,?,?)",[$nombre,$maxidlog[0]->id_equivalent,'equivalentes',date('Y-m-d')]);
                               }
                             }



                $companies = Company::where('active','=',1)->get();
                return view('invoice/equivalents',['modules' => $modules,'companies' => $companies,'countDocuments'=>$countDocuments,'pendingDocuments'=>$pendingDocuments]);


        }
    }

    public function equivalents(Request $request){
        $user = Auth::user();

        if ($user) {
        $application = new Application();
        $modules = $application->getModules($user->id,5);

          $pendingDocuments=DB::SELECT('SELECT l.id AS id,
                                           CONCAT(r.prefijo,l.id_consecutive) AS 
                                           numero_documento,
                                           l.supplier AS proveedor,
                                           l.id_supplier AS id_proveedor,
                                           l.created_date AS fecha_documento,
                                           SUM(f.equivalent_value) AS Total,
                                           s.name AS estado,
                                    (SELECT MAX(id) FROM equivalent_users_flow ef WHERE ef.id_equivalent = l.id) LOG
                                    FROM equivalent_log l
                                    INNER JOIN equivalent_flow f 
                                    ON f.equivalent_id=l.id 
                                    INNER JOIN resolutions r
                                    ON r.id = l.id_resolution
                                    INNER JOIN equivalent_users_flow ef 
                                    ON ef.id_equivalent = l.id
                                    INNER JOIN equivalent_states s
                                    ON s.id=ef.state
                                    WHERE ef.next_user_id = ? AND
                                          ef.id =(SELECT MAX(id) FROM equivalent_users_flow ef WHERE ef.id_equivalent = l.id) AND
                                          ef.state <> 6
                                    GROUP BY ef.id_equivalent
                                    ORDER BY l.id DESC',[$user->id]);

          $countDocuments=count($pendingDocuments);

        $companies = Company::where('active','=',1)->get();
        return view('invoice/equivalents',['modules' => $modules,'companies' => $companies,'countDocuments'=>$countDocuments,'pendingDocuments'=>$pendingDocuments]);
        }else{
        $user_id=$request->user_id;
        $application = new Application();
        $modules = $application->getModules($user_id,5);

          $pendingDocuments=DB::SELECT('SELECT l.id AS id,
                                           CONCAT(r.prefijo,l.id_consecutive) AS 
                                           numero_documento,
                                           l.supplier AS proveedor,
                                           l.id_supplier AS id_proveedor,
                                           l.created_date AS fecha_documento,
                                           SUM(f.equivalent_value) AS Total,
                                           s.name AS estado,
                                    (SELECT MAX(id) FROM equivalent_users_flow ef WHERE ef.id_equivalent = l.id) LOG
                                    FROM equivalent_log l
                                    INNER JOIN equivalent_flow f 
                                    ON f.equivalent_id=l.id 
                                    INNER JOIN resolutions r
                                    ON r.id = l.id_resolution
                                    INNER JOIN equivalent_users_flow ef 
                                    ON ef.id_equivalent = l.id
                                    INNER JOIN equivalent_states s
                                    ON s.id=ef.state
                                    WHERE ef.next_user_id = ? AND
                                          ef.id =(SELECT MAX(id) FROM equivalent_users_flow ef WHERE ef.id_equivalent = l.id) AND
                                          ef.state <> 6
                                    GROUP BY ef.id_equivalent
                                    ORDER BY l.id DESC',[$user_id]);

          $countDocuments=count($pendingDocuments);

        $companies = Company::where('active','=',1)->get();
        return view('invoice/equivalents',['modules' => $modules,'companies' => $companies,'countDocuments'=>$countDocuments,'pendingDocuments'=>$pendingDocuments]);




        }
    }



    public function imprimir(Request $request){
        $user = Auth::user();

        if ($user) {
        $application = new Application();
        $modules = $application->getModules($user->id,5);
        $input = $request->all();

        $company =DB::SELECT('SELECT a.company AS company,
                                     a.city AS city,
                                     a.supplier AS supplier,
                                     a.address AS address,
                                     a.created_date AS date,
                                     a.id_supplier AS id_supplier,
                                     a.phone AS phone,
                                     a.id_consecutive AS consecutivo,
                                     b.resolution_number AS resolution,
                                     b.int_number AS inicio,
                                     b.end_number AS final,
                                     b.finish_date AS finish_date,
                                     b.prefijo AS prefijo,
                                     TIMESTAMPDIFF(MONTH, b.begin_date, b.finish_date) AS meses
                              FROM 
                              equivalent_log a
                              INNER JOIN resolutions b
                              ON b.id=a.id_resolution
                              WHERE a.id=?',[$input['document_id']]);
        $prefix=$company[0]->prefijo;
        $consecutivo=$company[0]->consecutivo;

        $information = DB::SELECT('SELECT equivalent_description AS description,
                                          equivalent_value AS value,
                                          currency AS currency
                                  FROM equivalent_flow
                                  WHERE equivalent_id = ?',[$input['document_id']]);
        $Total = DB::SELECT('SELECT SUM(equivalent_value) AS Total
                             FROM equivalent_flow
                             WHERE equivalent_id=?
                             GROUP BY equivalent_id',[$input['document_id']]);
        $Total_final= $Total[0]->Total;
       // $information=DB::SELECT('SELECT ')
        $company_final=$company[0]->company;
        $nombre_usuario=$user->first_name.' '.$user->last_name;

        $data=compact('company_final','company','information','Total_final','prefix','consecutivo','nombre_usuario');
        $pdf = PDF::loadView('pdf.equivalentepdf', $data);
        return $pdf->stream();
        }else{
        $user=$request->user_id;
        $application = new Application();
        $modules = DB::select('SELECT module_id AS module_id,
                                       module_name AS module_name,
                                       function_id AS function_id,
                                       function_name AS function_name,
                                       route AS function_route
                                FROM permission
                                WHERE id_user=? AND aplication_id=? AND active = ?
                                ORDER BY module_id ASC',[$user,5,1]);
        $input = $request->all();

        $next_user_name= DB::SELECT('SELECT u.name AS name
                                     FROM users u
                                     INNER JOIN equivalent_flow f
                                     ON f.id_user = u.id
                                     WHERE f.id=(SELECT min(f.id) FROM equivalent_flow f WHERE f.equivalent_id = ?)',[$input['document_id']]);

        $company =DB::SELECT('SELECT a.company AS company,
                                     a.city AS city,
                                     a.supplier AS supplier,
                                     a.address AS address,
                                     a.created_date AS date,
                                     a.id_supplier AS id_supplier,
                                     a.phone AS phone,
                                     a.id_consecutive AS consecutivo,
                                     b.resolution_number AS resolution,
                                     b.int_number AS inicio,
                                     b.end_number AS final,
                                     b.finish_date AS finish_date,
                                     b.prefijo AS prefijo,
                                     TIMESTAMPDIFF(MONTH, b.begin_date, b.finish_date) AS meses
                              FROM 
                              equivalent_log a
                              INNER JOIN resolutions b
                              ON b.id=a.id_resolution
                              WHERE a.id=?',[$input['document_id']]);
        $prefix=$company[0]->prefijo;
        $consecutivo=$company[0]->consecutivo;

        $information = DB::SELECT('SELECT equivalent_description AS description,
                                          equivalent_value AS value,
                                          currency AS currency
                                  FROM equivalent_flow
                                  WHERE equivalent_id = ?',[$input['document_id']]);
        $Total = DB::SELECT('SELECT SUM(equivalent_value) AS Total
                             FROM equivalent_flow
                             WHERE equivalent_id=?
                             GROUP BY equivalent_id',[$input['document_id']]);
        $Total_final= $Total[0]->Total;
       // $information=DB::SELECT('SELECT ')
        $company_final=$company[0]->company;
        $nombre_usuario=$next_user_name[0]->name;
        $data=compact('company_final','company','information','Total_final','prefix','consecutivo','nombre_usuario');
        $pdf = PDF::loadView('pdf.equivalentepdf', $data);
        return $pdf->stream();


        }

    }


  public function adjuntosfilesequivalentes(Request $request){
        $adjuntosfiles= DB::SELECT("SELECT DATE_FORMAT(i.created_at, '%Y-%m-%d') AS date,
                           CASE
                           WHEN i.files IS NOT NULL THEN i.files
                           ELSE ''  
                           END AS file
                FROM attacheds i
                WHERE i.id_relation = ? AND
                      i.files IS NOT NULL",[$request->id]);

        echo json_encode($adjuntosfiles);



    }


  public function gestionequivalents(Request $request){
    $user = Auth::user();
    if ($user) {
    $application = new Application();
    $modules = $application->getModules($user->id,5);
    $id_documento= $request->id_documento;

    $datos= DB::SELECT('SELECT CONCAT(r.prefijo,l.id_consecutive) AS documento,
                               SUM(f.equivalent_value) AS total,
                               l.created_date AS date,
                               l.id AS id,
                               f.currency AS currency
                        FROM resolutions r
                        INNER JOIN equivalent_log l
                        ON l.id_resolution = r.id
                        INNER JOIN equivalent_flow f
                        ON f.equivalent_id = l.id
                        WHERE l.id= ?',[$id_documento]);

    $users_flow =DB::SELECT('SELECT u.name AS name,
                                    f.created_at AS date,
                                    f.equivalent_text AS description,
                                    CASE 
                                        WHEN f.state = 1 THEN "Radicada"
                                        WHEN f.state = 2 THEN "Cancelada"
                                        WHEN f.state = 3 THEN "Validada"
                                        WHEN f.state = 4 THEN "Aprobada"
                                        WHEN f.state = 5 THEN "Rechazada"
                                        WHEN f.state = 6 THEN "Finalizada"
                                    END AS estado,
                                      CASE 
                                        WHEN f.equivalent_text IS NOT NULL THEN f.equivalent_text
                                        ELSE ""
                                    END AS description
                                    FROM users u
                                    INNER JOIN equivalent_users_flow f
                                    ON u.id= f.user_id
                                    INNER JOIN equivalent_flow l
                                    ON l.equivalent_id= f.id_equivalent
                              WHERE l.id= (SELECT MAX(l.id) FROM equivalent_flow l WHERE l.equivalent_id = ?)',[$id_documento]);
    $approvers=DB::SELECT('SELECT u.name AS name,
                                  u.id AS user_id
                            FROM users u
                            INNER JOIN equivalent_approvers a
                            ON a.user_id = u.id
                            WHERE a.user_id <> ?',[$user->id]);
    $user_rol=DB::SELECT('SELECT rol_id AS rol
                          FROM equivalent_approvers
                          WHERE user_id = ?',[$user->id]);
    $cantidad_flow=count($users_flow);

    return view('invoice/gestionequivalents',['modules' => $modules,'datos'=>$datos,'flow'=>$users_flow,'cantidad_flow'=>$cantidad_flow,'approvers'=>$approvers,'user_rol'=>$user_rol[0]->rol]);

    }else{
    $user=$request->user_id;
    $application = new Application();
    $modules = DB::select('SELECT module_id AS module_id,
                                       module_name AS module_name,
                                       function_id AS function_id,
                                       function_name AS function_name,
                                       route AS function_route
                                FROM permission
                                WHERE id_user=? AND aplication_id=? AND active = ?
                                ORDER BY module_id ASC',[$user,5,1]);
    $id_documento= $request->id_documento;
    $datos= DB::SELECT('SELECT CONCAT(r.prefijo,l.id_consecutive) AS documento,
                               SUM(f.equivalent_value) AS total,
                               l.created_date AS date,
                               l.id AS id,
                               f.currency AS currency
                        FROM resolutions r
                        INNER JOIN equivalent_log l
                        ON l.id_resolution = r.id
                        INNER JOIN equivalent_flow f
                        ON f.equivalent_id = l.id
                        WHERE l.id= ?',[$id_documento]);

    $users_flow =DB::SELECT('SELECT u.name AS name,
                                    f.created_at AS date,
                                    f.equivalent_text AS description,
                                    CASE 
                                        WHEN f.state = 1 THEN "Radicada"
                                        WHEN f.state = 2 THEN "Cancelada"
                                        WHEN f.state = 3 THEN "Validada"
                                        WHEN f.state = 4 THEN "Aprobada"
                                        WHEN f.state = 5 THEN "Rechazada"
                                        WHEN f.state = 6 THEN "Finalizada"
                                    END AS estado,
                                      CASE 
                                        WHEN f.equivalent_text IS NOT NULL THEN f.equivalent_text
                                        ELSE ""
                                    END AS description
                                    FROM users u
                                    INNER JOIN equivalent_users_flow f
                                    ON u.id= f.user_id
                                    INNER JOIN equivalent_flow l
                                    ON l.equivalent_id= f.id_equivalent
                              WHERE l.id= (SELECT MAX(l.id) FROM equivalent_flow l WHERE l.equivalent_id = ?)',[$id_documento]);
    $approvers=DB::SELECT('SELECT u.name AS name,
                                  u.id AS user_id
                            FROM users u
                            INNER JOIN equivalent_approvers a
                            ON a.user_id = u.id
                            WHERE a.user_id <> ?',[$user]);
    $user_rol=DB::SELECT('SELECT rol_id AS rol
                          FROM equivalent_approvers
                          WHERE user_id = ?',[$user]);
    $cantidad_flow=count($users_flow);

    return view('invoice/gestionequivalents',['modules' => $modules,'datos'=>$datos,'flow'=>$users_flow,'cantidad_flow'=>$cantidad_flow,'approvers'=>$approvers,'user_rol'=>2]);



    }


  }



  public function loggestionequivalents(Request $request){
    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,5);
    $estado_final=0;
    if ($request->action == 'Rechazar') {
      $estado_final=5;
    }elseif($request->action == 'Validar'){
      $estado_final=3;
    }elseif ($request->action == 'Aprobar') {
      $estado_final=4;
    }else{
      $estado_final =6;
    }

    $insertdata= DB::INSERT('INSERT INTO equivalent_users_flow (user_id,next_user_id,state,id_equivalent,equivalent_text,created_at) VALUES (?,?,?,?,?,?)',[$user->id,$request->approver_id,$estado_final,$request->id_documento,$request->description,date('Y-m-d')]);


            $pendingDocuments=DB::SELECT('SELECT l.id AS id,
                                           CONCAT(r.prefijo,l.id_consecutive) AS 
                                           numero_documento,
                                           l.supplier AS proveedor,
                                           l.id_supplier AS id_proveedor,
                                           l.created_date AS fecha_documento,
                                           SUM(f.equivalent_value) AS Total,
                                           s.name AS estado,
                                    (SELECT MAX(id) FROM equivalent_users_flow ef WHERE ef.id_equivalent = l.id) LOG
                                    FROM equivalent_log l
                                    INNER JOIN equivalent_flow f 
                                    ON f.equivalent_id=l.id 
                                    INNER JOIN resolutions r
                                    ON r.id = l.id_resolution
                                    INNER JOIN equivalent_users_flow ef 
                                    ON ef.id_equivalent = l.id
                                    INNER JOIN equivalent_states s
                                    ON s.id=ef.state
                                    WHERE ef.next_user_id = ? AND
                                          ef.id =(SELECT MAX(id) FROM equivalent_users_flow ef WHERE ef.id_equivalent = l.id) AND
                                          ef.state <> 6
                                    GROUP BY ef.id_equivalent
                                    ORDER BY r.prefijo DESC',[$user->id]);
          $countDocuments=count($pendingDocuments);

          $name_user=DB::SELECT('SELECT first_name AS name,
                                        email AS email
                                 FROM users 
                                 WHERE id=?',[$request->approver_id]);

       if($estado_final != 3) {
        $user_creator= $user->name;
        $assignmentuser = $request->approver_id;
        $user_name = $name_user[0]->name;
        $Type = 'Factura equivalente';
        $MailSend= $name_user[0]->email;

        $request->session()->put('assignmentuser', $user_name);
        
        $data=[$assignmentuser,$Type,$user_creator,$user_name];
        
        if ($MailSend != NULL) {
          Mail::to($MailSend)->send(new SendMail($data));
        }


       }

        $companies = Company::where('active','=',1)->get();
        return view('invoice/equivalents',['modules' => $modules,'companies' => $companies,'countDocuments'=>$countDocuments,'pendingDocuments'=>$pendingDocuments]);

  }



  public function aprobacion_masiva(Request $request){

        $input = $request->all();
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,4);

        $invoices_ids=DB::SELECT('SELECT i.id AS id FROM invoices i
                                  INNER JOIN invoice_logg l
                                  ON i.id=(SELECT MAX(l.invoice_id) FROM invoice_logg l WHERE l.invoice_id = i.id)
                                  WHERE i.egress IS NOT NULL
                                  AND l.state_id= 4
                                  GROUP BY i.id');
        $invoices_id_final=json_decode( json_encode($invoices_ids), true);

        $cantidad_facturas=count($invoices_id_final);
        $i=0;
        for ($i=0; $i <$cantidad_facturas ; $i++) { 
         $insert_invoices=DB::INSERT('INSERT INTO invoice_logg (invoice_id, user_id, state_id, description,next_user_id)
           VALUES (?, ?, ?, ?,?)',[$invoices_id_final[$i]['id'],$user->id,6,'Aprobada',$user->id]);
        }
        $invoice = new Invoice();
        $invoices = $invoice->getActives(Auth::id());
        $countInvoices = count($invoices);


        return view('invoice.index',['modules' => $modules,'user' => $user,'invoices' => $invoices,'countInvoices' => $countInvoices]);
  }


  public function adjuntosfilesanticipos(Request $request){
    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);
    $adjuntosfiles= DB::SELECT("SELECT DATE_FORMAT(i.created_at, '%Y-%m-%d') AS date,
                        CASE
                        WHEN i.files IS NOT NULL THEN i.files
                        ELSE ''  
                        END AS file
            FROM attacheds i
            WHERE i.id_relation = ? AND
                    i.name_module=? AND
                  i.files IS NOT NULL",[$request->id,$function_name[0]->name]);

    echo json_encode($adjuntosfiles);
  }


  /*
  * Funcion que retorna el Log de Anticipo / Legalizacion
  * @param id: (int) numero de anticipo
  * @param type: (string) tipo que indica si es "Anticipo" o "Legalizacion"
  * @return json: (Object) Logs del registro
  */
  public function anticiposLog ( Request $request ){
    $id = $request->id;
    $type = $request->type;
    
    $anticiposLogs = DB::SELECT("SELECT 
        DATE_FORMAT(al.created_at, '%Y-%m-%d') AS date_,
        al.user_id,
        u1.name as init_user,
        al.next_user_id,
        u2.name as next_user
      FROM anticipos_log al
      JOIN users u1 ON u1.id = al.user_id
      JOIN users u2 ON u2.id = al.next_user_id
      WHERE al.id_document = ? AND
      al.type_document = ?   
      ORDER BY al.id DESC", [ $id, $type ]);

    echo json_encode([ 'data' => $anticiposLogs, 'success' => true ]);
  }



  public function adjuntosfileslegalizaciones(Request $request){
        $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[27]);
        $adjuntosfiles= DB::SELECT("SELECT DATE_FORMAT(i.created_at, '%Y-%m-%d') AS date,
                           CASE
                           WHEN i.files IS NOT NULL THEN i.files
                           ELSE ''  
                           END AS file
                FROM attacheds i
                WHERE i.id_relation = ? AND
                       i.name_module=? AND
                      i.files IS NOT NULL",[$request->id,$function_name[0]->name]);

        echo json_encode($adjuntosfiles);



    }


  public function anticipos(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);

    $suppliers = Supplier::select('suppliers.id', 'suppliers.nit', 'suppliers.name')
                          ->where('active', 1)
                          ->get();

   $directores=DB::SELECT("SELECT id AS id,
                                  name AS name,
                                  profile_name AS profile
                               FROM users
                               WHERE ((SUBSTRING(LTRIM(RTRIM(profile_name)),1,8)=? 
                               OR    SUBSTRING(LTRIM(RTRIM(profile_name)),1,9)=?) 
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?)
                               AND active = ?",['DIRECTOR','DIRECTORA',6,239,226,275,315,2169,69,1]);


    return view('anticipos/anticipos',['modules' => $modules,'user' => $user, 'suppliers'=>$suppliers,'directores'=>$directores]);
    

  }


  public function save(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);


   if($request->file('file1') == NULL){
             if ($request->supplier_id) {

                $save=DB::INSERT('INSERT INTO anticipos (id_user,empresa,fecha_pago,valor_anticipo,forma_pago,concepto,proveedor,estado) VALUES (?,?,?,?,?,?,?,?)',[$user->id,$request->empresa,$request->fecha_anticipo,$request->valor_anticipo,$request->forma_pago,$request->concepto_anticipo,$request->supplier_id,0]);
              }else{
                    $save=DB::INSERT('INSERT INTO anticipos (id_user,empresa,fecha_pago,valor_anticipo,forma_pago,concepto,proveedor,estado) VALUES (?,?,?,?,?,?,?,?)',[$user->id,$request->empresa,$request->fecha_anticipo,$request->valor_anticipo,$request->forma_pago,$request->concepto_anticipo,'',0]);
             }
             $ultimo_registro=DB::SELECT('SELECT max(id) AS id FROM anticipos');
             $leader_id=$request->id_director;
             $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id]);
             $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);
             $guardado_datos=DB::INSERT("INSERT INTO attacheds(files,id_user,next_user_id,id_relation,id_function,name_module, created_at) VALUES (?,?,?,?,?,?,?)",['',$user->id,$leader_id,$ultimo_registro[0]->id,24,$function_name[0]->name,date('Y-m-d')]);
             $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[$user->id,$leader_id,$ultimo_registro[0]->id,$function_name[0]->name,date('Y-m-d')]);

             $anticipos = DB::SELECT('SELECT
                                           a.id AS id, 
                                           a.fecha_pago AS fecha_pago,
                                           a.valor_anticipo AS valor_anticipo,
                                           a.forma_pago AS forma_pago,
                                           a.concepto AS concepto,
                                             us.name AS gestionando,
                                      CASE
                                      WHEN a.estado = 0 THEN "En proceso..."
                                      WHEN a.estado = 1 THEN "Aprobado"
                                      WHEN a.estado = 2 THEN "Pagado"
                                      WHEN a.estado = 3 THEN "Rechazado" 
                                      WHEN a.estado = 4 THEN "Proceso legalización"
                                      WHEN a.estado = 5 THEN "Legalización aprobada"
                                      WHEN a.estado = 6 THEN "Legalización cerrada"
                                      WHEN a.estado = 7 THEN "Legalización finalizada"
                                      WHEN a.estado = 8 THEN "Legalización rechazada"       
                                      END AS estado,
                                      ad.files AS files
                                      FROM anticipos a
                                      INNER JOIN attacheds ad
                                      ON ad.id_user = ?
                                      INNER JOIN users us
                                      ON ad.next_user_id=us.id 
                                      WHERE ad.name_module= ? AND
                                            ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id)
                                            GROUP BY a.id',[$user->id,$function_name[0]->name]);

                 $count= DB::SELECT('SELECT count(id) AS count FROM anticipos
                                  WHERE id_user=?',[$user->id]);

                 $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
                                                   a.fecha_pago AS fecha_pago,
                                                   a.valor_anticipo AS valor_anticipo,
                                                   a.forma_pago AS forma_pago,
                                                   a.concepto AS concepto,
                                                   u.name AS name
                                                   FROM anticipos a
                                                   INNER JOIN users u
                                                   ON u.id=a.id_user
                                                   WHERE a.id=?',[$ultimo_registro[0]->id]);


                  $assignmentuser = $leader_name[0]->name;
                  $Type = 'anticipo';
                  $MailSend= $leader_name[0]->email;

                  $request->session()->put('assignmentuser', $leader_name[0]->name);
                  
                  $data=[$assignmentuser,$Type,$ultimo_registro[0]->id,$leader_name[0]->name,$data_anticipo[0]->empresa,$data_anticipo[0]->fecha_pago,$data_anticipo[0]->valor_anticipo,$data_anticipo[0]->forma_pago,$data_anticipo[0]->concepto,$data_anticipo[0]->name,$leader_id,$leader_id];

                  if ($MailSend != NULL) {
                    Mail::to($MailSend)->send(new SendMail($data));
                  }

                  header("Location: https://flora.tierragro.com/anticipos/historial",true,303);  
                  exit();  
              //   https://flora.tierragro.com/anticipos/historial
              //   return view('anticipos/historialnew',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);
             //    die();
   }else{

                 $cantidadadjuntos=intval($request->countfieldsadd);
                 $leader_id=$request->id_director;
                 $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id]);
                if ($request->hasFile('file1'))
                {   
                     if ($request->supplier_id) {
                     $save=DB::INSERT('INSERT INTO anticipos (id_user,empresa,fecha_pago,valor_anticipo,forma_pago,concepto,proveedor,estado) VALUES (?,?,?,?,?,?,?,?)',[$user->id,$request->empresa,$request->fecha_anticipo,$request->valor_anticipo,$request->forma_pago,$request->concepto_anticipo,$request->supplier_id,0]);
                   }else{
                     $save=DB::INSERT('INSERT INTO anticipos (id_user,empresa,fecha_pago,valor_anticipo,forma_pago,concepto,proveedor,estado) VALUES (?,?,?,?,?,?,?,?)',[$user->id,$request->empresa,$request->fecha_anticipo,$request->valor_anticipo,$request->forma_pago,$request->concepto_anticipo,'',0]);
                   }
                     $ultimo_registro=DB::SELECT('SELECT max(id) AS id FROM anticipos');
                     $leader_id=$request->id_director;
                     $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id]);
                     $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);
                     for ($i=1; $i <=$cantidadadjuntos; $i++) {
                      $file = $request->file('file'.$i);
                            $ext = $file->getClientOriginalExtension();
                            $nombre = Str::random(6).".".$ext;
                            \Storage::disk('facturas')->put($nombre,  \File::get($file));
                        $guardado_datos=DB::INSERT("INSERT INTO attacheds(files,id_user,next_user_id,id_relation,id_function,name_module, created_at) VALUES (?,?,?,?,?,?,?)",[$nombre,$user->id,$leader_id,$ultimo_registro[0]->id,24,$function_name[0]->name,date('Y-m-d')]);
                        $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[$user->id,$leader_id,$ultimo_registro[0]->id,$function_name[0]->name,date('Y-m-d')]);
                     }
                     

                     $anticipos = DB::SELECT('SELECT
                                                   a.id AS id, 
                                                   a.fecha_pago AS fecha_pago,
                                                   a.valor_anticipo AS valor_anticipo,
                                                   a.forma_pago AS forma_pago,
                                                   a.concepto AS concepto,
                                                     us.name AS gestionando,
                                              CASE
                                              WHEN a.estado = 0 THEN "En proceso..."
                                              WHEN a.estado = 1 THEN "Aprobado"
                                              WHEN a.estado = 2 THEN "Pagado"
                                              WHEN a.estado = 3 THEN "Rechazado" 
                                              WHEN a.estado = 4 THEN "Proceso legalización"
                                              WHEN a.estado = 5 THEN "Legalización aprobada"
                                              WHEN a.estado = 6 THEN "Legalización cerrada"
                                              WHEN a.estado = 7 THEN "Legalización finalizada"
                                              WHEN a.estado = 8 THEN "Legalización rechazada"       
                                              END AS estado,
                                              ad.files AS files
                                              FROM anticipos a
                                              INNER JOIN attacheds ad
                                              ON ad.id_user = ?
                                              INNER JOIN users us
                                              ON ad.next_user_id=us.id 
                                              WHERE ad.name_module= ? AND
                                                    ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id)
                                                    GROUP BY a.id',[$user->id,$function_name[0]->name]);

                       $count= DB::SELECT('SELECT count(id) AS count FROM anticipos
                                        WHERE id_user=?',[$user->id]);

                       $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
                                                         a.fecha_pago AS fecha_pago,
                                                         a.valor_anticipo AS valor_anticipo,
                                                         a.forma_pago AS forma_pago,
                                                         a.concepto AS concepto,
                                                         u.name AS name
                                                         FROM anticipos a
                                                         INNER JOIN users u
                                                         ON u.id=a.id_user
                                                         WHERE a.id=?',[$ultimo_registro[0]->id]);


                        $assignmentuser = $leader_name[0]->name;
                        $Type = 'anticipo';
                        $MailSend= $leader_name[0]->email;

                        $request->session()->put('assignmentuser', $leader_name[0]->name);
                        
                        $data=[$assignmentuser,$Type,$ultimo_registro[0]->id,$leader_name[0]->name,$data_anticipo[0]->empresa,$data_anticipo[0]->fecha_pago,$data_anticipo[0]->valor_anticipo,$data_anticipo[0]->forma_pago,$data_anticipo[0]->concepto,$data_anticipo[0]->name,$leader_id,$leader_id];

                        if ($MailSend != NULL) {
                          Mail::to($MailSend)->send(new SendMail($data));
                        }


                      header("Location: https://flora.tierragro.com/anticipos/historial",true,303);  
                      exit();  
                      //  header("HTTP/1.1 303 See Other");
                      //  return view('anticipos/historialnew',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);
                      //  die();


               }else{

                        $suppliers = Supplier::where('active','=',1)
                              ->orderby('name','asc')
                              ->get(); 
           
   $directores=DB::SELECT("SELECT id AS id,
                                  name AS name,
                                  profile_name AS profile
                               FROM users
                               WHERE ((SUBSTRING(LTRIM(RTRIM(profile_name)),1,8)=? 
                               OR    SUBSTRING(LTRIM(RTRIM(profile_name)),1,9)=?) 
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?)
                               AND active = ?",['DIRECTOR','DIRECTORA',6,239,226,275,315,2169,1]);

                         return view('anticipos/anticiposerror',['modules' => $modules,'user' => $user, 'suppliers'=>$suppliers,'directores'=>$directores]);

               }

   }

/*

    if ($request->supplier_id) {

    $save=DB::INSERT('INSERT INTO anticipos (id_user,empresa,fecha_pago,valor_anticipo,forma_pago,concepto,proveedor,estado) VALUES (?,?,?,?,?,?,?,?)',[$user->id,$request->empresa,$request->fecha_anticipo,$request->valor_anticipo,$request->forma_pago,$request->concepto_anticipo,$request->supplier_id,0]);
  }else{
        $save=DB::INSERT('INSERT INTO anticipos (id_user,empresa,fecha_pago,valor_anticipo,forma_pago,concepto,proveedor,estado) VALUES (?,?,?,?,?,?,?,?)',[$user->id,$request->empresa,$request->fecha_anticipo,$request->valor_anticipo,$request->forma_pago,$request->concepto_anticipo,'',0]);
  }

    $ultimo_registro=DB::SELECT('SELECT max(id) AS id FROM anticipos');


      
      $cantidadadjuntos=intval($request->countfieldsadd);

      $leader_id=$request->id_director;
      $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id]);

      $file = $request->file('file1');
      
      $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);
      if ($request->hasFile('file1'))
      {
         for ($i=1; $i <=$cantidadadjuntos; $i++) {
          $file = $request->file('file'.$i);
                $ext = $file->getClientOriginalExtension();
                $nombre = Str::random(6).".".$ext;
                \Storage::disk('facturas')->put($nombre,  \File::get($file));
            $guardado_datos=DB::INSERT("INSERT INTO attacheds(files,id_user,next_user_id,id_relation,id_function,name_module, created_at) VALUES (?,?,?,?,?,?,?)",[$nombre,$user->id,$leader_id,$ultimo_registro[0]->id,24,$function_name[0]->name,date('Y-m-d')]);
         }


   $anticipos = DB::SELECT('SELECT
                                 a.id AS id, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                   us.name AS gestionando,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"       
                            END AS estado,
                            ad.files AS files
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_user = ?
                            INNER JOIN users us
                            ON ad.next_user_id=us.id 
                            WHERE ad.name_module= ? AND
                                  ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id)
                                  GROUP BY a.id',[$user->id,$function_name[0]->name]);

       $count= DB::SELECT('SELECT count(id) AS count FROM anticipos
                        WHERE id_user=?',[$user->id]);

       $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
                                         a.fecha_pago AS fecha_pago,
                                         a.valor_anticipo AS valor_anticipo,
                                         a.forma_pago AS forma_pago,
                                         a.concepto AS concepto,
                                         u.name AS name
                                         FROM anticipos a
                                         INNER JOIN users u
                                         ON u.id=a.id_user
                                         WHERE a.id=?',[$ultimo_registro[0]->id]);


        $assignmentuser = $leader_name[0]->name;
        $Type = 'anticipo';
        $MailSend= $leader_name[0]->email;

        $request->session()->put('assignmentuser', $leader_name[0]->name);
        
        $data=[$assignmentuser,$Type,$ultimo_registro[0]->id,$leader_name[0]->name,$data_anticipo[0]->empresa,$data_anticipo[0]->fecha_pago,$data_anticipo[0]->valor_anticipo,$data_anticipo[0]->forma_pago,$data_anticipo[0]->concepto,$data_anticipo[0]->name,$leader_id,$leader_id];

        if ($MailSend != NULL) {
          Mail::to($MailSend)->send(new SendMail($data));
        }


    return view('anticipos.historial',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);

       }else{
              $suppliers = Supplier::where('active','=',1)
                              ->orderby('name','asc')
                              ->get(); 
           
             $directores=DB::SELECT("SELECT id AS id,
                                            name AS name
                                         FROM users
                                         WHERE (SUBSTRING(LTRIM(RTRIM(profile_name)),1,8)=? 
                                         OR    SUBSTRING(LTRIM(RTRIM(profile_name)),1,9)=?)
                                         AND active = ?",['DIRECTOR','DIRECTORA',1]);

              return view('anticipos/anticiposerror',['modules' => $modules,'user' => $user, 'suppliers'=>$suppliers,'directores'=>$directores]);

           // $guardado_datos=DB::INSERT("INSERT INTO attacheds(files,id_user,id_relation,id_function,name_module,created_at) VALUES (?,?,?,?,?,?,?)",['N/A',$user->id,$leader_id,$ultimo_registro[0]->id,24,$function_name[0]->name,date('Y-m-d')]);
       }
*/

  } 






  public function historial(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);


    $ultimo_registro = DB::SELECT('SELECT max(id) AS id FROM anticipos');

    $cantidadadjuntos = intval($request->countfieldsadd);

    $leader_id = DB::SELECT('SELECT leader_id AS leader_id FROM users
        WHERE id=?',[$user->id]);

    
    //$estados_anticipos = config('app.global')['estados_anticipos'];

    $id_function_anticipos = 24;

    $id_function_legalizacion_acticipos = 27;

    $functNameAnticipos = DB::SELECT('SELECT name FROM functions WHERE id = ?', [
      $id_function_anticipos,
    ]);

    $functNameLegalizacionAnticipos = DB::SELECT('SELECT name FROM functions WHERE id = ?', [
      $id_function_legalizacion_acticipos,
    ]);

    $anticipos = DB::SELECT('SELECT
          a.id AS id, 
          a.fecha_pago AS fecha_pago,
          a.valor_anticipo AS valor_anticipo,
          a.forma_pago AS forma_pago,
          a.concepto AS concepto,
          us.name AS gestionando,
          CASE
            WHEN a.estado = 0 THEN "En proceso..."
            WHEN a.estado = 1 THEN "Aprobado"
            WHEN a.estado = 2 THEN "Pagado"
            WHEN a.estado = 3 THEN "Rechazado" 
            WHEN a.estado = 4 THEN "Proceso legalización"
            WHEN a.estado = 5 THEN "Legalización aprobada"
            WHEN a.estado = 6 THEN "Legalización cerrada"
            WHEN a.estado = 7 THEN "Legalización finalizada"
            WHEN a.estado = 8 THEN "Legalización rechazada"       
          END AS estado,
          ad.files AS files
        FROM anticipos a
        INNER JOIN attacheds ad
        ON ad.id_user = ?
        INNER JOIN users us
        ON ad.next_user_id = us.id
        WHERE 
          ( ad.name_module = ? OR ad.name_module = ? )
          AND
          a.estado <> 6 
          AND
          a.estado <> 7  
          AND
          ad.id =( SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id )
        GROUP BY 
          a.id,us.name,ad.files 
        ORDER BY a.id DESC', [
          $user->id,
          $functNameAnticipos[0]->name,
          $functNameLegalizacionAnticipos[0]->name,
      ]);

    $count= DB::SELECT('SELECT count(id) AS count FROM anticipos
                        WHERE id_user=?',[$user->id]);
    return view('anticipos/historial', [
      'modules' => $modules,
      'user' => $user,
      'anticipos'=>$anticipos,
      'count'=>$count
    ]);
    

  } 




    public function historialcorreo(Request $request){


    $id_user_correo=$request->id_user;

    //$user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($id_user_correo,4);


    $ultimo_registro=DB::SELECT('SELECT max(id) AS id FROM anticipos');


      
      $cantidadadjuntos=intval($request->countfieldsadd);

      $leader_id=DB::SELECT('SELECT leader_id AS leader_id FROM users
                             WHERE id=?',[$id_user_correo]);

   $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);
   $anticipos = DB::SELECT('SELECT
                                 a.id AS id, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"       
                            END AS estado,
                            ad.files AS files
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_user = ?
                            INNER JOIN users us
                            ON ad.next_user_id=us.id
                            WHERE ad.name_module= ?
                                  AND
                                  ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id)
                            GROUP BY a.id',[$id_user_correo,$function_name[0]->name]);

    $count= DB::SELECT('SELECT count(id) AS count FROM anticipos
                        WHERE id_user=?',[$id_user_correo]);
    return view('anticipos.historial',['modules' => $modules,'user' => $id_user_correo,'anticipos'=>$anticipos,'count'=>$count]);

  }


  public function historialnew(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);


    $ultimo_registro=DB::SELECT('SELECT max(id) AS id FROM anticipos');


      
      $cantidadadjuntos=intval($request->countfieldsadd);

      $leader_id=DB::SELECT('SELECT leader_id AS leader_id FROM users
                             WHERE id=?',[$user->id]);

   $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);
   $anticipos = DB::SELECT('SELECT
                                 a.id AS id, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"       
                            END AS estado,
                            ad.files AS files
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_user = ?
                            INNER JOIN users us
                            ON ad.next_user_id=us.id
                            WHERE ad.name_module= ?
                                  AND
                                  ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id)
                            GROUP BY a.id,us.name,ad.files ORDER BY a.id DESC',[$user->id,$function_name[0]->name]);

    $count= DB::SELECT('SELECT count(id) AS count FROM anticipos
                        WHERE id_user=?',[$user->id]);
    return view('anticipos/historial',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);
    

  }   


  public function gestion(Request $request){

        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,4);



       $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);
        $anticipos=DB::SELECT('SELECT
                                     a.id AS id,
                                     a.id_user AS id_user,
                                     usn.cedula AS cedula,
                                     usnu.name AS ultimo_aprobador,
                                     usnu.profile_name AS cargo_aprobador,
                                     a.empresa AS empresa, 
                                     a.fecha_pago AS fecha_pago,
                                     a.valor_anticipo AS valor_anticipo,
                                     a.forma_pago AS forma_pago,
                                     a.concepto AS concepto,
                                    us.name AS gestionando,
                                    usn.name AS name,
                                    p.name AS proveedor,
                                CASE
                                WHEN a.estado = 0 THEN "En proceso..."
                                WHEN a.estado = 1 THEN "Aprobado"
                                WHEN a.estado = 2 THEN "Pagado"
                                WHEN a.estado = 3 THEN "Rechazado" 
                                WHEN a.estado = 4 THEN "Proceso legalización"
                                WHEN a.estado = 5 THEN "Legalización aprobada"
                                WHEN a.estado = 6 THEN "Legalización cerrada"
                                WHEN a.estado = 7 THEN "Legalización finalizada"
                                WHEN a.estado = 8 THEN "Legalización rechazada"    
                                END AS estado,
                                ad.files AS adjunto
                                FROM anticipos a
                                INNER JOIN attacheds ad
                                ON ad.id_relation = a.id
                                INNER JOIN users us
                                ON ad.next_user_id=?
                                INNER JOIN users usn
                                ON a.id_user =usn.id
                                LEFT JOIN suppliers p
                                ON p.id=a.proveedor
                                LEFT JOIN anticipos_log l
                                ON l.id= (SELECT MAX(id) FROM anticipos_log l WHERE l.id_document = a.id)
                                LEFT JOIN users usnu
                                ON usnu.id = l.user_id
                                WHERE ad.name_module= ? AND
                                      ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id) 
                                GROUP BY a.id',[$user->id,$function_name[0]->name]);
        $count= count($anticipos);
        $id_usuario=$user->id;
        return view('anticipos/gestion',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count,'id_usuario'=>$id_usuario]);
    
   }
     





  public function gestioncorreo(Request $request){
    $user=$request->id_user;
    $application = new Application();
    $modules = $application->getModules($user,4);



   $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);
    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user,
                                 usn.cedula AS cedula,
                                 usnu.name AS ultimo_aprobador,
                                 usnu.profile_name AS cargo_aprobador,
                                 a.empresa AS empresa,  
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                p.name AS proveedor,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"    
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            LEFT JOIN suppliers p
                            ON p.id=a.proveedor
                            LEFT JOIN anticipos_log l
                            ON l.id= (SELECT MAX(id) FROM anticipos_log l WHERE l.id_document = a.id)
                            LEFT JOIN users usnu
                            ON usnu.id = l.user_id
                            WHERE ad.name_module= ? AND
                                  ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id)
                            GROUP BY a.id',[$user,$function_name[0]->name]);
    $count= count($anticipos);
    $id_usuario=$user;
    return view('anticipos/gestioncorreo',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count,'id_usuario'=>$id_usuario]);
    
  } 




  public function gestionaraceptar(Request $request){

    // Obtener el ID del User con la opción: Cierre de legalización
    $id_function_cierre_legalizacion = 30;
    $rows_permissions = DB::SELECT('SELECT id_user FROM permission  
      WHERE function_id = ' . $id_function_cierre_legalizacion );

    $validador_contable = $rows_permissions[ 0 ]->id_user;

    // Obtener el ID del User con la opción: Pagar anticipos
    $id_function_pagar_anticipos = 26;
    $rows_permissions2 = DB::SELECT('SELECT id_user FROM permission  
      WHERE function_id = ' . $id_function_pagar_anticipos .' AND active = ?',[1]);

    $validador_tesoreria = $rows_permissions2[ 0 ]->id_user;

    $user = Auth::user();

    if( $user ){
      $application = new Application();
      $modules = $application->getModules($user->id,4);


      $leader_id = DB::SELECT('SELECT leader_id AS leader_id FROM users WHERE id=?',[$user->id]);

      $function_name = DB::SELECT('SELECT name AS name FROM functions WHERE id = ?', [
        24
      ]);

      $ultimo_registro = DB::SELECT('SELECT max(id) AS id FROM attacheds
        WHERE id_relation=? AND
          name_module=?',[
            $request->id, 
            $function_name[0]->name
          ]);

      $anticipos1 = DB::SELECT('SELECT
          a.id AS id,
          a.id_user AS id_user, 
          a.fecha_pago AS fecha_pago,
          a.valor_anticipo AS valor_anticipo,
          a.forma_pago AS forma_pago,
          a.concepto AS concepto,
          us.name AS gestionando,
          usn.name AS name,
          CASE
          WHEN a.estado = 0 THEN "En proceso..."
          WHEN a.estado = 1 THEN "Aprobado"
          WHEN a.estado = 2 THEN "Pagado"
          WHEN a.estado = 3 THEN "Rechazado" 
          WHEN a.estado = 4 THEN "Proceso legalización"
          WHEN a.estado = 5 THEN "Legalización aprobada"
          WHEN a.estado = 6 THEN "Legalización cerrada"
          WHEN a.estado = 7 THEN "Legalización finalizada"
          WHEN a.estado = 8 THEN "Legalización rechazada"   
          END AS estado,
          ad.files AS adjunto
        FROM anticipos a
        INNER JOIN attacheds ad
        ON ad.id_relation = a.id
        INNER JOIN users us
        ON ad.next_user_id=?
        INNER JOIN users usn
        ON a.id_user =usn.id
        WHERE ad.name_module= ?
        GROUP BY a.id',[
          $user->id,
          $function_name[0]->name
        ]);

    
      $anticipo_valor = DB::SELECT('SELECT a.valor_anticipo
          FROM anticipos a
          WHERE a.id = ?
          GROUP BY a.id',[
            $request->id,
          ]);

      $valor_anticipo_real=str_replace('.', '', $anticipo_valor[0]->valor_anticipo);


      $cargo_usuario=DB::SELECT("SELECT profile_name AS profile
        FROM users
        WHERE id=?",[
          $user->id
        ]);


      $pos = strpos($cargo_usuario[0]->profile," ");
      $cargo_final =substr($cargo_usuario[0]->profile,0,$pos);


      if( (intval($valor_anticipo_real > 5000000)) && (($cargo_final == 'DIRECTOR') || ($cargo_final == 'DIRECTORA')) ){
        //echo '<h4>Entra en 1 </h4>';
        $update = DB::UPDATE('UPDATE attacheds
          SET next_user_id = ?
          WHERE id=?',[
            $leader_id[0]->leader_id,
            $ultimo_registro[0]->id
          ]);

        $guardado_datos_log = DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[
          $user->id,
          $leader_id[0]->leader_id,
          $request->id, //$ultimo_registro[0]->id,
          $function_name[0]->name,
          date('Y-m-d')
        ]);

        $leader_name= DB::SELECT('SELECT id AS id,first_name AS name, email AS email FROM users WHERE id=?',[
          $leader_id[0]->leader_id
        ]);

      }
      elseif( (intval($valor_anticipo_real > 5000000)) && (($cargo_final == 'GERENTE')) )
      {
        //echo '<h4>Entra en 2 </h4>';
        $update=DB::UPDATE('UPDATE attacheds
          SET next_user_id = ?
          WHERE id=?',[
            $validador_contable,
            $ultimo_registro[0]->id
          ]);

        $guardado_datos_log = DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[
          $user->id,
          $validador_contable,
          $request->id, //$ultimo_registro[0]->id,
          $function_name[0]->name,
          date('Y-m-d')
        ]);

        $leader_name = DB::SELECT('SELECT id AS id,first_name AS name, email AS email FROM users WHERE id=?',[
          $validador_contable,
        ]);

      }
      elseif( (intval($valor_anticipo_real < 5000000)) && (($cargo_final == 'GERENTE')) ){
        //echo '<h4>Entra en 3 </h4>';
        $update=DB::UPDATE('UPDATE attacheds
          SET next_user_id = ?
          WHERE id=?',[
            $validador_contable,
            $ultimo_registro[0]->id
        ]);

        $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[
          $user->id,
          $validador_contable,
          $request->id, //$ultimo_registro[0]->id,
          $function_name[0]->name,
          date('Y-m-d')
        ]);
        $leader_name= DB::SELECT('SELECT id AS id,first_name AS name, email AS email FROM users WHERE id=?',[
          $validador_contable,
        ]);

      }
      elseif( (intval($valor_anticipo_real <= 5000000)) && (($cargo_final == 'DIRECTOR') || ($cargo_final == 'DIRECTORA')) ){
        $update=DB::UPDATE('UPDATE attacheds
            SET next_user_id = ?
            WHERE id=?',[
              $validador_contable,
              $ultimo_registro[0]->id
        ]);
        $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[
          $user->id,
          $validador_contable,
          $request->id, //$ultimo_registro[0]->id,
          $function_name[0]->name,
          date('Y-m-d')
        ]);
        $leader_name= DB::SELECT('SELECT id as id,first_name AS name, email AS email FROM users WHERE id=?',[
          $validador_contable,
        ]);
      }
      //elseif( ($cargo_usuario[0]->profile == 'ANALISTA CONTABLE') && ($user->id == 1926) ){
      elseif( $user->id == $validador_contable ){
        $update=DB::UPDATE('UPDATE attacheds
          SET next_user_id = ?
          WHERE id=?',[
            $validador_tesoreria,
            $ultimo_registro[0]->id
        ]);
        $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[
          $user->id,
          $validador_tesoreria,
          $request->id, //$ultimo_registro[0]->id,
          $function_name[0]->name,
          date('Y-m-d')
        ]);
        $leader_name= DB::SELECT('SELECT id as id,first_name AS name, email AS email FROM users WHERE id=?',[
          $validador_tesoreria
        ]);
      }
      else{        
        $update=DB::UPDATE('UPDATE attacheds
          SET next_user_id = ?
          WHERE id=?',[
            $leader_id[0]->leader_id,
            $ultimo_registro[0]->id
        ]);
        $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[
          $user->id,
          $leader_id[0]->leader_id,
          $request->id, //$ultimo_registro[0]->id,
          $function_name[0]->name,
          date('Y-m-d')
        ]);
        $leader_name= DB::SELECT('SELECT id as id,first_name AS name, email AS email FROM users WHERE id=?',[
          $leader_id[0]->leader_id
        ]);
      }


      $update=DB::UPDATE('UPDATE anticipos
        SET estado = 1
        WHERE id=?',[
          $request->id
      ]);


      $anticipos=DB::SELECT('SELECT
        a.id AS id,
        a.id_user AS id_user,
        usn.cedula AS cedula,
        usnu.name AS ultimo_aprobador,
        usnu.profile_name AS cargo_aprobador,
        a.empresa AS empresa,  
        a.fecha_pago AS fecha_pago,
        a.valor_anticipo AS valor_anticipo,
        a.forma_pago AS forma_pago,
        a.concepto AS concepto,
        us.name AS gestionando,
        usn.name AS name,
        p.name AS proveedor,
        CASE
          WHEN a.estado = 0 THEN "En proceso..."
          WHEN a.estado = 1 THEN "Aprobado"
          WHEN a.estado = 2 THEN "Pagado"
          WHEN a.estado = 3 THEN "Rechazado" 
          WHEN a.estado = 4 THEN "Proceso legalización"
          WHEN a.estado = 5 THEN "Legalización aprobada"
          WHEN a.estado = 6 THEN "Legalización cerrada"
          WHEN a.estado = 7 THEN "Legalización finalizada"
          WHEN a.estado = 8 THEN "Legalización rechazada"      
        END AS estado,
        ad.files AS adjunto
        FROM anticipos a
        INNER JOIN attacheds ad
        ON ad.id_relation = a.id
        INNER JOIN users us
        ON ad.next_user_id=?
        INNER JOIN users usn
        ON a.id_user =usn.id
        LEFT JOIN suppliers p
        ON p.id=a.proveedor
        LEFT JOIN anticipos_log l
        ON l.id= (SELECT MAX(id) FROM anticipos_log l WHERE l.id_document = a.id)
        LEFT JOIN users usnu
        ON usnu.id = l.next_user_id
        WHERE ad.name_module= ? AND
        ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id) 
        GROUP BY a.id',[
          $user->id,
          $function_name[0]->name
        ]);


      $count= DB::SELECT('SELECT count(a.id) AS count 
        FROM anticipos a
        INNER JOIN users u
        ON u.id=a.id_user
        INNER JOIN attacheds atc
        ON atc.id_relation= a.id
        WHERE atc.next_user_id=? AND
        a.estado <> ?',[
          $user->id,
          6,//6
      ]);


      $data_anticipo=DB::SELECT('SELECT a.id AS id,
          a.empresa AS empresa,
          a.fecha_pago AS fecha_pago,
          a.valor_anticipo AS valor_anticipo,
          a.forma_pago AS forma_pago,
          a.concepto AS concepto,
          u.name AS name
        FROM anticipos a
        INNER JOIN users u
        ON u.id=a.id_user
        WHERE a.id=?',[
          $request->id
      ]);


      // $Case = Ticket::orderby('id','DESC')->limit(1)->get();
      // $TicketUser= User::where('id','=',$request->user_id);
      $Type='';
      $assignmentuser = $leader_name[0]->name;

      if( $user->id != $validador_contable ){
        $Type = 'anticipo';
      }
      else{
        $Type = 'pagoanticipocorreo';
      }

      /*
      echo '<pre>';
      print_r([
        'user_id' => $user->id,
        'leader_id' => $leader_id,
        'valor_anticipo_real' => $valor_anticipo_real,
        'cargo_usuario' => $cargo_usuario,
        'cargo_final' => $cargo_final,
      ]);
      echo '</pre>'; */
      //exit();


      //$CaseNumber =$Case[0]->id;
      $MailSend= $leader_name[0]->email;

      $request->session()->put('assignmentuser', $leader_name[0]->name);


      $data = [
        $assignmentuser,
        $Type,
        $request->id,
        $leader_name[0]->name,
        $data_anticipo[0]->empresa,
        $data_anticipo[0]->fecha_pago,
        $data_anticipo[0]->valor_anticipo,
        $data_anticipo[0]->forma_pago,
        $data_anticipo[0]->concepto,
        $data_anticipo[0]->name,
        $data_anticipo[0]->id,
        $leader_name[0]->id
      ];


      if ($MailSend != NULL) {
        Mail::to($MailSend)->send(new SendMail($data));
      }

      $id_usuario=$user->id;
    
      return view('anticipos/gestion',[
        'modules' => $modules,
        'user' => $user,
        'anticipos'=>$anticipos,
        'count'=>$count,
        'id_usuario'=>$id_usuario
      ]);

    }
    else{
    
      $user = $request->id_user_proceso;

      $application = new Application();
      $modules = $application->getModules($user,4);

      $leader_id = DB::SELECT('SELECT leader_id AS leader_id FROM users
          WHERE id=?',[
            $user
          ]);

      $function_name = DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[
        24
      ]);

      $ultimo_registro = DB::SELECT('SELECT max(id) AS id FROM attacheds
          WHERE id_relation=? AND
          name_module=?',[
            $request->id,
            $function_name[0]->name,
      ]);

    

      $anticipos1 = DB::SELECT('SELECT
          a.id AS id,
          a.id_user AS id_user, 
          a.fecha_pago AS fecha_pago,
          a.valor_anticipo AS valor_anticipo,
          a.forma_pago AS forma_pago,
          a.concepto AS concepto,
          us.name AS gestionando,
          usn.name AS name,
          CASE
            WHEN a.estado = 0 THEN "En proceso..."
            WHEN a.estado = 1 THEN "Aprobado"
            WHEN a.estado = 2 THEN "Pagado"
            WHEN a.estado = 3 THEN "Rechazado" 
            WHEN a.estado = 4 THEN "Proceso legalización"
            WHEN a.estado = 5 THEN "Legalización aprobada"
            WHEN a.estado = 6 THEN "Legalización cerrada"
            WHEN a.estado = 7 THEN "Legalización finalizada"
            WHEN a.estado = 8 THEN "Legalización rechazada"   
            END AS estado,
          ad.files AS adjunto
        FROM anticipos a
        INNER JOIN attacheds ad
        ON ad.id_relation = a.id
        INNER JOIN users us
        ON ad.next_user_id=?
        INNER JOIN users usn
        ON a.id_user =usn.id
        WHERE ad.name_module= ?
        GROUP BY a.id',[
          $user,
          $function_name[0]->name
        ]);

      $valor_anticipo_real=str_replace('.', '', $anticipos1[0]->valor_anticipo);


      $cargo_usuario=DB::SELECT("SELECT profile_name AS profile
          FROM users
          WHERE id=?",[
            $user
          ]);



      $pos = strpos($cargo_usuario[0]->profile," ");
      $cargo_final =substr($cargo_usuario[0]->profile,0,$pos);



      if( (intval($valor_anticipo_real > 5000000)) && (($cargo_final == 'DIRECTOR') || ($cargo_final == 'DIRECTORA')) ){
        $update=DB::UPDATE('UPDATE attacheds
          SET next_user_id = ?
          WHERE id=?',[
            $leader_id[0]->leader_id,
            $ultimo_registro[0]->id
          ]);
        $leader_name= DB::SELECT('SELECT id AS id,first_name AS name, email AS email FROM users WHERE id=?',[
          $leader_id[0]->leader_id
        ]);

      }
      elseif((intval($valor_anticipo_real > 5000000)) && (($cargo_final == 'GERENTE'))){
       $update=DB::UPDATE('UPDATE attacheds
          SET next_user_id = ?
          WHERE id=?',[
            $validador_contable,
            $ultimo_registro[0]->id
          ]);
       $leader_name= DB::SELECT('SELECT id AS id,first_name AS name, email AS email FROM users WHERE id=?',[
        $validador_contable
        ]);

      }
      elseif((intval($valor_anticipo_real <= 5000000)) && (($cargo_final == 'DIRECTOR') || ($cargo_final == 'DIRECTORA'))){
        $update=DB::UPDATE('UPDATE attacheds
            SET next_user_id = ?
            WHERE id=?',[
              $validador_contable,
              $ultimo_registro[0]->id
            ]);
        $leader_name= DB::SELECT('SELECT id as id,first_name AS name, email AS email FROM users WHERE id=?',[
          $validador_contable
        ]);

      }
      elseif((intval($valor_anticipo_real < 5000000)) && (($cargo_final == 'GERENTE'))){
       $update=DB::UPDATE('UPDATE attacheds
          SET next_user_id = ?
          WHERE id=?',[
            $validador_contable,
            $ultimo_registro[0]->id
          ]);       
      }
      // ????
      //elseif( ($cargo_final == 'ANALISTA CONTABLE') && ($user == 1926) ){
      elseif( $user == $validador_contable ){ 
          $update=DB::UPDATE('UPDATE attacheds
            SET next_user_id = ?
            WHERE id=?',[
              $validador_tesoreria,
              $ultimo_registro[0]->id
            ]);
          $leader_name= DB::SELECT('SELECT id as id,first_name AS name, email AS email FROM users WHERE id=?',[
            $validador_tesoreria
          ]);

      }
      else{
        $update=DB::UPDATE('UPDATE attacheds
          SET next_user_id = ?
          WHERE id=?',[
            $leader_id[0]->leader_id,
            $ultimo_registro[0]->id
          ]);
        $leader_name= DB::SELECT('SELECT id as id,first_name AS name, email AS email FROM users WHERE id=?',[
          $leader_id[0]->leader_id
        ]);

      }


    $update=DB::UPDATE('UPDATE anticipos
        SET estado = 1
        WHERE id=?',[
          $request->id
        ]);


    $anticipos=DB::SELECT('SELECT
          a.id AS id,
          a.id_user AS id_user,
          usn.cedula AS cedula,
          usnu.name AS ultimo_aprobador,
          usnu.profile_name AS cargo_aprobador,
          a.empresa AS empresa,  
          a.fecha_pago AS fecha_pago,
          a.valor_anticipo AS valor_anticipo,
          a.forma_pago AS forma_pago,
          a.concepto AS concepto,
          us.name AS gestionando,
          usn.name AS name,
          p.name AS proveedor,
          CASE
            WHEN a.estado = 0 THEN "En proceso..."
            WHEN a.estado = 1 THEN "Aprobado"
            WHEN a.estado = 2 THEN "Pagado"
            WHEN a.estado = 3 THEN "Rechazado" 
            WHEN a.estado = 4 THEN "Proceso legalización"
            WHEN a.estado = 5 THEN "Legalización aprobada"
            WHEN a.estado = 6 THEN "Legalización cerrada"
            WHEN a.estado = 7 THEN "Legalización finalizada"
            WHEN a.estado = 8 THEN "Legalización rechazada"      
          END AS estado,
          ad.files AS adjunto
        FROM anticipos a
        INNER JOIN attacheds ad
        ON ad.id_relation = a.id
        INNER JOIN users us
        ON ad.next_user_id=?
        INNER JOIN users usn
        ON a.id_user =usn.id
        LEFT JOIN suppliers p
        ON p.id=a.proveedor
        LEFT JOIN anticipos_log l
        ON l.id= (SELECT MAX(id) FROM anticipos_log l WHERE l.id_document = a.id)
        LEFT JOIN users usnu
        ON usnu.id = l.next_user_id
        WHERE ad.name_module= ? AND
        ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id) 
        GROUP BY a.id',[
          $user,
          $function_name[0]->name
        ]);


    $count= DB::SELECT('SELECT count(a.id) AS count 
      FROM anticipos a
      INNER JOIN users u
      ON u.id=a.id_user
      INNER JOIN attacheds atc
      ON atc.id_relation= a.id
      WHERE atc.next_user_id=? AND
      a.estado <> ?',[
        $user,
        6
      ]);


    $data_anticipo = DB::SELECT('SELECT a.id AS id,
        a.empresa AS empresa,
        a.fecha_pago AS fecha_pago,
        a.valor_anticipo AS valor_anticipo,
        a.forma_pago AS forma_pago,
        a.concepto AS concepto,
        u.name AS name
        FROM anticipos a
        INNER JOIN users u
        ON u.id=a.id_user
        WHERE a.id=?',[
          $request->id
        ]);


       // $Case = Ticket::orderby('id','DESC')->limit(1)->get();
       // $TicketUser= User::where('id','=',$request->user_id);
    $Type='';
    $assignmentuser = $leader_name[0]->name;

    if( $user != $validador_contable ){
      $Type = 'anticipo';
    }else{
      $Type = 'pagoanticipocorreo';
    }

    //$CaseNumber =$Case[0]->id;
    $MailSend= $leader_name[0]->email;

    $request->session()->put('assignmentuser', $leader_name[0]->name);

    
    $data = [
      $assignmentuser,
      $Type,
      $request->id,
      $leader_name[0]->name,
      $data_anticipo[0]->empresa,
      $data_anticipo[0]->fecha_pago,
      $data_anticipo[0]->valor_anticipo,
      $data_anticipo[0]->forma_pago,
      $data_anticipo[0]->concepto,
      $data_anticipo[0]->name,
      $data_anticipo[0]->id,
      $leader_name[0]->id
    ];


    if ($MailSend != NULL) {
      Mail::to($MailSend)->send(new SendMail($data));
    }

    $id_usuario = $user;
    return view('anticipos/gestion',[
      'modules' => $modules,
      'user' => $user,
      'anticipos'=>$anticipos,
      'count'=>$count,
      'id_usuario'=>$id_usuario]
    );
  }
}
  





  public function gestionarrechazar(Request $request){

    $user = Auth::user();
    if ($user) {
    $application = new Application();
    $modules = $application->getModules($user->id,4);

    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);
    $original_user=DB::SELECT('SELECT id_user AS original_user
                               FROM anticipos
                               WHERE id=?',[$request->invoice_id]);


    $update=DB::UPDATE('UPDATE anticipos
                                  SET estado = 3,
                                      motivo_rechazo = ?,
                                      id_user_rechazo = ?
                                  WHERE id=?',[$request->motivo_rechazo,$user->id,$request->invoice_id]);

    $update_attacheds=DB::UPDATE('UPDATE attacheds
                        SET next_user_id = ?
                        WHERE id_relation=? AND
                              name_module = ? AND
                              next_user_id = ?',[$original_user[0]->original_user,$request->invoice_id,$function_name[0]->name,$user->id]);
    $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[$user->id,$original_user[0]->original_user,$request->invoice_id,$function_name[0]->name,date('Y-m-d')]);



    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user,
                                 usn.cedula AS cedula,
                                 usnu.name AS ultimo_aprobador,
                                 usnu.profile_name AS cargo_aprobador,
                                 a.empresa AS empresa,  
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                p.name AS proveedor,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"      
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            LEFT JOIN suppliers p
                            ON p.id=a.proveedor
                            LEFT JOIN anticipos_log l
                           ON l.id= (SELECT MAX(id) FROM anticipos_log l WHERE l.id_document = a.id)
                            LEFT JOIN users usnu
                            ON usnu.id = l.next_user_id
                            WHERE ad.name_module= ? AND
                                  ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id) 
                            GROUP BY a.id',[$user->id,$function_name[0]->name]);
    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE u.leader_id=? AND
                              a.estado = ?',[$user->id,0]);


       $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
                                         a.fecha_pago AS fecha_pago,
                                         a.valor_anticipo AS valor_anticipo,
                                         a.forma_pago AS forma_pago,
                                         a.concepto AS concepto,
                                         u.first_name AS name,
                                         a.motivo_rechazo AS motivo_rechazo,
                                         u.email AS email,
                                         u.id AS id_user,
                                         ur.name AS usuario_rechazo
                                         FROM anticipos a
                                         INNER JOIN users u
                                         ON u.id=a.id_user
                                         INNER JOIN users ur
                                         ON ur.id = a.id_user_rechazo
                                         WHERE a.id=?',[$request->invoice_id]);



       // $Case = Ticket::orderby('id','DESC')->limit(1)->get();
       // $TicketUser= User::where('id','=',$request->user_id);

      //  $assignmentuser = $leader_name[0]->name;
        $Type = 'anticiporechazo';
        //$CaseNumber =$Case[0]->id;
        $MailSend= $data_anticipo[0]->email;

        $request->session()->put('assignmentuser', $data_anticipo[0]->name);

        
        $data=[$data_anticipo[0]->name,$Type,$request->invoice_id,$data_anticipo[0]->name,$data_anticipo[0]->empresa,$data_anticipo[0]->fecha_pago,$data_anticipo[0]->valor_anticipo,$data_anticipo[0]->forma_pago,$data_anticipo[0]->concepto,$data_anticipo[0]->name,$data_anticipo[0]->motivo_rechazo,$data_anticipo[0]->usuario_rechazo,$data_anticipo[0]->id_user];


        if ($MailSend != NULL) {
          Mail::to($MailSend)->send(new SendMail($data));
        }


    $id_usuario=$user->id;
    return view('anticipos/gestion',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count,'id_usuario'=>$id_usuario]);
    }else{
    $user=$request->id_usuario;
    $application = new Application();
    $modules = $application->getModules($user,4);

    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);
    $original_user=DB::SELECT('SELECT id_user AS original_user
                               FROM anticipos
                               WHERE id=?',[$request->invoice_id]);


    $update=DB::UPDATE('UPDATE anticipos
                                  SET estado = 3,
                                      motivo_rechazo = ?,
                                      id_user_rechazo = ?
                                  WHERE id=?',[$request->motivo_rechazo,$user,$request->invoice_id]);

    $update_attacheds=DB::UPDATE('UPDATE attacheds
                        SET next_user_id = ?
                        WHERE id_relation=? AND
                              name_module = ? AND
                              next_user_id = ?',[$original_user[0]->original_user,$request->invoice_id,$function_name[0]->name,$user]);
    $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[$user->id,$original_user[0]->original_user,$request->invoice_id,$function_name[0]->name,date('Y-m-d')]);



    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user,
                                 usn.cedula AS cedula,
                                 usnu.name AS ultimo_aprobador,
                                 usnu.profile_name AS cargo_aprobador,
                                 a.empresa AS empresa,  
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                p.name AS proveedor,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"      
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            LEFT JOIN suppliers p
                            ON p.id=a.proveedor
                            LEFT JOIN anticipos_log l
                           ON l.id= (SELECT MAX(id) FROM anticipos_log l WHERE l.id_document = a.id)
                            LEFT JOIN users usnu
                            ON usnu.id = l.next_user_id
                            WHERE ad.name_module= ? AND
                                  ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id) 
                            GROUP BY a.id',[$user,$function_name[0]->name]);
    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE u.leader_id=? AND
                              a.estado = ?',[$user,0]);


       $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
                                         a.fecha_pago AS fecha_pago,
                                         a.valor_anticipo AS valor_anticipo,
                                         a.forma_pago AS forma_pago,
                                         a.concepto AS concepto,
                                         u.first_name AS name,
                                         a.motivo_rechazo AS motivo_rechazo,
                                         u.email AS email,
                                         u.id AS id_user,
                                         ur.name AS usuario_rechazo
                                         FROM anticipos a
                                         INNER JOIN users u
                                         ON u.id=a.id_user
                                         INNER JOIN users ur
                                         ON ur.id = a.id_user_rechazo
                                         WHERE a.id=?',[$request->invoice_id]);



       // $Case = Ticket::orderby('id','DESC')->limit(1)->get();
       // $TicketUser= User::where('id','=',$request->user_id);

      //  $assignmentuser = $leader_name[0]->name;
        $Type = 'anticiporechazo';
        //$CaseNumber =$Case[0]->id;
        $MailSend= $data_anticipo[0]->email;

        $request->session()->put('assignmentuser', $data_anticipo[0]->name);

        
        $data=[$data_anticipo[0]->name,$Type,$request->invoice_id,$data_anticipo[0]->name,$data_anticipo[0]->empresa,$data_anticipo[0]->fecha_pago,$data_anticipo[0]->valor_anticipo,$data_anticipo[0]->forma_pago,$data_anticipo[0]->concepto,$data_anticipo[0]->name,$data_anticipo[0]->motivo_rechazo,$data_anticipo[0]->usuario_rechazo,$data_anticipo[0]->id_user];

        if ($MailSend != NULL) {
          Mail::to($MailSend)->send(new SendMail($data));
        }


    $id_usuario=$request->id_usuario;
    return view('anticipos/gestion',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count,'id_usuario'=>$id_usuario]);
  }
    

  }      





  public function gestionarrechazarlegalizacion(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);
    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[27]);


    $original_user=DB::SELECT('SELECT id_user AS original_user
        FROM anticipos
        WHERE id=?',[$request->invoice_id]);


    $update=DB::UPDATE('UPDATE anticipos
        SET estado = 8,
            motivo_rechazo_legalización = ?,
            id_user_rechazo_legalizacion = ?
        WHERE id=?', [
          $request->motivo_rechazo,$user->id,$request->invoice_id
        ]);

    $update_attacheds=DB::UPDATE('UPDATE attacheds
        SET next_user_id = ?
        WHERE id_relation=? AND
              name_module = ? AND
              next_user_id = ?',[$original_user[0]->original_user,$request->invoice_id,$function_name[0]->name,$user->id]);

    $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[$user->id,$original_user[0]->original_user,$request->invoice_id,$function_name[0]->name,date('Y-m-d')]);



    $anticipos=DB::SELECT('SELECT
        a.id AS id,
        a.id_user AS id_user,
        usn.cedula AS cedula,
        usnu.name AS ultimo_aprobador,
        usnu.profile_name AS cargo_aprobador,
        a.empresa AS empresa,  
        a.fecha_pago AS fecha_pago,
        a.valor_anticipo AS valor_anticipo,
        a.forma_pago AS forma_pago,
        a.concepto AS concepto,
        us.name AS gestionando,
        usn.name AS name,
        p.name AS proveedor,
        CASE
          WHEN a.estado = 0 THEN "En proceso..."
          WHEN a.estado = 1 THEN "Aprobado"
          WHEN a.estado = 2 THEN "Pagado"
          WHEN a.estado = 3 THEN "Rechazado" 
          WHEN a.estado = 4 THEN "Proceso legalización"
          WHEN a.estado = 5 THEN "Legalización aprobada"
          WHEN a.estado = 6 THEN "Legalización cerrada"
          WHEN a.estado = 7 THEN "Legalización finalizada"
          WHEN a.estado = 8 THEN "Legalización rechazada"      
        END AS estado,
        ad.files AS adjunto
      FROM anticipos a
      INNER JOIN attacheds ad
      ON ad.id_relation = a.id
      INNER JOIN users us
      ON ad.next_user_id=?
      INNER JOIN users usn
      ON a.id_user =usn.id
      LEFT JOIN suppliers p
      ON p.id=a.proveedor
      LEFT JOIN anticipos_log l
      ON l.id= (SELECT MAX(id) FROM anticipos_log l WHERE l.id_document = a.id)
      LEFT JOIN users usnu
      ON usnu.id = l.next_user_id
      WHERE ad.name_module= ? AND
            ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id)
      GROUP BY a.id',[$user->id,$function_name[0]->name]);

    $count= DB::SELECT('SELECT count(a.id) AS count 
        FROM anticipos a
                INNER JOIN users u
                ON u.id=a.id_user
        WHERE u.leader_id=? AND
              a.estado = ?',[$user->id,0]);


    $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
        a.fecha_pago AS fecha_pago,
        a.valor_anticipo AS valor_anticipo,
        a.forma_pago AS forma_pago,
        a.concepto AS concepto,
        u.first_name AS name,
        a.motivo_rechazo_legalización AS motivo_rechazo,
        u.email AS email,
        ur.name AS usuario_rechazo
        FROM anticipos a
        INNER JOIN users u
        ON u.id=a.id_user
        INNER JOIN users ur
        ON ur.id = a.id_user_rechazo_legalizacion
        WHERE a.id=?',[$request->invoice_id]);



      $Type = 'legalizacionrechazo';
      $MailSend= $data_anticipo[0]->email;

      $request->session()->put('assignmentuser', $data_anticipo[0]->name);

        
      $data=[$data_anticipo[0]->name,$Type,$request->invoice_id,$data_anticipo[0]->name,$data_anticipo[0]->empresa,$data_anticipo[0]->fecha_pago,$data_anticipo[0]->valor_anticipo,$data_anticipo[0]->forma_pago,$data_anticipo[0]->concepto,$data_anticipo[0]->name,$data_anticipo[0]->motivo_rechazo,$data_anticipo[0]->usuario_rechazo];


      if ($MailSend != NULL) {
        Mail::to($MailSend)->send(new SendMail($data));
      }

      $id_usuario = $user->id;

      return view('anticipos/gestion', [
        'modules' => $modules,
        'user' => $user,
        'anticipos'=>$anticipos,
        'count'=>$count,
        'id_usuario'=>$id_usuario,
      ]);
  } 


  public function pagaranticipos(Request $request){

    $user = Auth::user();
    if ($user) {
    $application = new Application();
    $modules = $application->getModules($user->id,4);

    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);


    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                p.name AS proveedor,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"       
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            LEFT JOIN suppliers p
                            ON p.id=a.proveedor
                            WHERE ad.name_module= ? AND
                                  a.estado = ?
                            GROUP BY a.id',[$user->id,$function_name[0]->name,1]);




   $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[1]);
    return view('anticipos/pagar',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);
    }else{
    $user=2101;
    $application = new Application();
    $modules = $application->getModules($user,4);

    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);


    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                p.name AS proveedor,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"       
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            LEFT JOIN suppliers p
                            ON p.id=a.proveedor
                            WHERE ad.name_module= ? AND
                                  a.estado = ?
                            GROUP BY a.id',[$user,$function_name[0]->name,1]);

   $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[1]);
    return view('anticipos/pagar',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);
  }

  }



  public function pagar(Request $request){
    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);

    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);


    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                p.name AS proveedor,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"       
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            LEFT JOIN suppliers p
                            ON p.id=a.proveedor
                            WHERE ad.name_module= ? AND
                                  a.estado = ?
                            GROUP BY a.id',[$user->id,$function_name[0]->name,1]);

   $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[1]);
    return view('anticipos/pagar',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);


  }



  public function pagado(Request $request){

    $user = Auth::user();

    if ($user) {
    $application = new Application();
    $modules = $application->getModules($user->id,4);

    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);



    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                usn.email AS email,
                                p.name AS proveedor,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"       
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            LEFT JOIN suppliers p
                            ON p.id=a.proveedor
                            WHERE ad.name_module= ? AND
                                  a.estado = ? AND
                                  a.id= ?
                            GROUP BY a.id',[$user->id,$function_name[0]->name,1,$request->id]);


    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[1]);
    $array = json_decode(json_encode($anticipos), true);


       $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
                                         a.fecha_pago AS fecha_pago,
                                         a.valor_anticipo AS valor_anticipo,
                                         a.forma_pago AS forma_pago,
                                         a.concepto AS concepto,
                                         u.name AS name
                                         FROM anticipos a
                                         INNER JOIN users u
                                         ON u.id=a.id_user
                                         WHERE a.id=?',[$request->id]);

        //$assignmentuser = $leader_name[0]->name;
        $Type = 'anticipopago';


        //var_dump($array[0]['email']);
        $MailSend = $array[0]['email'];
        
        //$CaseNumber =$Case[0]->id;
        //$MailSend= $array[0]['email'];
       // var_dump($array[0]['name']);

        $request->session()->put('assignmentuser', $array[0]['name']);
        
        $data=[$array[0]['name'],$Type,$request->id,$array[0]['name'],$data_anticipo[0]->empresa,$data_anticipo[0]->fecha_pago,$data_anticipo[0]->valor_anticipo,$data_anticipo[0]->forma_pago,$data_anticipo[0]->concepto,$data_anticipo[0]->name,$array[0]['id_user']];



        if ($MailSend != NULL) {
         Mail::to($MailSend)->send(new SendMail($data));
        }

        $update=DB::UPDATE('UPDATE anticipos
                    SET estado = 2
                    WHERE id=?',[$request->id]);
        $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[$user->id,$array[0]['id_user'],$request->id,$function_name[0]->name,date('Y-m-d')]);



    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                usn.email AS email,
                                p.name AS proveedor,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"        
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            LEFT JOIN suppliers p
                            ON p.id=a.proveedor
                            WHERE ad.name_module= ? AND
                                  a.estado = ?
                            GROUP BY a.id',[$user->id,$function_name[0]->name,1]);


       return view('anticipos/pagar',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);     
    }else{
    $user = 2101;
    $application = new Application();
    $modules = $application->getModules($user,4);

    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);



    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                usn.email AS email,
                                p.name AS proveedor,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"       
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            LEFT JOIN suppliers p
                            ON p.id=a.proveedor
                            WHERE ad.name_module= ? AND
                                  a.estado = ?
                            GROUP BY a.id',[$user,$function_name[0]->name,1]);
    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[1]);
    $array = json_decode(json_encode($anticipos), true);


       $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
                                         a.fecha_pago AS fecha_pago,
                                         a.valor_anticipo AS valor_anticipo,
                                         a.forma_pago AS forma_pago,
                                         a.concepto AS concepto,
                                         u.name AS name
                                         FROM anticipos a
                                         INNER JOIN users u
                                         ON u.id=a.id_user
                                         WHERE a.id=?',[$request->id]);

        //$assignmentuser = $leader_name[0]->name;
        $Type = 'anticipopago';


        //var_dump($array[0]['email']);
        $MailSend = $array[0]['email'];
        
        //$CaseNumber =$Case[0]->id;
        //$MailSend= $array[0]['email'];
       // var_dump($array[0]['name']);

        $request->session()->put('assignmentuser', $array[0]['name']);
        
        $data=[$array[0]['name'],$Type,$request->id,$array[0]['name'],$data_anticipo[0]->empresa,$data_anticipo[0]->fecha_pago,$data_anticipo[0]->valor_anticipo,$data_anticipo[0]->forma_pago,$data_anticipo[0]->concepto,$data_anticipo[0]->name,$array[0]['id_user']];



        if ($MailSend != NULL) {
          Mail::to($MailSend)->send(new SendMail($data));
        }

        $update=DB::UPDATE('UPDATE anticipos
                    SET estado = 2
                    WHERE id=?',[$request->id]);



    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                usn.email AS email,
                                p.name AS proveedor,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"        
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            LEFT JOIN suppliers p
                            ON p.id=a.proveedor
                            WHERE ad.name_module= ? AND
                                  a.estado = ?
                            GROUP BY a.id',[$user,$function_name[0]->name,1]);


       return view('anticipos/pagar',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);
  }
    

  }




    public function pagadocorreo(Request $request){

    $user = $request->id_user_proceso;
    $id_anticipo=$request->id;
    $application = new Application();
    $modules = $application->getModules($user,4);

    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);



    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                usn.email AS email,
                                p.name AS proveedor,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"       
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            LEFT JOIN suppliers p
                            ON p.id=a.proveedor
                            WHERE ad.name_module= ? AND
                                  a.estado = ? AND
                                  a. id = ?
                            GROUP BY a.id',[$user,$function_name[0]->name,1,$request->id]);
    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[1]);
    $array = json_decode(json_encode($anticipos), true);


       $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
                                         a.fecha_pago AS fecha_pago,
                                         a.valor_anticipo AS valor_anticipo,
                                         a.forma_pago AS forma_pago,
                                         a.concepto AS concepto,
                                         u.name AS name
                                         FROM anticipos a
                                         INNER JOIN users u
                                         ON u.id=a.id_user
                                         WHERE a.id=?',[$request->id]);

        //$assignmentuser = $leader_name[0]->name;
        $Type = 'anticipopago';


        //var_dump($array[0]['email']);
        $MailSend = $array[0]['email'];
        
        //$CaseNumber =$Case[0]->id;
        //$MailSend= $array[0]['email'];
       // var_dump($array[0]['name']);

        $request->session()->put('assignmentuser', $array[0]['name']);
        
        $data=[$array[0]['name'],$Type,$request->id,$array[0]['name'],$data_anticipo[0]->empresa,$data_anticipo[0]->fecha_pago,$data_anticipo[0]->valor_anticipo,$data_anticipo[0]->forma_pago,$data_anticipo[0]->concepto,$data_anticipo[0]->name];



        if ($MailSend != NULL) {
          Mail::to($MailSend)->send(new SendMail($data));
        }

        $update=DB::UPDATE('UPDATE anticipos
                    SET estado = 2
                    WHERE id=?',[$request->id]);



    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                usn.email AS email,
                                p.name AS proveedor,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"        
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            LEFT JOIN suppliers p
                            ON p.id=a.proveedor
                            WHERE ad.name_module= ? AND
                                  a.estado = ?
                            GROUP BY a.id',[0,$function_name[0]->name,1]);


       return view('anticipos/pagar',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);
    

  }


  public function legalizacion(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);

    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);


    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"        
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.id_user=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            WHERE ad.name_module= ? AND 
                                  (a.estado = ? OR a.estado=?)
                            GROUP BY a.id',[$user->id,$function_name[0]->name,2,8]);
    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[2]);
    return view('anticipos/legalizacion',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);
    

  }



  public function legalizar_final(Request $request){


    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);
    $costCenters = CostCenter::where('active','=',1)
                       ->orderby('name','asc')->get();
    $id_documento=$request->id;
    $cuentas = DB::SELECT('SELECT id AS id,
                           Cuenta AS cuenta
                           FROM cuentas_cecos');

    $empresa = DB::SELECT('SELECT empresa AS empresa
                           FROM anticipos
                           WHERE id=?',[$request->id]);


    switch ($empresa[0]->empresa) {
      case 'PEREZ Y CARDONA S.A.S':
       $costCenters = CostCenter::where('active','=',1)
                       ->orderby('name','asc')->get();

       $costCenters = DB::SELECT('SELECT
                                  id AS id, 
                                  code AS code,
                                  name AS name
                                  FROM cost_centers
                                  WHERE SUBSTRING(LTRIM(RTRIM(code)),1,1) = 1');
        break;

      case 'M.P GALAGRO S.A.S':
       $costCenters = DB::SELECT('SELECT
                                  id AS id, 
                                  code AS code,
                                  name AS name
                                  FROM cost_centers
                                  WHERE SUBSTRING(LTRIM(RTRIM(code)),1,1) = 2');
        break;
      default:
       $costCenters = DB::SELECT('SELECT
                                  id AS id, 
                                  code AS code,
                                  name AS name
                                  FROM cost_centers
                                  WHERE SUBSTRING(LTRIM(RTRIM(code)),1,1) = 3');
        break;
    }


   $directores=DB::SELECT("SELECT id AS id,
                                  name AS name,
                                  profile_name AS profile
                               FROM users
                               WHERE ((SUBSTRING(LTRIM(RTRIM(profile_name)),1,8)=? 
                               OR    SUBSTRING(LTRIM(RTRIM(profile_name)),1,9)=?) 
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?)
                               AND active = ?",['DIRECTOR','DIRECTORA',6,239,226,275,315,2169,1]);




    return view('anticipos/legalizar_final',['modules' => $modules,'user' => $user,'id_documento'=>$id_documento,'costCenters' => $costCenters,'cuentas'=>$cuentas,'directores'=>$directores]);
    

  }



  public function legalizacionsave(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);

    $input = $request->all();

      
      $cantidadadjuntos=intval($request->countfieldsadd);
      $cantidadcampos=intval($request->countfields);
      $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[27]);

      $leader_id=DB::SELECT('SELECT leader_id AS leader_id FROM users
                             WHERE id=?',[$user->id]);

      $file = $request->file('file1');

      if ($file != NULL) 
      {
         for ($i=1; $i <=$cantidadadjuntos; $i++) {
          $file = $request->file('file'.$i);
                $ext = $file->getClientOriginalExtension();
                $nombre = Str::random(6).".".$ext;
                \Storage::disk('facturas')->put($nombre,  \File::get($file));
            $guardado_datos=DB::INSERT("INSERT INTO attacheds(files,id_user,next_user_id,id_relation,id_function,name_module,created_at) VALUES (?,?,?,?,?,?,?)",[$nombre,$user->id,$request->id_director,$request->invoice_id,27,$function_name[0]->name,date('Y-m-d')]);
         }
          $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[$user->id,$request->id_director,$request->invoice_id,$function_name[0]->name,date('Y-m-d')]);
       }else{
            $guardado_datos=DB::INSERT("INSERT INTO attacheds(files,id_user,id_relation,id_function,name_module, created_at) VALUES (?,?,?,?,?,?,?)",['N/A',$user->id,$request->id_director,$request->invoice_id,27,$function_name[0]->name,date('Y-m-d')]);
          $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[$user->id,$request->id_director,$request->invoice_id,$function_name[0]->name,date('Y-m-d')]);
       }


        for ($j=1; $j <=$cantidadcampos; $j++) {
            $guardado_datos=DB::INSERT("INSERT INTO distributions_legalizacion(anticipo_id,cost_center_id,cuenta,value, created_at,updated_at,concept) VALUES (?,?,?,?,?,?,?)",[$request->invoice_id,$input['coce'.$j],$input['cuenta'.$j],$input['value'.$j],date('Y-m-d'),date('Y-m-d'),$input['concepto_anticipo']]);
         }

      $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$request->id_director]);


    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                                usn.email AS email,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"         
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.id_user=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            WHERE ad.name_module= ? AND 
                                  (a.estado = ? OR a.estado=?)
                            GROUP BY a.id',[$user->id,$function_name[0]->name,2,8]);
    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[2]);
    $array = json_decode(json_encode($anticipos), true);

    $update=DB::UPDATE('UPDATE anticipos
                        SET estado = 4
                        WHERE id=?',[$request->invoice_id]);






   $function_name2=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[24]);
    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"        
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.id_user=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            WHERE ad.name_module= ? AND 
                                  (a.estado = ? OR a.estado=?)
                            GROUP BY a.id',[$user->id,$function_name2[0]->name,2,8]);

   $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
                                   a.fecha_pago AS fecha_pago,
                                   a.valor_anticipo AS valor_anticipo,
                                   a.forma_pago AS forma_pago,
                                   a.concepto AS concepto,
                                   u.name AS name
                                   FROM anticipos a
                                   INNER JOIN users u
                                   ON u.id=a.id_user
                                   WHERE a.id=?',[$request->invoice_id]);

  //$assignmentuser = $leader_name[0]->name;
  $Type = 'legalizacion';


  //var_dump($array[0]['email']);
  $MailSend = $leader_name[0]->email;
  
  //$CaseNumber =$Case[0]->id;
  //$MailSend= $array[0]['email'];
 // var_dump($array[0]['name']);

  $request->session()->put('assignmentuser', $leader_name[0]->name);
  
  // Envia info por correo: "Solicitud de gestón de legalización"
  $data = [
    $array[0]['name'],
    $Type,
    $request->invoice_id,
    $array[0]['name'],
    $data_anticipo[0]->empresa,
    $data_anticipo[0]->fecha_pago,
    $data_anticipo[0]->valor_anticipo,
    $data_anticipo[0]->forma_pago,
    $data_anticipo[0]->concepto,
    $data_anticipo[0]->name,
    $request->id_director, // $leader_id[0]->leader_id
  ];


        if ($MailSend != NULL) {
          Mail::to($MailSend)->send(new SendMail($data));
        }




    header("Location: https://flora.tierragro.com/anticipos/legalizacion",true,303);  
    exit();  

  //  return view('anticipos/legalizacion',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);
    

  }



  public function legalizaciongestion(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);
    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[27]);

    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user,
                                 ad.next_user_id,
                                 usn.cedula AS cedula,
                                 usnu.profile_name AS cargo_aprobador, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                 l.concept AS conceptolegalizacion,
                                us.name AS gestionando,
                                usn.name AS name,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"      
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            INNER JOIN distributions_legalizacion l
                            ON l.anticipo_id = a.id
                            LEFT JOIN anticipos_log al
                            ON al.id_document= a.id 
                            LEFT JOIN users usnu
                            ON usnu.id = al.next_user_id
                            WHERE ad.name_module= ? AND
                                  ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id) AND
                                  al.id = (SELECT MAX(id) FROM anticipos_log al WHERE al.id_document = a.id) AND 
                                  (a.estado = ? OR a.estado= ?)
                            GROUP BY a.id',[$user->id,$function_name[0]->name,4,5]);


    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[ 4 ]);

    // Bandera si esta logado: IMPORTANTE!!!!
    $user_id_ = -1;
    
    return view('anticipos/legalizacionesgestion',[
      'modules' => $modules,
      'user' => $user,
      'anticipos' => $anticipos,
      'count' => $count,
      'user_id_' => $user_id_,
    ]);
  }



  public function legalizaciongestioncorreo(Request $request){

    $user = $request->id_user;
    $application = new Application();
    $modules = $application->getModules($user,4);
    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[27]);

    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 ad.next_user_id,
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                 l.concept AS conceptolegalizacion,
                                us.name AS gestionando,
                                usn.name AS name,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"      
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            INNER JOIN distributions_legalizacion l
                            ON l.anticipo_id = a.id
                            WHERE ad.name_module= ? AND 
                                  (a.estado = ? OR a.estado= ?)
                            GROUP BY a.id',[$user,$function_name[0]->name,4,5]);
    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[4]);


    // Bandera No esta logado: IMPORTANTE!!!
    $user_id_ = $request->id_user;
    return view('anticipos/legalizacionesgestion',[
      'modules' => $modules,
      'user' => $user,
      'anticipos' => $anticipos,
      'count' => $count,
      'user_id_' => $user_id_,
    ]);
  }


  public function gestionaraceptarlegalizacion(Request $request){

    $id_function_cierre_legalizacion = 30;
    $rows_permissions = DB::SELECT('SELECT id_user FROM permission  
      WHERE function_id = ' . $id_function_cierre_legalizacion );

    $validador_contable = $rows_permissions[ 0 ]->id_user;


    $user = Auth::user();
    if ( $user ) {
      $application = new Application();
      $modules = $application->getModules($user->id,4);

      $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[
        27
      ]);

      $leader_id=DB::SELECT('SELECT leader_id AS leader_id FROM users
          WHERE id=?',[
            $user->id
          ]);

      $ultimo_registro=DB::SELECT('SELECT max(id) AS id FROM attacheds
          WHERE id_relation=? AND
          name_module=?',[
            $request->id,
            $function_name[0]->name
          ]);

      $anticipos1 = DB::SELECT('SELECT
            a.id AS id,
            a.id_user AS id_user,
            a.empresa AS empresa,
            usn.cedula AS cedula,
            usnu.name AS ultimo_aprobador,
            usnu.profile_name AS cargo_aprobador,  
            a.fecha_pago AS fecha_pago,
            a.valor_anticipo AS valor_anticipo,
            a.forma_pago AS forma_pago,
            a.concepto AS concepto,
            l.concept AS concept,
            us.name AS gestionando,
            usn.name AS name,
            usn.email AS email,
            CASE
              WHEN a.estado = 0 THEN "En proceso..."
              WHEN a.estado = 1 THEN "Aprobado"
              WHEN a.estado = 2 THEN "Pagado"
              WHEN a.estado = 3 THEN "Rechazado" 
              WHEN a.estado = 4 THEN "Proceso legalización"
              WHEN a.estado = 5 THEN "Legalización aprobada"
              WHEN a.estado = 6 THEN "Legalización cerrada"
              WHEN a.estado = 7 THEN "Legalización finalizada"
              WHEN a.estado = 8 THEN "Legalización rechazada"        
            END AS estado,
            ad.files AS adjunto
          FROM anticipos a
          INNER JOIN attacheds ad
          ON ad.id_relation = a.id
          INNER JOIN users us
          ON ad.next_user_id=?
          INNER JOIN users usn
          ON a.id_user =usn.id
          INNER JOIN distributions_legalizacion l
          ON l.anticipo_id=a.id
          LEFT JOIN anticipos_log lo
          ON lo.id_document= a.id 
          LEFT JOIN users usnu
          ON usnu.id = lo.next_user_id
          WHERE ad.name_module= ? AND
          (a.estado = ? OR a.estado = ?)
          GROUP BY a.id',[
            $user->id,
            $function_name[0]->name,
            4,
            5
          ]);



      $valor_anticipo_real=str_replace('.', '', $anticipos1[0]->valor_anticipo);

      $empresa=$anticipos1[0]->empresa;

      $cargo_usuario=DB::SELECT("SELECT profile_name AS profile
          FROM users
          WHERE id=?",[
            $user->id
          ]);

      $pos = strpos($cargo_usuario[0]->profile," ");
      $cargo_final =substr($cargo_usuario[0]->profile,0,$pos);

      $next_user_legalizacion='';

      if ((intval($valor_anticipo_real > 5000000)) && (($cargo_final == 'DIRECTOR') || ($cargo_final == 'DIRECTORA'))) 
      {
        if (($user->id == 1954) && ($anticipos1[0]->empresa == 'PEREZ Y CARDONA S.A.S')) {
            $update=DB::UPDATE('UPDATE attacheds
            SET next_user_id = ?
            WHERE id=?',[239,$ultimo_registro[0]->id]);

            $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id[0]->leader_id]);

            $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[
            $user->id,
            239,
            $request->id, // $ultimo_registro[0]->id
            $function_name[0]->name,
            date('Y-m-d')
            ]);

            $next_user_legalizacion = $leader_id[0]->leader_id;
        }elseif(($user->id == 1954) && ($anticipos1[0]->empresa == 'M.P GALAGRO S.A.S')){
          $update=DB::UPDATE('UPDATE attacheds
          SET next_user_id = ?
          WHERE id=?',[226,$ultimo_registro[0]->id]);

          $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id[0]->leader_id]);

          $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[
          $user->id,
          226,
          $request->id, // $ultimo_registro[0]->id
          $function_name[0]->name,
          date('Y-m-d')
          ]);

          $next_user_legalizacion = $leader_id[0]->leader_id;

        }else{
        $update=DB::UPDATE('UPDATE attacheds
                          SET next_user_id = ?
                          WHERE id=?',[$leader_id[0]->leader_id,$ultimo_registro[0]->id]);

        $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id[0]->leader_id]);

        $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[
          $user->id,
          $leader_id[0]->leader_id,
          $request->id, // $ultimo_registro[0]->id
          $function_name[0]->name,
          date('Y-m-d')
        ]);

        $next_user_legalizacion = $leader_id[0]->leader_id;
      }

      }
      elseif((intval($valor_anticipo_real >= 5000000)) && (($cargo_final == 'GERENTE')))
      {
        $update=DB::UPDATE('UPDATE attacheds
          SET next_user_id = ?
          WHERE id=?',[
            $validador_contable,
            $ultimo_registro[0]->id
          ]);

        $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
          $validador_contable
        ]);

        $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[
          $user->id,
          $validador_contable,
          $request->id, // $ultimo_registro[0]->id
          $function_name[0]->name,
          date('Y-m-d')
        ]);
        $next_user_legalizacion = $validador_contable;


      }
      elseif((intval($valor_anticipo_real < 5000000)) && (($cargo_final == 'GERENTE')))
      {
        $update=DB::UPDATE('UPDATE attacheds
            SET next_user_id = ?
            WHERE id=?',[
              $validador_contable,
              $ultimo_registro[0]->id
            ]);

        $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
          $validador_contable
        ]);

        $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[
          $user->id,
          $validador_contable,
          $request->id, // $ultimo_registro[0]->id,
          $function_name[0]->name,
          date('Y-m-d')
        ]);

        $next_user_legalizacion = $validador_contable;

      }
      elseif((intval($valor_anticipo_real < 5000000)) && (($cargo_final == 'DIRECTOR') || ($cargo_final == 'DIRECTORA')))
      {
        $update=DB::UPDATE('UPDATE attacheds
            SET next_user_id = ?
            WHERE id=?',[
              $validador_contable,
              $ultimo_registro[0]->id
            ]);

        $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
          $validador_contable
        ]);

        $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)", [
          $user->id,
          $validador_contable,
          $request->id, // $ultimo_registro[0]->id
          $function_name[0]->name,
          date('Y-m-d')
        ]);

        $next_user_legalizacion = $validador_contable;

      }
      else{

        $update = DB::UPDATE('UPDATE attacheds
            SET next_user_id = ?
            WHERE id=?',[
              $leader_id[0]->leader_id,
              $ultimo_registro[0]->id
            ]);

        $leader_name = DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
          $leader_id[0]->leader_id
        ]);

        $guardado_datos_log=DB::INSERT("INSERT INTO anticipos_log(user_id,next_user_id,id_document,type_document, created_at) VALUES (?,?,?,?,?)",[
          $user->id,
          $validador_contable,
          $request->id, // $ultimo_registro[0]->id
          $function_name[0]->name,
          date('Y-m-d'),
        ]);

        $next_user_legalizacion = $validador_contable;

      }


      $update = DB::UPDATE('UPDATE anticipos
        SET estado = ?
        WHERE id=?',[
          5,//5
          $request->id
        ]);


      $anticipos = DB::SELECT('SELECT
          a.id AS id,
          a.id_user AS id_user,
          ad.next_user_id,
          usn.cedula AS cedula,
          usnu.profile_name AS cargo_aprobador, 
          a.fecha_pago AS fecha_pago,
          a.valor_anticipo AS valor_anticipo,
          a.forma_pago AS forma_pago,
          a.concepto AS concepto,
          l.concept AS conceptolegalizacion,
          us.name AS gestionando,
          usn.name AS name,
          CASE
            WHEN a.estado = 0 THEN "En proceso..."
            WHEN a.estado = 1 THEN "Aprobado"
            WHEN a.estado = 2 THEN "Pagado"
            WHEN a.estado = 3 THEN "Rechazado" 
            WHEN a.estado = 4 THEN "Proceso legalización"
            WHEN a.estado = 5 THEN "Legalización aprobada"
            WHEN a.estado = 6 THEN "Legalización cerrada"
            WHEN a.estado = 7 THEN "Legalización finalizada"
            WHEN a.estado = 8 THEN "Legalización rechazada"      
          END AS estado,
          ad.files AS adjunto
        FROM anticipos a
        INNER JOIN attacheds ad
        ON ad.id_relation = a.id
        INNER JOIN users us
        ON ad.next_user_id=?
        INNER JOIN users usn
        ON a.id_user =usn.id
        INNER JOIN distributions_legalizacion l
        ON l.anticipo_id = a.id
        LEFT JOIN anticipos_log al
        ON al.id_document= a.id 
        LEFT JOIN users usnu
        ON usnu.id = al.next_user_id
        WHERE ad.name_module= ? AND
        ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id) AND
        al.id = (SELECT MAX(id) FROM anticipos_log al WHERE al.id_document = a.id) AND 
        (a.estado = ? OR a.estado= ?)
        GROUP BY a.id',[
          $user->id,
          $function_name[0]->name,
          4,//4
          5,//5
        ]);


      $count= DB::SELECT('SELECT count(a.id) AS count 
        FROM anticipos a
        INNER JOIN users u
        ON u.id=a.id_user
        WHERE a.estado = ?',[
          4,//4
        ]);

      $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
          a.fecha_pago AS fecha_pago,
          a.valor_anticipo AS valor_anticipo,
          a.forma_pago AS forma_pago,
          a.concepto AS concepto,
          u.name AS name
        FROM anticipos a
        INNER JOIN users u
        ON u.id=a.id_user
        WHERE a.id=?',[
          $request->id
        ]);

      //$assignmentuser = $leader_name[0]->name;
      $Type = 'legalizacion';

      //var_dump($array[0]['email']);
      $MailSend = $leader_name[0]->email;
    
      //$CaseNumber =$Case[0]->id;
      //$MailSend= $array[0]['email'];
      //var_dump($array[0]['name']);

      $request->session()->put('assignmentuser', $leader_name[0]->name);
  
      $data = [
        $leader_name[0]->name,
        $Type,
        $request->id,
        $leader_name[0]->name,
        $data_anticipo[0]->empresa,
        $data_anticipo[0]->fecha_pago,
        $data_anticipo[0]->valor_anticipo,
        $data_anticipo[0]->forma_pago,
        $data_anticipo[0]->concepto,
        $data_anticipo[0]->name,
        $next_user_legalizacion,
      ];

      if ($MailSend != NULL) {
        Mail::to($MailSend)->send(new SendMail($data));
      }


      // Bandera si esta logado: iMPORTANTE !!!!
      $user_id_ = -1;
      return view('anticipos/legalizacionesgestion',[
        'modules' => $modules,
        'user' => $user,
        'anticipos' => $anticipos,
        'count' => $count,
        'user_id_' => $user_id_,
      ]);
      
    }
    else{

      $user = $request->id_user;
      $application = new Application();
      $modules = $application->getModules($user,4);

      $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[
        27, //27
      ]);

      $leader_id = DB::SELECT('SELECT leader_id AS leader_id FROM users
          WHERE id=?',[$user]);

      $ultimo_registro = DB::SELECT('SELECT max(id) AS id FROM attacheds
        WHERE id_relation=? AND
        name_module=?',[
          $request->id,
          $function_name[0]->name
        ]);



      $anticipos1=DB::SELECT('SELECT
          a.id AS id,
          a.id_user AS id_user, 
          a.fecha_pago AS fecha_pago,
          a.valor_anticipo AS valor_anticipo,
          a.forma_pago AS forma_pago,
          a.concepto AS concepto,
          l.concept AS conceptolegalizacion,
          us.name AS gestionando,
          usn.name AS name,
          usn.email AS email,
          CASE
            WHEN a.estado = 0 THEN "En proceso..."
            WHEN a.estado = 1 THEN "Aprobado"
            WHEN a.estado = 2 THEN "Pagado"
            WHEN a.estado = 3 THEN "Rechazado" 
            WHEN a.estado = 4 THEN "Proceso legalización"
            WHEN a.estado = 5 THEN "Legalización aprobada"
            WHEN a.estado = 6 THEN "Legalización cerrada"
            WHEN a.estado = 7 THEN "Legalización finalizada"
            WHEN a.estado = 8 THEN "Legalización rechazada"        
          END AS estado,
        ad.files AS adjunto
        FROM anticipos a
        INNER JOIN attacheds ad
        ON ad.id_relation = a.id
        INNER JOIN users us
        ON ad.next_user_id=?
        INNER JOIN users usn
        ON a.id_user =usn.id
        INNER JOIN distributions_legalizacion l
        ON l.anticipo_id=a.id
        WHERE ad.name_module= ? AND 
        (a.estado = ? OR a.estado = ?)
        GROUP BY a.id',[
          $user,
          $function_name[0]->name,
          4,//4
          5,//5
        ]);

      $valor_anticipo_real=str_replace('.', '', $anticipos1[0]->valor_anticipo);

      $cargo_usuario=DB::SELECT("SELECT profile_name AS profile
        FROM users
        WHERE id=?",[
          $user 
        ]);

      $pos = strpos($cargo_usuario[0]->profile," ");
      $cargo_final =substr($cargo_usuario[0]->profile,0,$pos);

      
      $next_user_legalizacion=''; // ..1

      if ((intval($valor_anticipo_real > 5000000)) && (($cargo_final == 'DIRECTOR') || ($cargo_final == 'DIRECTORA'))) {
        $update=DB::UPDATE('UPDATE attacheds
        SET next_user_id = ?
        WHERE id=?',[
          $leader_id[0]->leader_id,
          $ultimo_registro[0]->id
        ]);
        $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
          $leader_id[0]->leader_id
        ]);

        $next_user_legalizacion = $leader_id[0]->leader_id; // ..2
      }
      elseif((intval($valor_anticipo_real >= 5000000)) && (($cargo_final == 'GERENTE'))){
          $update=DB::UPDATE('UPDATE attacheds
            SET next_user_id = ?
            WHERE id=?',[
              $validador_contable,
              $ultimo_registro[0]->id,
            ]);
          $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
            $validador_contable
          ]);

          $next_user_legalizacion = $validador_contable; // ..3
      }
      elseif((intval($valor_anticipo_real < 5000000)) && (($cargo_final == 'GERENTE'))){
        $update=DB::UPDATE('UPDATE attacheds
          SET next_user_id = ?
          WHERE id=?',[
            $validador_contable,
            $ultimo_registro[0]->id
          ]);
        $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
          $validador_contable
        ]);

        $next_user_legalizacion = $validador_contable; // ..4
      }
      elseif((intval($valor_anticipo_real < 5000000)) && (($cargo_final == 'DIRECTOR') || ($cargo_final == 'DIRECTORA'))){
        $update=DB::UPDATE('UPDATE attacheds
            SET next_user_id = ?
            WHERE id=?',[
              $validador_contable,
              $ultimo_registro[0]->id
            ]);
        $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
          $validador_contable
        ]);

        $next_user_legalizacion = $validador_contable; // ..5
      }
      else{
        $update = DB::UPDATE('UPDATE attacheds
            SET next_user_id = ?
            WHERE id=?',[
              $leader_id[0]->leader_id,
              $ultimo_registro[0]->id
            ]);
        $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
          $leader_id[0]->leader_id
        ]);

        $next_user_legalizacion = $validador_contable; // ..6
      }


      $update=DB::UPDATE('UPDATE anticipos
          SET estado = ?
          WHERE id=?',[
            5,//5,
            $request->id
          ]);


      $anticipos = DB::SELECT('SELECT
            a.id AS id,
            a.id_user AS id_user,
            ad.next_user_id,
            a.fecha_pago AS fecha_pago,
            a.valor_anticipo AS valor_anticipo,
            a.forma_pago AS forma_pago,
            a.concepto AS concepto,
            l.concept AS conceptolegalizacion,
            us.name AS gestionando,
            usn.name AS name,
            CASE
              WHEN a.estado = 0 THEN "En proceso..."
              WHEN a.estado = 1 THEN "Aprobado"
              WHEN a.estado = 2 THEN "Pagado"
              WHEN a.estado = 3 THEN "Rechazado" 
              WHEN a.estado = 4 THEN "Proceso legalización"
              WHEN a.estado = 5 THEN "Legalización aprobada"
              WHEN a.estado = 6 THEN "Legalización cerrada"
              WHEN a.estado = 7 THEN "Legalización finalizada"
              WHEN a.estado = 8 THEN "Legalización rechazada"       
            END AS estado,
            ad.files AS adjunto
          FROM anticipos a
          INNER JOIN attacheds ad
          ON ad.id_relation = a.id
          INNER JOIN users us
          ON ad.next_user_id=?
          INNER JOIN users usn
          ON a.id_user =usn.id
          INNER JOIN distributions_legalizacion l
          ON l.anticipo_id=a.id
          WHERE ad.name_module= ? AND 
          ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id) AND
          (a.estado = ? OR a.estado= ?)
          GROUP BY a.id',[
            $user,
            $function_name[0]->name,
            4,//4
            5,//5
          ]);

      $count= DB::SELECT('SELECT count(a.id) AS count 
          FROM anticipos a
          INNER JOIN users u
          ON u.id=a.id_user
          WHERE a.estado = ?',[
           4,//4
          ]);

      $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
          a.fecha_pago AS fecha_pago,
          a.valor_anticipo AS valor_anticipo,
          a.forma_pago AS forma_pago,
          a.concepto AS concepto,
          u.name AS name
          FROM anticipos a
          INNER JOIN users u
          ON u.id=a.id_user
          WHERE a.id=?',[
            $request->id
          ]);

      //$assignmentuser = $leader_name[0]->name;
      $Type = 'legalizacion';


      //var_dump($array[0]['email']);
      $MailSend = $leader_name[0]->email;

      //$CaseNumber =$Case[0]->id;
      //$MailSend= $array[0]['email'];
      // var_dump($array[0]['name']);

      $request->session()->put('assignmentuser', $leader_name[0]->name);
  
      $data = [
        $leader_name[0]->name,
        $Type,
        $request->id,
        $leader_name[0]->name,
        $data_anticipo[0]->empresa,
        $data_anticipo[0]->fecha_pago,
        $data_anticipo[0]->valor_anticipo,
        $data_anticipo[0]->forma_pago,
        $data_anticipo[0]->concepto,
        $data_anticipo[0]->name,
        $next_user_legalizacion, // ..6
      ];

      if ($MailSend != NULL) {
        Mail::to($MailSend)->send(new SendMail($data));
      }


      // Bandera No esta logado: IMPORTANTE!!!
      $user_id_ = $request->id_user;
      return view('anticipos/legalizacionesgestion',[
        'modules' => $modules,
        'user' => $user,
        'anticipos' => $anticipos,
        'count' => $count,
        'user_id_' => $user_id_,
      ]);
    }
  }



public function legalizacioncerrar(Request $request){


    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);
    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[27]);

    $leader_id=DB::SELECT('SELECT leader_id AS leader_id FROM users
                             WHERE id=?',[$user->id]);

    $ultimo_registro=DB::SELECT('SELECT max(id) AS id FROM attacheds
                                 WHERE id_relation=? AND
                                       name_module=?',[$request->id,$function_name[0]->name]);



   /* $anticipos1=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                 d.concept AS conceptolegalizacion,
                                us.name AS gestionando,
                                usn.name AS name,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"       
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            INNER JOIN distributions_legalizacion d
                            ON d.anticipo_id=a.id
                            WHERE ad.name_module= ? AND 
                                  (a.estado = ? OR a.estado = ?)
                            GROUP BY a.id',[$user->id,$function_name[0]->name,4,5]);*/

    

    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                 l.concept AS conceptolegalizacion,
                                us.name AS gestionando,
                                usn.name AS name,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"       
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            INNER JOIN distributions_legalizacion l
                            ON l.anticipo_id = a.id
                            WHERE ad.name_module= ? AND 
                                  (a.estado = ? OR a.estado= ?) 
                            GROUP BY a.id',[$user->id,$function_name[0]->name,4,5]);
    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE (a.estado = ? OR a.estado=?)',[4,5]);
    return view('anticipos/legalizacion_cerrar',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);
    

  }






  public function gestionarcerrarlegalizacion(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);
    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[27]);

    $leader_id=DB::SELECT('SELECT leader_id AS leader_id FROM users
                             WHERE id=?',[$user->id]);

    $ultimo_registro=DB::SELECT('SELECT max(id) AS id FROM attacheds
                                 WHERE id_relation=? AND
                                       name_module=?',[$request->id,$function_name[0]->name]);



    $anticipos1=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                us.name AS gestionando,
                                usn.name AS name,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"        
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            WHERE ad.name_module= ? AND 
                                  (a.estado = ? OR a.estado = ?)
                            GROUP BY a.id',[$user->id,$function_name[0]->name,4,5]);



   /* if (($anticipos1[0]->valor_anticipo > 5000000) && ($user->id == '26')) {
       $update=DB::UPDATE('UPDATE attacheds
                        SET next_user_id = ?
                        WHERE id=?',[$leader_id[0]->leader_id,$ultimo_registro[0]->id]);
    }elseif(($anticipos1[0]->valor_anticipo < 5000000) && ($user->id == '26')){
        $update=DB::UPDATE('UPDATE attacheds
                        SET next_user_id = ?
                        WHERE id=?',['215',$ultimo_registro[0]->id]);
    }else{
      $update=DB::UPDATE('UPDATE attacheds
                        SET next_user_id = ?
                        WHERE id=?',[$leader_id[0]->leader_id,$ultimo_registro[0]->id]);
    }*/

    $update=DB::UPDATE('UPDATE anticipos
                        SET estado = 6
                        WHERE id=?',[$request->id]);


    $update=DB::UPDATE('UPDATE attacheds
                        SET next_user_id = ?
                        WHERE id=?',[$user->id,$ultimo_registro[0]->id]);


   // $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',['189']);


    $anticipos=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                 l.concept AS conceptolegalizacion,
                                us.name AS gestionando,
                                usn.name AS name,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"       
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            INNER JOIN distributions_legalizacion l
                            ON l.anticipo_id = a.id
                            WHERE ad.name_module= ? AND 
                                  (a.estado = ? OR a.estado= ?) AND 
                                  ad.id =(SELECT MAX(id) FROM attacheds ad WHERE ad.id_relation = a.id)
                            GROUP BY a.id',[$user->id,$function_name[0]->name,4,5]);
    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[4]);
  
    $data_anticipo=DB::SELECT('SELECT a.empresa AS empresa,
                                   a.fecha_pago AS fecha_pago,
                                   a.valor_anticipo AS valor_anticipo,
                                   a.forma_pago AS forma_pago,
                                   a.concepto AS concepto,
                                   u.name AS name
                                   FROM anticipos a
                                   INNER JOIN users u
                                   ON u.id=a.id_user
                                   WHERE a.id=?',[$request->id]);

  //$assignmentuser = $leader_name[0]->name;
  /*$Type = 'legalizacion';


  //var_dump($array[0]['email']);
  $MailSend = $leader_name[0]->email;
  
  //$CaseNumber =$Case[0]->id;
  //$MailSend= $array[0]['email'];
 // var_dump($array[0]['name']);

  $request->session()->put('assignmentuser', $leader_name[0]->name);
  
  $data=[$leader_name[0]->name,$Type,$request->id,$leader_name[0]->name,$data_anticipo[0]->empresa,$data_anticipo[0]->fecha_pago,$data_anticipo[0]->valor_anticipo,$data_anticipo[0]->forma_pago,$data_anticipo[0]->concepto,$data_anticipo[0]->name];



  Mail::to($MailSend)->send(new SendMail($data));*/




    return view('anticipos/legalizacion_cerrar',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos,'count'=>$count]);
    

  }




  public function gestionarfinalizarlegalizacion(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);
    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[27]);

    $leader_id=DB::SELECT('SELECT leader_id AS leader_id FROM users
                             WHERE id=?',[$user->id]);

    $ultimo_registro=DB::SELECT('SELECT max(id) AS id FROM attacheds
                                 WHERE id_relation=? AND
                                       name_module=?',[$request->id,$function_name[0]->name]);



    $anticipos1=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                 l.concept AS conceptolegalizacion,
                                us.name AS gestionando,
                                usn.name AS name,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"      
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            INNER JOIN distributions_legalizacion l
                            ON l.anticipo_id=a.id
                            WHERE ad.name_module= ? AND 
                                  (a.estado = ?)
                            GROUP BY a.id',[$user->id,$function_name[0]->name,6]);
    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[4]);
   return view('anticipos/legalizacion_finalizar',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos1,'count'=>$count]);
    

  }





public function gestionarfinalizacionlegalizacion(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);

    // Obtener el ID del User con la opción: Pagar anticipos
    $id_function_pagar_anticipos = 26;
    $rows_permissions2 = DB::SELECT('SELECT id_user FROM permission  
      WHERE function_id = ' . $id_function_pagar_anticipos );
      

    $validador_tesoreria = $rows_permissions2[ 0 ]->id_user;


    $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[
      27, //27
    ]);


    $leader_id=DB::SELECT('SELECT leader_id AS leader_id FROM users
                             WHERE id=?',[$user->id]);

    $ultimo_registro=DB::SELECT('SELECT max(id) AS id FROM attacheds
                                 WHERE id_relation=? AND
                                       name_module=?',[$request->id,$function_name[0]->name]);

    $update=DB::UPDATE('UPDATE anticipos
                        SET estado = 7
                        WHERE id=?',[$request->id]);


    $update=DB::UPDATE('UPDATE attacheds
        SET next_user_id = ?
        WHERE id=?',[
          $validador_tesoreria,
          $ultimo_registro[0]->id
        ]);


    $anticipos1=DB::SELECT('SELECT
                                 a.id AS id,
                                 a.id_user AS id_user, 
                                 a.fecha_pago AS fecha_pago,
                                 a.valor_anticipo AS valor_anticipo,
                                 a.forma_pago AS forma_pago,
                                 a.concepto AS concepto,
                                 l.concept AS conceptolegalizacion,
                                us.name AS gestionando,
                                usn.name AS name,
                            CASE
                            WHEN a.estado = 0 THEN "En proceso..."
                            WHEN a.estado = 1 THEN "Aprobado"
                            WHEN a.estado = 2 THEN "Pagado"
                            WHEN a.estado = 3 THEN "Rechazado" 
                            WHEN a.estado = 4 THEN "Proceso legalización"
                            WHEN a.estado = 5 THEN "Legalización aprobada"
                            WHEN a.estado = 6 THEN "Legalización cerrada"
                            WHEN a.estado = 7 THEN "Legalización finalizada"
                            WHEN a.estado = 8 THEN "Legalización rechazada"        
                            END AS estado,
                            ad.files AS adjunto
                            FROM anticipos a
                            INNER JOIN attacheds ad
                            ON ad.id_relation = a.id
                            INNER JOIN users us
                            ON ad.next_user_id=?
                            INNER JOIN users usn
                            ON a.id_user =usn.id
                            INNER JOIN distributions_legalizacion l
                            ON l.anticipo_id=a.id
                            WHERE ad.name_module= ? AND 
                                  (a.estado = ?)
                            GROUP BY a.id',[$user->id,$function_name[0]->name,6]);
    $count= DB::SELECT('SELECT count(a.id) AS count 
                        FROM anticipos a
                               INNER JOIN users u
                               ON u.id=a.id_user
                        WHERE a.estado = ?',[4]);
   return view('anticipos/legalizacion_finalizar',['modules' => $modules,'user' => $user,'anticipos'=>$anticipos1,'count'=>$count]);
    

  }
  


  public function gastos(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);
    $costCenters = CostCenter::where('active','=',1)
                       ->orderby('name','asc')->get();
    $id_documento=$request->id;
    $cuentas = DB::SELECT('SELECT id AS id,
                           Cuenta AS cuenta
                           FROM cuentas_cecos');

   $directores=DB::SELECT("SELECT id AS id,
                                  name AS name,
                                  profile_name AS profile
                               FROM users
                               WHERE ((SUBSTRING(LTRIM(RTRIM(profile_name)),1,8)=? 
                               OR    SUBSTRING(LTRIM(RTRIM(profile_name)),1,9)=?) 
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?
                               OR (id) = ?)
                               AND active = ?",['DIRECTOR','DIRECTORA',6,239,226,315,2169,69,1]);

    $validacion=0;


    return view('/anticipos/gastos',['modules' => $modules,'user' => $user,'cuentas'=>$cuentas,'directores'=>$directores,'validacion'=>$validacion]);
  }



  public function gastos_save(Request $request){

  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);
  $valor=0;


  $input=$request->all();
  
  $cantidad=($input['countfields']);
  $cantidadadjuntos=($input['countfieldsadd']);
  $leader_id=$request->id_director;

  for ($i=1; $i <=$cantidad ; $i++) { 
    $valor =$valor + intval(preg_replace('/[@\.\;\" "]+/', '', $input['value'.$i]));
  }

  $save=DB::INSERT('INSERT INTO gastos (id_user,empresa,fecha_pago,valor_reintregro,forma_pago,concepto,estado) VALUES (?,?,?,?,?,?,?)',[$user->id,$request->empresa,$request->fecha_anticipo,$valor,$request->forma_pago,$request->concepto_anticipo,0]);
  $maxId=DB::SELECT('SELECT max(id) AS id_gasto FROM gastos');
  $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[37]);
  if ($request->file1 != NULL) 
  {
     for ($i=1; $i <=$cantidadadjuntos; $i++) {
      $file = $request->file('file'.$i);
            $ext = $file->getClientOriginalExtension();
            $nombre = Str::random(6).".".$ext;
            \Storage::disk('facturas')->put($nombre,  \File::get($file));
            $guardado_datos=DB::INSERT("INSERT INTO attacheds(id_user,next_user_id,files,id_relation,id_function,name_module, created_at) VALUES (?,?,?,?,?,?,?)",[$user->id,$leader_id,$nombre,$maxId[0]->id_gasto,37,$function_name[0]->name,date('Y-m-d')]);
           // $save=DB::INSERT('INSERT INTO gastos (id_user,empresa,fecha_pago,valor_reintregro,forma_pago,concepto,proveedor,estado) VALUES (?,?,?,?,?,?,?,?)',[$user->id,$request->empresa,$request->fecha_anticipo,$request->valor_anticipo,$request->forma_pago,$request->concepto_anticipo,'',0]);
     }
   }

   $guardado_datos_log=DB::INSERT("INSERT INTO gastos_log(user_id,next_user_id,id_document,created_at) VALUES (?,?,?,?)",[$user->id,$request->id_director,$maxId[0]->id_gasto,date('Y-m-d')]);
   $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id]);
   
   for ($j=1; $j <=$cantidad ; $j++) { 
    $distributions_gastos = DB::INSERT('INSERT INTO distributions_gastos (gasto_id,cost_center_id,cuenta,value,created_at, updated_at, active) VALUES (?,?,?,?,?,?,?)',[$maxId[0]->id_gasto, $input['coce'.$j], $input['cuenta'.$j], $input['value'.$j],date('Y-m-d'),date('Y-m-d'),1]);
  }

        $assignmentuser = $leader_name[0]->name;
        $Type = 'gastos';
        $MailSend= $leader_name[0]->email;

        $request->session()->put('assignmentuser', $leader_name[0]->name);
        
        $data=[$assignmentuser,$Type,$maxId[0]->id_gasto,$leader_name[0]->name,$request->empresa,$request->fecha_anticipo,$valor,$request->forma_pago,$request->concepto_anticipo,$request->observacion_anticipo,$user->name,$user->name,$leader_id];

        if ($MailSend != NULL) {
          Mail::to($MailSend)->send(new SendMail($data));
        }

        $cuentas = DB::SELECT('SELECT id AS id,
        Cuenta AS cuenta
        FROM cuentas_cecos');

        $directores=DB::SELECT("SELECT id AS id,
                      name AS name,
                      profile_name AS profile
                    FROM users
                    WHERE ((SUBSTRING(LTRIM(RTRIM(profile_name)),1,8)=? 
                    OR    SUBSTRING(LTRIM(RTRIM(profile_name)),1,9)=?) 
                    OR (id) = ?
                    OR (id) = ?
                    OR (id) = ?
                    OR (id) = ?)
                    AND active = ?",['DIRECTOR','DIRECTORA',6,239,226,315,1]);

        $validacion=1;


        return view('/anticipos/gastos',['modules' => $modules,'user' => $user,'cuentas'=>$cuentas,'directores'=>$directores,'validacion'=>$validacion]);

  }



  public function costcenterlegalizacion(Request $request){
     $empresa=$request->empresa;
     $costcenters='';


     switch ($empresa) {
        case 'PEREZ Y CARDONA S.A.S':
              $costcenters= DB::SELECT("SELECT code AS code,
                                                name AS name
                                        FROM  cost_centers
                                        WHERE SUBSTRING(code, 1, 1)=?",[1]);
          break;
        case 'M.P GALAGRO S.A.S':
              $costcenters= DB::SELECT("SELECT code AS code,
                                                name AS name
                                        FROM  cost_centers
                                        WHERE SUBSTRING(code, 1, 1)=?",[2]);
          break;
        case 'SUPER AGRO S.A.S':
              $costcenters= DB::SELECT("SELECT code AS code,
                                                name AS name
                                        FROM  cost_centers
                                        WHERE SUBSTRING(code, 1, 1)=?",[4]);
          break;
     }

      echo json_encode($costcenters);
  }



  public function gastosgestion(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);

    $anticipos=DB::SELECT('SELECT g.id AS id,
                                  g.empresa AS company,
                                  u.name AS solicitante,
                                  g.fecha_pago AS fecha_pago,
                                  g.valor_reintregro AS valor,
                                  g.concepto AS concepto,
                              CASE
                                  when g.estado = 0 then "Radicada"
                                  when g.estado = 1 then "Aprobada"
                                  when g.estado = 2 then "Pagada"
                              END
                                AS estado
                              FROM gastos g
                              INNER JOIN gastos_log l
                              ON l.id_document= g.id
                              INNER JOIN users u
                              ON u.id= g.id_user
                              WHERE l.id = (SELECT MAX(id) FROM gastos_log l WHERE l.id_document = g.id) AND (g.estado=0 OR g.estado=1) AND
                                l.next_user_id = ?
                        GROUP BY g.id',[$user->id]);

    return view('anticipos/gastosgestion',[
      'modules' => $modules,
      'user' => $user,
      'anticipos' => $anticipos
    ]);
  }


  public function gastosgestionuser(Request $request){

    $user = $request->id_user;
    $application = new Application();
    $modules = $application->getModules($user,4);

    $anticipos=DB::SELECT('SELECT g.id AS id,
                                  g.empresa AS company,
                                  u.name AS solicitante,
                                  g.fecha_pago AS fecha_pago,
                                  g.valor_reintregro AS valor,
                                  g.concepto AS concepto,
                              CASE
                                  when g.estado = 0 then "Radicada"
                                  when g.estado = 1 then "Aprobada"
                                  when g.estado = 2 then "Pagada"
                              END
                                AS estado
                              FROM gastos g
                              INNER JOIN gastos_log l
                              ON l.id_document= g.id
                              INNER JOIN users u
                              ON u.id= g.id_user
                              WHERE l.id = (SELECT MAX(id) FROM gastos_log l WHERE l.id_document = g.id) AND (g.estado=0 OR g.estado=1) AND
                                l.next_user_id = ?
                              GROUP BY g.id',[$user]);

    return view('anticipos/gastosgestionuser',[
      'modules' => $modules,
      'user' => $user,
      'anticipos' => $anticipos
    ]);
  }



  public function flujogastos(Request $request){
    
    $flujosgastos= DB::SELECT("SELECT u.name AS nombre1,
                                    u2.name AS nombre2,
                                    l.created_at AS fecha
                                FROM users u
                                INNER JOIN gastos_log l
                                ON l.user_id=u.id
                                INNER JOIN users u2
                                ON u2.id=l.next_user_id
                                WHERE l.id_document=?",[$request->id]);

    echo json_encode($flujosgastos);



}


public function adjuntosfilesgastos(Request $request){
    
  $adjuntosgastos= DB::SELECT("SELECT a.created_at AS fecha,
                                a.files AS file,
                                u.name AS usuario
                              FROM attacheds a
                              INNER JOIN users u
                              ON u.id=a.id_user
                              WHERE id_relation =? AND a.id_function=?",[$request->id,37]);

  echo json_encode($adjuntosgastos);



}


public function adjuntosdistribuciongastos(Request $request){
    
  $adjuntosgastos= DB::SELECT("SELECT c.name as ceco,
                                      c1.Cuenta as cuenta,
                                      d.value as valor
                              FROM cost_centers c
                              INNER JOIN distributions_gastos d
                              ON d.cost_center_id = c.code
                              INNER JOIN cuentas_cecos c1
                              ON c1.id = d.cuenta
                              WHERE d.gasto_id=?",[$request->id]);

  echo json_encode($adjuntosgastos);



}


public function aceptargastos(Request $request){
  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);


  $leader_id = DB::SELECT('SELECT leader_id AS leader_id FROM users
                           WHERE id=?',[$user->id]);

  $cargo_usuario=DB::SELECT("SELECT profile_name AS profile FROM users
                               WHERE id=?",[$user->id]);

    $pos = strpos($cargo_usuario[0]->profile," ");
    $cargo_final =substr($cargo_usuario[0]->profile,0,$pos);

    $valor_anticipo=DB::SELECT('SELECT g.valor_reintregro AS valor, g.concepto AS concepto,g.fecha_pago AS fecha_pago,u.name as nombre FROM gastos g INNER JOIN users u ON u.id=g.id_user WHERE g.id=?',[$request->id]);
    $valor_anticipo_real=$valor_anticipo[0]->valor;

    $next_user_legalizacion=''; // ..1



    // Obtener el ID del User con la opción: Cierre de legalización
    $id_function_cierre_legalizacion = 30;
    $id_function_pago_legalizacion = 41;
    $rows_permissions = DB::SELECT('SELECT id_user FROM permission  
      WHERE function_id = ' . $id_function_cierre_legalizacion );

    $rows_permissions_pagos = DB::SELECT('SELECT id_user FROM permission  
    WHERE function_id = ' . $id_function_pago_legalizacion );

    $validador_contable = 2030;

    $validador_cartera = $rows_permissions_pagos[ array_rand($rows_permissions_pagos) ]->id_user;

    // Obtener el ID del User con la opción: Pagar anticipos
    $id_function_pagar_anticipos = 26;
    $rows_permissions2 = DB::SELECT('SELECT id_user FROM permission  
      WHERE function_id = ' . $id_function_pagar_anticipos );

    $validador_tesoreria = $rows_permissions2[ 0 ]->id_user;

    if ((intval($valor_anticipo_real > 5000000)) && (($cargo_final == 'DIRECTOR') || ($cargo_final == 'DIRECTORA'))) {
    $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id[0]->leader_id
    ]);
    $next_user_legalizacion = $leader_id[0]->leader_id; // ..2
    }elseif((intval($valor_anticipo_real >= 5000000)) && (($cargo_final == 'GERENTE'))){
    $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
        $validador_contable
      ]);

      $next_user_legalizacion = $validador_contable; // ..3
    }elseif((intval($valor_anticipo_real < 5000000)) && (($cargo_final == 'GERENTE'))){
    $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
      $validador_contable
    ]);
    $next_user_legalizacion = $validador_contable; // ..4
    }elseif((intval($valor_anticipo_real < 5000000)) && (($cargo_final == 'DIRECTOR') || ($cargo_final == 'DIRECTORA'))){
    $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
      $validador_contable
    ]);
    $next_user_legalizacion = $validador_contable; // ..5
    }else{
      if ($user->id == $validador_contable) {
        $next_user_legalizacion = $validador_cartera;
        $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$validador_cartera]);
      }else{
        $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id[0]->leader_id]);
        $next_user_legalizacion = $validador_contable; // ..6        
      }
    }

    $update=DB::UPDATE('UPDATE gastos
      SET estado = ?
      WHERE id=?',[1,$request->id]);

    $guardado_datos_log=DB::INSERT("INSERT INTO gastos_log(user_id,next_user_id,id_document,created_at) VALUES (?,?,?,?)",[$user->id,$next_user_legalizacion,$request->id,date('Y-m-d')]);

    $assignmentuser = $leader_name[0]->name;
    $Type = 'gastos';
    $MailSend= $leader_name[0]->email;

    $request->session()->put('assignmentuser', $leader_name[0]->name);
    
    $data=[$valor_anticipo[0]->nombre,$Type,$request->id,$leader_name[0]->name,$request->empresa,$valor_anticipo[0]->fecha_pago,$valor_anticipo_real,$request->forma_pago,$valor_anticipo[0]->concepto,$request->observacion_anticipo,$valor_anticipo[0]->nombre,$user->name,$next_user_legalizacion];

    if ($MailSend != NULL) {
      Mail::to($MailSend)->send(new SendMail($data));
    }

    $anticipos=DB::SELECT('SELECT g.id AS id,
                                  g.empresa AS company,
                                  u.name AS solicitante,
                                  g.fecha_pago AS fecha_pago,
                                  g.valor_reintregro AS valor,
                                  g.concepto AS concepto,
                              CASE
                                  when g.estado = 0 then "Radicada"
                                  when g.estado = 1 then "Aprobada"
                                  when g.estado = 2 then "Pagada"
                              END
                                AS estado
                              FROM gastos g
                              INNER JOIN gastos_log l
                              ON l.id_document= g.id
                              INNER JOIN users u
                              ON u.id= g.id_user
                              WHERE l.id = (SELECT MAX(id) FROM gastos_log l WHERE l.id_document = g.id) AND (g.estado=0 OR g.estado=1) AND
                                l.next_user_id = ?
                        GROUP BY g.id',[$user->id]);

    return view('anticipos/gastosgestion',['modules' => $modules,'user' => $user,'anticipos' => $anticipos]);
}




public function aceptargastosuser(Request $request){

  $user=$request->id_user;
  $application = new Application();
  $modules = $application->getModules($user,4);

  $name_person=DB::SELECT("SELECT name AS name FROM users WHERE id=?",[$user]);


  $leader_id = DB::SELECT('SELECT leader_id AS leader_id FROM users
                           WHERE id=?',[$user]);

  $cargo_usuario=DB::SELECT("SELECT profile_name AS profile FROM users
                               WHERE id=?",[$user]);

    $pos = strpos($cargo_usuario[0]->profile," ");
    $cargo_final =substr($cargo_usuario[0]->profile,0,$pos);

    $valor_anticipo=DB::SELECT('SELECT g.valor_reintregro AS valor, g.concepto AS concepto,g.fecha_pago AS fecha_pago,u.name as nombre FROM gastos g INNER JOIN users u ON u.id=g.id_user WHERE g.id=?',[$request->id]);
    $valor_anticipo_real=$valor_anticipo[0]->valor;

    $next_user_legalizacion=''; // ..1



    // Obtener el ID del User con la opción: Cierre de legalización
    $id_function_cierre_legalizacion = 30;
    $id_function_pago_legalizacion = 41;
    $rows_permissions = DB::SELECT('SELECT id_user FROM permission  
      WHERE function_id = ' . $id_function_cierre_legalizacion );

    $rows_permissions_pagos = DB::SELECT('SELECT id_user FROM permission  
    WHERE function_id = ' . $id_function_pago_legalizacion );

    $validador_contable = 2030;

    $validador_cartera = $rows_permissions_pagos[ array_rand($rows_permissions_pagos) ]->id_user;

    // Obtener el ID del User con la opción: Pagar anticipos
    $id_function_pagar_anticipos = 26;
    $rows_permissions2 = DB::SELECT('SELECT id_user FROM permission  
      WHERE function_id = ' . $id_function_pagar_anticipos );

    $validador_tesoreria = $rows_permissions2[ 0 ]->id_user;

    if ((intval($valor_anticipo_real > 5000000)) && (($cargo_final == 'DIRECTOR') || ($cargo_final == 'DIRECTORA'))) {
    $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id[0]->leader_id
    ]);
    $next_user_legalizacion = $leader_id[0]->leader_id; // ..2
    }elseif((intval($valor_anticipo_real >= 5000000)) && (($cargo_final == 'GERENTE'))){
    $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
        $validador_contable
      ]);

      $next_user_legalizacion = $validador_contable; // ..3
    }elseif((intval($valor_anticipo_real < 5000000)) && (($cargo_final == 'GERENTE'))){
    $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
      $validador_contable
    ]);
    $next_user_legalizacion = $validador_contable; // ..4
    }elseif((intval($valor_anticipo_real < 5000000)) && (($cargo_final == 'DIRECTOR') || ($cargo_final == 'DIRECTORA'))){
    $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[
      $validador_contable
    ]);
    $next_user_legalizacion = $validador_contable; // ..5
    }else{
      if ($user == $validador_contable) {
        $next_user_legalizacion = $validador_cartera;
        $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$validador_cartera]);
      }else{
        $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id[0]->leader_id]);
        $next_user_legalizacion = $validador_contable; // ..6        
      }
    }

    $update=DB::UPDATE('UPDATE gastos
      SET estado = ?
      WHERE id=?',[1,$request->id]);

    $guardado_datos_log=DB::INSERT("INSERT INTO gastos_log(user_id,next_user_id,id_document,created_at) VALUES (?,?,?,?)",[$user,$next_user_legalizacion,$request->id,date('Y-m-d')]);

    $assignmentuser = $leader_name[0]->name;
    $Type = 'gastos';
    $MailSend= $leader_name[0]->email;

    $request->session()->put('assignmentuser', $leader_name[0]->name);
    
    $data=[$valor_anticipo[0]->nombre,$Type,$request->id,$leader_name[0]->name,$request->empresa,$valor_anticipo[0]->fecha_pago,$valor_anticipo_real,$request->forma_pago,$valor_anticipo[0]->concepto,$request->observacion_anticipo,$valor_anticipo[0]->nombre,$name_person[0]->name,$next_user_legalizacion];

    if ($MailSend != NULL) {
      Mail::to($MailSend)->send(new SendMail($data));
    }

    $anticipos=DB::SELECT('SELECT g.id AS id,
                                  g.empresa AS company,
                                  u.name AS solicitante,
                                  g.fecha_pago AS fecha_pago,
                                  g.valor_reintregro AS valor,
                                  g.concepto AS concepto,
                              CASE
                                  when g.estado = 0 then "Radicada"
                                  when g.estado = 1 then "Aprobada"
                                  when g.estado = 2 then "Pagada"
                              END
                                AS estado
                              FROM gastos g
                              INNER JOIN gastos_log l
                              ON l.id_document= g.id
                              INNER JOIN users u
                              ON u.id= g.id_user
                              WHERE l.id = (SELECT MAX(id) FROM gastos_log l WHERE l.id_document = g.id) AND (g.estado=0 OR g.estado=1) AND
                                l.next_user_id = ?
                        GROUP BY g.id',[$user]);

    return view('anticipos/gastosgestionuser',['modules' => $modules,'user' => $user,'anticipos' => $anticipos]);
}



public function historialgastos(Request $request){

  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);

  $anticipos=DB::SELECT('SELECT g.id AS id,
                                g.empresa AS company,
                                u.name AS solicitante,
                                g.fecha_pago AS fecha_pago,
                                g.valor_reintregro AS valor,
                                g.concepto AS concepto,
                            CASE
                                when g.estado = 0 then "Radicada"
                                when g.estado = 1 then "Aprobada"
                                when g.estado = 2 then "Pagada"
                                when g.estado = 3 then "Rechazado"
                            END
                              AS estado
                            FROM gastos g
                            INNER JOIN gastos_log l
                            ON l.id_document= g.id
                            INNER JOIN users u
                            ON u.id= g.id_user
                            WHERE g.id_user = ?
                      GROUP BY g.id',[$user->id]);
              

  return view('anticipos/historialgastos',[
    'modules' => $modules,
    'user' => $user,
    'anticipos' => $anticipos
  ]);
}


public function gastospagos(Request $request){

  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);

  $anticipos=DB::SELECT('SELECT g.id AS id,
                                g.empresa AS company,
                                u.name AS solicitante,
                                g.fecha_pago AS fecha_pago,
                                g.valor_reintregro AS valor,
                                g.concepto AS concepto,
                            CASE
                                when g.estado = 0 then "Radicada"
                                when g.estado = 1 then "Aprobada"
                                when g.estado = 2 then "Pagada"
                                when g.estado = 3 then "Rechazado"
                            END
                              AS estado
                            FROM gastos g
                            INNER JOIN gastos_log l
                            ON l.id_document= g.id
                            INNER JOIN users u
                            ON u.id= g.id_user
                            WHERE l.id = (SELECT MAX(id) FROM gastos_log l WHERE l.id_document = g.id) AND
                                  l.next_user_id = ? AND 
                                  g.estado NOT IN (?,?)
                      GROUP BY g.id',[$user->id,2,3]);
              

  return view('anticipos/gastospagos',[
    'modules' => $modules,
    'user' => $user,
    'anticipos' => $anticipos
  ]);
}



public function pagogasto(Request $request){

  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);


  $pagogasto=DB::UPDATE('UPDATE gastos
                        SET estado = ?
                        WHERE id=?',[2,$request->id]);


$valor_anticipo=DB::SELECT('SELECT g.valor_reintregro AS valor, g.concepto AS concepto,g.fecha_pago AS fecha_pago,u.name as nombre,u.email AS email,g.motivo_rechazo AS motivo_rechazo FROM gastos g INNER JOIN users u ON u.id=g.id_user WHERE g.id=?',[$request->id]);

//$assignmentuser = $leader_name[0]->name;
$Type = 'gastospago';
$MailSend= $valor_anticipo[0]->email;

$request->session()->put('assignmentuser', $valor_anticipo[0]->nombre);

$data=[$valor_anticipo[0]->nombre,$Type,$request->invoice_id,$valor_anticipo[0]->nombre,$request->empresa,$valor_anticipo[0]->fecha_pago,$valor_anticipo[0]->valor,$request->forma_pago,$valor_anticipo[0]->concepto,$valor_anticipo[0]->motivo_rechazo,$valor_anticipo[0]->nombre,$user->name];

if ($MailSend != NULL) {
  Mail::to($MailSend)->send(new SendMail($data));
}

  $anticipos=DB::SELECT('SELECT g.id AS id,
                                g.empresa AS company,
                                u.name AS solicitante,
                                g.fecha_pago AS fecha_pago,
                                g.valor_reintregro AS valor,
                                g.concepto AS concepto,
                            CASE
                                when g.estado = 0 then "Radicada"
                                when g.estado = 1 then "Aprobada"
                                when g.estado = 2 then "Pagada"
                                when g.estado = 3 then "Rechazado"
                            END
                              AS estado
                            FROM gastos g
                            INNER JOIN gastos_log l
                            ON l.id_document= g.id
                            INNER JOIN users u
                            ON u.id= g.id_user
                            WHERE l.id = (SELECT MAX(id) FROM gastos_log l WHERE l.id_document = g.id) AND
                                  l.next_user_id = ? AND 
                                  g.estado NOT IN (?,?)
                      GROUP BY g.id',[$user->id,2,3]);
              

  return view('anticipos/gastospagos',[
    'modules' => $modules,
    'user' => $user,
    'anticipos' => $anticipos
  ]);
}



public function rechazarlegalizaciongastos(Request $request){

  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);

  $rechazar_legalizacion=DB::UPDATE('UPDATE gastos
                                     SET estado = ?,
                                         motivo_rechazo = ?,
                                         id_user_rechazo = ?
                                     WHERE id=?',[3,$request->motivo_rechazo,$user->id,$request->invoice_id]);

  
$valor_anticipo=DB::SELECT('SELECT g.valor_reintregro AS valor, g.concepto AS concepto,g.fecha_pago AS fecha_pago,u.name as nombre,u.email AS email,g.motivo_rechazo AS motivo_rechazo FROM gastos g INNER JOIN users u ON u.id=g.id_user WHERE g.id=?',[$request->invoice_id]);

//$assignmentuser = $leader_name[0]->name;
$Type = 'gastosrechazo';
$MailSend= $valor_anticipo[0]->email;

$request->session()->put('assignmentuser', $valor_anticipo[0]->nombre);

$data=[$valor_anticipo[0]->nombre,$Type,$request->invoice_id,$valor_anticipo[0]->nombre,$request->empresa,$valor_anticipo[0]->fecha_pago,$valor_anticipo[0]->valor,$request->forma_pago,$valor_anticipo[0]->concepto,$valor_anticipo[0]->motivo_rechazo,$valor_anticipo[0]->nombre,$user->name];

if ($MailSend != NULL) {
  Mail::to($MailSend)->send(new SendMail($data));
}



  $anticipos=DB::SELECT('SELECT g.id AS id,
                                g.empresa AS company,
                                u.name AS solicitante,
                                g.fecha_pago AS fecha_pago,
                                g.valor_reintregro AS valor,
                                g.concepto AS concepto,
                            CASE
                                when g.estado = 0 then "Radicada"
                                when g.estado = 1 then "Aprobada"
                                when g.estado = 2 then "Pagada"
                            END
                              AS estado
                            FROM gastos g
                            INNER JOIN gastos_log l
                            ON l.id_document= g.id
                            INNER JOIN users u
                            ON u.id= g.id_user
                            WHERE l.id = (SELECT MAX(id) FROM gastos_log l WHERE l.id_document = g.id) AND (g.estado=0 OR g.estado=1) AND
                              l.next_user_id = ?
                      GROUP BY g.id',[$user->id]);

  return view('anticipos/gastosgestion',[
    'modules' => $modules,
    'user' => $user,
    'anticipos' => $anticipos
  ]);
}


public function rechazarlegalizaciongastosuser(Request $request){

  $user = $request->user_id;
  $application = new Application();
  $modules = $application->getModules($user,4);

  $name_usuario=DB::SELECT("SELECT name AS name FROM users WHERE id=?",[$user]);

  $rechazar_legalizacion=DB::UPDATE('UPDATE gastos
                                     SET estado = ?,
                                         motivo_rechazo = ?,
                                         id_user_rechazo = ?
                                     WHERE id=?',[3,$request->motivo_rechazo,$user,$request->invoice_id]);

  
$valor_anticipo=DB::SELECT('SELECT g.valor_reintregro AS valor, g.concepto AS concepto,g.fecha_pago AS fecha_pago,u.name as nombre,u.email AS email,g.motivo_rechazo AS motivo_rechazo FROM gastos g INNER JOIN users u ON u.id=g.id_user WHERE g.id=?',[$request->invoice_id]);

//$assignmentuser = $leader_name[0]->name;
$Type = 'gastosrechazo';
$MailSend= $valor_anticipo[0]->email;

$request->session()->put('assignmentuser', $valor_anticipo[0]->nombre);

$data=[$valor_anticipo[0]->nombre,$Type,$request->invoice_id,$valor_anticipo[0]->nombre,$request->empresa,$valor_anticipo[0]->fecha_pago,$valor_anticipo[0]->valor,$request->forma_pago,$valor_anticipo[0]->concepto,$valor_anticipo[0]->motivo_rechazo,$valor_anticipo[0]->nombre,$name_usuario[0]->name];

if ($MailSend != NULL) {
  Mail::to($MailSend)->send(new SendMail($data));
}



  $anticipos=DB::SELECT('SELECT g.id AS id,
                                g.empresa AS company,
                                u.name AS solicitante,
                                g.fecha_pago AS fecha_pago,
                                g.valor_reintregro AS valor,
                                g.concepto AS concepto,
                            CASE
                                when g.estado = 0 then "Radicada"
                                when g.estado = 1 then "Aprobada"
                                when g.estado = 2 then "Pagada"
                            END
                              AS estado
                            FROM gastos g
                            INNER JOIN gastos_log l
                            ON l.id_document= g.id
                            INNER JOIN users u
                            ON u.id= g.id_user
                            WHERE l.id = (SELECT MAX(id) FROM gastos_log l WHERE l.id_document = g.id) AND (g.estado=0 OR g.estado=1) AND
                              l.next_user_id = ?
                      GROUP BY g.id',[$user]);

  return view('anticipos/gastosgestionuser',[
    'modules' => $modules,
    'user' => $user,
    'anticipos' => $anticipos
  ]);
}



public function devoluciones(Request $request){
  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);
  $input = $request->all();

  $validacion=0;

  return view('invoice.returncreate', [
    'modules' => $modules,
    'user' => $user,
    'validacion' => $validacion
  ]); 
}





public function devoluciones_save(Request $request){

  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);
  $valor=0;


  $input=$request->all();
  
  $cantidadadjuntos=intval(($input['countfieldsadd']));

  $leader_id=DB::SELECT("SELECT id_user AS id FROM returns_approvers 
                         WHERE id_level_approver = ? AND
                               state = ? AND
                               id_area = ?
                          ORDER BY RAND() LIMIT 1",[2,1,$input['area']]);
  
  if ($input['observacion_devolucion'] == NULL) {
    $save=DB::INSERT('INSERT INTO returns (nombre_cliente,id_cliente,fecha_pago,id_area,forma_pago,valor_devolucion,motivo_devolucion) VALUES (?,?,?,?,?,?,?)',[$input['nombre_cliente'],$input['id_cliente'],$input['fecha_pago'],$input['area'],$input['forma_pago'],$input['valor'],$input['motivo_devolucion']]);
  }else{
    $save=DB::INSERT('INSERT INTO returns (nombre_cliente,id_cliente,fecha_pago,id_area,forma_pago,valor_devolucion,motivo_devolucion,observación) VALUES (?,?,?,?,?,?,?,?)',[$input['nombre_cliente'],$input['id_cliente'],$input['fecha_pago'],$input['area'],$input['forma_pago'],$input['valor'],$input['motivo_devolucion'],$input['observacion_devolucion']]);
  }

  $maxId=DB::SELECT('SELECT max(id) AS id_devolucion FROM returns');


  $function_name=DB::SELECT('SELECT name AS name FROM functions WHERE id=?',[43]);
  if ($request->file1 != NULL) 
  {
     for ($i=1; $i <= $cantidadadjuntos; $i++) {
      $file = $request->file('file'.$i);
            $ext = $file->getClientOriginalExtension();
            $nombre = Str::random(6).".".$ext;
            \Storage::disk('facturas')->put($nombre,  \File::get($file));
            $guardado_datos=DB::INSERT("INSERT INTO attacheds(id_user,next_user_id,files,id_relation,id_function,name_module, created_at) VALUES (?,?,?,?,?,?,?)",[$user->id,$leader_id[0]->id,$nombre,$maxId[0]->id_devolucion,43,$function_name[0]->name,date('Y-m-d')]);
           // $save=DB::INSERT('INSERT INTO gastos (id_user,empresa,fecha_pago,valor_reintregro,forma_pago,concepto,proveedor,estado) VALUES (?,?,?,?,?,?,?,?)',[$user->id,$request->empresa,$request->fecha_anticipo,$request->valor_anticipo,$request->forma_pago,$request->concepto_anticipo,'',0]);
     }
   }

   $guardado_datos_log=DB::INSERT("INSERT INTO returns_log(user_id,next_user_id,id_devolucion,estado,created_at) VALUES (?,?,?,?,?)",[$user->id,$leader_id[0]->id,$maxId[0]->id_devolucion,0,date('Y-m-d')]);
   $leader_name= DB::SELECT('SELECT first_name AS name, email AS email FROM users WHERE id=?',[$leader_id[0]->id]);
   
  
        $assignmentuser = $leader_name[0]->name;
        $Type = 'devoluciones';
        $MailSend= $leader_name[0]->email;

        $request->session()->put('assignmentuser', $leader_name[0]->name);
        
        $data=[$assignmentuser,$Type,$maxId[0]->id_devolucion,$leader_name[0]->name,$request->valor,$request->fecha_pago,$valor,$request->forma_pago,$request->motivo_devolucion,$request->observacion_devolucion,$user->name,$user->name];

        if ($MailSend != NULL) {
          Mail::to($MailSend)->send(new SendMail($data));
        }

        $validacion=1;

        return view('invoice.returncreate', [
          'modules' => $modules,
          'user' => $user,
          'validacion' => $validacion
        ]); 

  }

  function gestiondevoluciones(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);

   $datos = DB::SELECT("SELECT r.id AS id,
                                r.nombre_cliente AS cliente,
                                r.id_cliente AS id_cliente,
                                r.fecha_pago AS fecha_pago,
                         CASE
                         WHEN r.id_area = 1 THEN 'Punto de venta'
                         WHEN r.id_area = 2 THEN 'Domicilios'
                         END AS area,
                              r.forma_pago AS forma_pago,
                              r.valor_devolucion AS valor,
                              r.motivo_devolucion AS motivo,
                              us.name AS usuario_generador,
                              us.id AS id_generador,
                         CASE
                         WHEN r.state = 0 THEN 'Radicada'
                         WHEN r.state = 1 THEN 'Aprobada'
                         WHEN r.state = 2 THEN 'Pagada'
                         WHEN r.state = 3 THEN 'Rechazada'
                         END AS estado
                         FROM returns r
                         INNER JOIN attacheds t
                         ON t.id_relation =r.id
                         INNER JOIN users us
                         ON us.id= t.id_user
                         INNER JOIN returns_log l
                         ON l.id_devolucion=r.id
                         INNER JOIN users u
                         ON u.id= l.next_user_id AND l.id = (SELECT MAX(id) FROM returns_log l WHERE l.id_devolucion = r.id)
                         WHERE r.state IN (?,?) AND
                               u.id=? AND
                               t.id_function=?
                        GROUP BY r.id",[0,1,$user->id,43]);

    $count=count($datos);

    return view('invoice.gestionreturn', [
      'modules' => $modules,
      'user' => $user,
      'datos'=>$datos,
      'count'=>$count
    ]);


  }


  public function devolucionesLog ( Request $request ){

    $id = $request->id;
    $devolucionesLogs = DB::SELECT("SELECT 
        DATE_FORMAT(al.created_at, '%Y-%m-%d') AS date,
        al.user_id as user_id,
        u1.name as init_user,
        al.next_user_id,
        u2.name as next_user,
      CASE 
       WHEN al.estado = 0 THEN 'Radicada'
       WHEN al.estado = 1 THEN 'Aprobada'
       WHEN al.estado = 2 THEN 'Pagada'
       WHEN al.estado = 3 THEN 'Rechazada'
      END AS estado
      FROM returns_log al
      JOIN users u1 ON u1.id = al.user_id
      JOIN users u2 ON u2.id = al.next_user_id
      WHERE al.id_devolucion = ?
      ORDER BY al.id DESC", [ $id]);

    echo json_encode($devolucionesLogs);
  }



  public function adjuntosfilesdevoluciones(Request $request){

    $adjuntosfiles= DB::SELECT("SELECT DATE_FORMAT(i.created_at, '%Y-%m-%d') AS date,
                       CASE
                       WHEN i.files IS NOT NULL THEN i.files
                       ELSE ''  
                       END AS file
            FROM attacheds i
            WHERE i.id_relation = ? AND
                  i.id_function = ? AND
                  i.files IS NOT NULL",[$request->id,43]);

    echo json_encode($adjuntosfiles);



}



public function devolucionesrrechazar(Request $request){

  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);

  $rechazar_devoluciones=DB::UPDATE('UPDATE returns
                                     SET state = ?,
                                         motivo_rechazo = ?,
                                         id_user_rechazo = ?
                                     WHERE id=?',[3,$request->motivo_rechazo,$user->id,$request->invoice_id]);

  $original_user=DB::SELECT("SELECT id_user AS id_user
                             FROM attacheds 
                             WHERE id=(SELECT min(id) FROM attacheds WHERE id_relation= ? AND id_function=?)",[$request->invoice_id,43]);


  $guardado_datos_log=DB::INSERT("INSERT INTO returns_log(user_id,next_user_id,id_devolucion,estado,created_at) VALUES (?,?,?,?,?)",[$user->id,$original_user[0]->id_user,$request->invoice_id,3,date('Y-m-d')]);
  $valor_devolucion=DB::SELECT('SELECT g.valor_devolucion AS valor, g.motivo_devolucion AS concepto,g.observación AS observacion,g.fecha_pago AS fecha_pago,u.name as nombre,u.email AS email,g.motivo_rechazo AS motivo_rechazo FROM returns g INNER JOIN attacheds a ON (a.id_relation = g.id AND a.id_function = 43)  INNER JOIN users u ON u.id=a.id_user WHERE g.id=?',[$request->invoice_id]);

//$assignmentuser = $leader_name[0]->name;
$Type = 'devolucionrechazo';
$MailSend= $valor_devolucion[0]->email;

$request->session()->put('assignmentuser', $valor_devolucion[0]->nombre);

$data=[$valor_devolucion[0]->nombre,$Type,$request->invoice_id,$valor_devolucion[0]->nombre,$request->empresa,$valor_devolucion[0]->fecha_pago,$valor_devolucion[0]->valor,$request->forma_pago,$valor_devolucion[0]->concepto,$valor_devolucion[0]->motivo_rechazo,$valor_devolucion[0]->nombre,$user->name];

if ($MailSend != NULL) {
  Mail::to($MailSend)->send(new SendMail($data));
}



$datos = DB::SELECT("SELECT r.id AS id,
                        r.nombre_cliente AS cliente,
                        r.id_cliente AS id_cliente,
                        r.fecha_pago AS fecha_pago,
                        CASE
                        WHEN r.id_area = 1 THEN 'Punto de venta'
                        WHEN r.id_area = 2 THEN 'Domicilios'
                        END AS area,
                        r.forma_pago AS forma_pago,
                        r.valor_devolucion AS valor,
                        r.motivo_devolucion AS motivo,
                        us.name AS usuario_generador,
                        us.id AS id_generador,
                        CASE
                        WHEN r.state = 0 THEN 'Radicada'
                        WHEN r.state = 1 THEN 'Aprobada'
                        WHEN r.state = 2 THEN 'Pagada'
                        WHEN r.state = 3 THEN 'Rechazada'
                        END AS estado
                        FROM returns r
                        INNER JOIN attacheds t
                        ON t.id_relation =r.id
                        INNER JOIN users us
                        ON us.id= t.id_user
                        INNER JOIN returns_log l
                        ON l.id_devolucion=r.id
                        INNER JOIN users u
                        ON u.id= l.next_user_id AND l.id = (SELECT MAX(id) FROM returns_log l WHERE l.id_devolucion = r.id)
                        WHERE r.state = ? AND
                        u.id=? AND
                        t.id_function=?",[0,$user->id,43]);

$count=count($datos);

return view('invoice.gestionreturn', [
  'modules' => $modules,
  'user' => $user,
  'datos'=>$datos,
  'count'=>$count
]);
}





public function gestionaraceptardevolucion(Request $request){

  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);

  $user_level =DB::SELECT('SELECT id_level_approver+1 AS level
                           FROM returns_approvers
                           WHERE id_user=?',[$user->id]);
                        




  $leader = DB::SELECT('SELECT a.id_user AS leader_id,
                               u.name AS name,
                               u.email AS email 
                        FROM returns_approvers a
                        INNER JOIN users u
                        ON u.id=a.id_user
                           WHERE a.id_level_approver=?
                           ORDER BY RAND() LIMIT 1',[intval($user_level[0]->level)]);

  if (count($leader) != 0) {
    $update=DB::UPDATE('UPDATE returns
      SET state = ?
      WHERE id=?',[1,$request->id]);


    $guardado_datos_log=DB::INSERT("INSERT INTO returns_log(user_id,next_user_id,id_devolucion,estado,created_at) VALUES (?,?,?,?,?)",[$user->id,$leader[0]->leader_id,$request->id,1,date('Y-m-d')]);

    $valor_devolucion=DB::SELECT('SELECT g.valor_devolucion AS valor,g.forma_pago AS forma_pago,g.motivo_devolucion AS concepto,g.observación AS observacion,g.fecha_pago AS fecha_pago,u.name as nombre,u.email AS email,g.motivo_rechazo AS motivo_rechazo FROM returns g INNER JOIN attacheds a ON (a.id_relation = g.id AND a.id_function = 43)  INNER JOIN users u ON u.id=a.id_user WHERE g.id=?',[$request->id]);

    $assignmentuser = $leader[0]->name;
    $Type = 'devoluciones';
    $MailSend= $leader[0]->email;

    $request->session()->put('assignmentuser', $leader[0]->name);

    $data=[$valor_devolucion[0]->nombre,$Type,$request->id,$leader[0]->name,$valor_devolucion[0]->valor,$valor_devolucion[0]->fecha_pago,$valor_devolucion[0]->valor,$valor_devolucion[0]->forma_pago,$valor_devolucion[0]->concepto,$request->observacion_anticipo,$valor_devolucion[0]->nombre,$user->name,$valor_devolucion[0]->observacion];

    if ($MailSend != NULL) {
      Mail::to($MailSend)->send(new SendMail($data));
    }

    $datos = DB::SELECT("SELECT r.id AS id,
              r.nombre_cliente AS cliente,
              r.id_cliente AS id_cliente,
              r.fecha_pago AS fecha_pago,
              CASE
              WHEN r.id_area = 1 THEN 'Punto de venta'
              WHEN r.id_area = 2 THEN 'Domicilios'
              END AS area,
              r.forma_pago AS forma_pago,
              r.valor_devolucion AS valor,
              r.motivo_devolucion AS motivo,
              us.name AS usuario_generador,
              us.id AS id_generador,
              CASE
              WHEN r.state = 0 THEN 'Radicada'
              WHEN r.state = 1 THEN 'Aprobada'
              WHEN r.state = 2 THEN 'Pagada'
              WHEN r.state = 3 THEN 'Rechazada'
              END AS estado
              FROM returns r
              INNER JOIN attacheds t
              ON t.id_relation =r.id
              INNER JOIN users us
              ON us.id= t.id_user
              INNER JOIN returns_log l
              ON l.id_devolucion=r.id
              INNER JOIN users u
              ON u.id= l.next_user_id AND l.id = (SELECT MAX(id) FROM returns_log l WHERE l.id_devolucion = r.id)
              WHERE r.state IN ( ?,?) AND
              u.id=? AND
              t.id_function=?",[0,1,$user->id,43]);

$count=count($datos);

    return view('invoice.gestionreturn', [
    'modules' => $modules,
    'user' => $user,
    'datos'=>$datos,
    'count'=>$count
    ]);
  }else{

    $update=DB::UPDATE('UPDATE returns
      SET state = ?
      WHERE id=?',[2,$request->id]);

    $leader=DB::SELECT('SELECT u.name AS name,u.id AS leader_id,u.email AS email FROM users u
    INNER JOIN attacheds a
    ON a.id_relation = ? AND a.id_function = ? AND a.id_user=u.id',[$request->id,43]);


    $guardado_datos_log=DB::INSERT("INSERT INTO returns_log(user_id,next_user_id,id_devolucion,estado,created_at) VALUES (?,?,?,?,?)",[$user->id,$leader[0]->leader_id,$request->id,2,date('Y-m-d')]);

    $valor_devolucion=DB::SELECT('SELECT g.valor_devolucion AS valor,g.forma_pago AS forma_pago,g.motivo_devolucion AS concepto,g.fecha_pago AS fecha_pago,u.name as nombre,u.email AS email,g.motivo_rechazo AS motivo_rechazo FROM returns g INNER JOIN attacheds a ON (a.id_relation = g.id AND a.id_function = 43)  INNER JOIN users u ON u.id=a.id_user WHERE g.id=?',[$request->id]);



    $assignmentuser = $leader[0]->name;
    $Type = 'devolucionespago';
    $MailSend= $leader[0]->email;

    $request->session()->put('assignmentuser', $leader[0]->name);

   
    $data=[$valor_devolucion[0]->nombre,$Type,$request->id,$leader[0]->name,$valor_devolucion[0]->valor,$valor_devolucion[0]->fecha_pago,$valor_devolucion[0]->valor,$valor_devolucion[0]->forma_pago,$valor_devolucion[0]->concepto,$request->observacion_anticipo,$valor_devolucion[0]->nombre,$user->name];

    if ($MailSend != NULL) {
      Mail::to($MailSend)->send(new SendMail($data));
    }

    $datos = DB::SELECT("SELECT r.id AS id,
              r.nombre_cliente AS cliente,
              r.id_cliente AS id_cliente,
              r.fecha_pago AS fecha_pago,
              CASE
              WHEN r.id_area = 1 THEN 'Punto de venta'
              WHEN r.id_area = 2 THEN 'Domicilios'
              END AS area,
              r.forma_pago AS forma_pago,
              r.valor_devolucion AS valor,
              r.motivo_devolucion AS motivo,
              us.name AS usuario_generador,
              us.id AS id_generador,
              CASE
              WHEN r.state = 0 THEN 'Radicada'
              WHEN r.state = 1 THEN 'Aprobada'
              WHEN r.state = 2 THEN 'Pagada'
              WHEN r.state = 3 THEN 'Rechazada'
              END AS estado
              FROM returns r
              INNER JOIN attacheds t
              ON t.id_relation =r.id
              INNER JOIN users us
              ON us.id= t.id_user
              INNER JOIN returns_log l
              ON l.id_devolucion=r.id
              INNER JOIN users u
              ON u.id= l.next_user_id AND l.id = (SELECT MAX(id) FROM returns_log l WHERE l.id_devolucion = r.id)
              WHERE r.state IN ( ?,?) AND
              u.id=? AND
              t.id_function=?",[0,1,$user->id,43]);

$count=count($datos);

    return view('invoice.gestionreturn', [
    'modules' => $modules,
    'user' => $user,
    'datos'=>$datos,
    'count'=>$count
    ]); 


  }




 }





 public function historialdevoluciones(Request $request){

  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);


    $datos = DB::SELECT("SELECT r.id AS id,
                                r.nombre_cliente AS cliente,
                                r.id_cliente AS id_cliente,
                                r.fecha_pago AS fecha_pago,
                                CASE
                                WHEN r.id_area = 1 THEN 'Punto de venta'
                                WHEN r.id_area = 2 THEN 'Domicilios'
                                END AS area,
                                r.forma_pago AS forma_pago,
                                r.valor_devolucion AS valor,
                                r.motivo_devolucion AS motivo,
                                us.name AS usuario_generador,
                                us.id AS id_generador,
                                CASE
                                WHEN r.state = 0 THEN 'Radicada'
                                WHEN r.state = 1 THEN 'Aprobada'
                                WHEN r.state = 2 THEN 'Pagada'
                                WHEN r.state = 3 THEN 'Rechazada'
                                END AS estado
                                FROM returns r
                                INNER JOIN attacheds t
                                ON t.id_relation =r.id
                                INNER JOIN users us
                                ON us.id= t.id_user
                                INNER JOIN returns_log l
                                ON l.id_devolucion=r.id
                                INNER JOIN users u
                                ON u.id= l.next_user_id AND l.id = (SELECT MAX(id) FROM returns_log l WHERE l.id_devolucion = r.id)
                                WHERE t.id_user=? AND
                                t.id_function=?
                                GROUP BY r.id",[$user->id,43]);

$count=count($datos);

    return view('invoice.historialdevolucion', [
    'modules' => $modules,
    'user' => $user,
    'datos'=>$datos,
    'count'=>$count
    ]);
  


  }




  public function datosdevoluciones(Request $request){

    $id_devolucion = $request->id;
  
      $datos = DB::SELECT("SELECT r.id AS id,
                r.nombre_cliente AS cliente,
                r.id_cliente AS id_cliente,
                r.fecha_pago AS fecha_pago,
                l.user_id AS last_user_id,
                CASE
                WHEN r.id_area = 1 THEN 'Punto de venta'
                WHEN r.id_area = 2 THEN 'Domicilios'
                END AS area,
                r.forma_pago AS forma_pago,
                r.valor_devolucion AS valor,
                r.motivo_devolucion AS motivo,
                us.name AS usuario_generador,
                us.id AS id_generador,
                CASE
                WHEN r.state = 0 THEN 'Radicada'
                WHEN r.state = 1 THEN 'Aprobada'
                WHEN r.state = 2 THEN 'Pagada'
                WHEN r.state = 3 THEN 'Rechazada'
                END AS estado
                FROM returns r
                INNER JOIN attacheds t
                ON t.id_relation =r.id
                INNER JOIN users us
                ON us.id= t.id_user
                INNER JOIN returns_log l
                ON l.id_devolucion=r.id
                INNER JOIN users u
                ON u.id= l.user_id AND l.id = (SELECT MAX(id) FROM returns_log l WHERE l.id_devolucion = r.id)
                WHERE r.id=? AND
                t.id_function=?",[$id_devolucion,43]);
  
      echo json_encode($datos);
    
  
  
    } 
    
    

    public function devoluciones_update(Request $request){

      $user = Auth::user();
      $application = new Application();
      $modules = $application->getModules($user->id,4);

      $actualizacion = DB::UPDATE("UPDATE returns
                                   SET nombre_cliente= ?,
                                       id_cliente = ?,
                                       fecha_pago = ?,
                                       forma_pago = ?,
                                       valor_devolucion = ?,
                                       motivo_devolucion = ?,
                                       state = ?
                                  WHERE id = ?",[$request->nombre_cliente,
                                                 $request->id_cliente,
                                                 $request->fecha_pago,
                                                 $request->forma_pago,
                                                 $request->valor,
                                                 $request->motivo_rechazo,
                                                 1,
                                                 $request->id_devolucion]);
      
      $insert=DB::INSERT("INSERT INTO returns_log
                          (id_devolucion,user_id,next_user_id,estado,created_at) 
                          VALUES ($request->id_devolucion,$user->id,$request->id_last_person,1,CURDATE())");

      
      $file = $request->file('file');
      if ($request->hasFile('file')) {

        $ext = $file->getClientOriginalExtension();
        $nombre = Str::random(6).".".$ext;

        $insert_adjunto=DB::INSERT("INSERT INTO attacheds
                                    (id_user,next_user_id,files,id_relation,name_module,
                                     id_function,created_at) VALUES($user->id,$request->id_last_person,'".$nombre."',$request->id_devolucion,'Devoluciones',43,CURDATE())");
        \Storage::disk('facturas')->put($nombre,  \File::get($file));

      }

      $leader=DB::SELECT('SELECT u.name AS name,u.email AS email FROM users u
      INNER JOIN returns_log a
      ON a.id_devolucion = ? AND a.next_user_id=u.id
      WHERE a.id=(SELECT max(id) FROM returns_log a WHERE a.id_devolucion= ?)',[$request->id_devolucion,$request->id_devolucion]);

      $valor_devolucion=DB::SELECT('SELECT g.valor_devolucion AS valor,g.forma_pago AS forma_pago,g.observación AS observacion,g.motivo_devolucion AS concepto,g.fecha_pago AS fecha_pago,u.name as nombre,u.email AS email,g.motivo_rechazo AS motivo_rechazo FROM returns g INNER JOIN attacheds a ON (a.id_relation = g.id AND a.id_function = 43)  INNER JOIN users u ON u.id=a.id_user WHERE g.id=?',[$request->id_devolucion]);

      $assignmentuser = $leader[0]->name;
      $Type = 'devolucionupdate';
      $MailSend= $leader[0]->email;
  
      $request->session()->put('assignmentuser', $leader[0]->name);
  
      
      $data=[$valor_devolucion[0]->nombre,$Type,$request->id_devolucion,$leader[0]->name,$valor_devolucion[0]->valor,$valor_devolucion[0]->fecha_pago,$valor_devolucion[0]->valor,$valor_devolucion[0]->forma_pago,$valor_devolucion[0]->concepto,$valor_devolucion[0]->observacion,$valor_devolucion[0]->nombre,$user->name,$valor_devolucion[0]->observacion];
  
      if ($MailSend != NULL) {
        Mail::to($MailSend)->send(new SendMail($data));
      }
  

         
      $datos = DB::SELECT("SELECT r.id AS id,
                            r.nombre_cliente AS cliente,
                            r.id_cliente AS id_cliente,
                            r.fecha_pago AS fecha_pago,
                            CASE
                            WHEN r.id_area = 1 THEN 'Punto de venta'
                            WHEN r.id_area = 2 THEN 'Domicilios'
                            END AS area,
                            r.forma_pago AS forma_pago,
                            r.valor_devolucion AS valor,
                            r.motivo_devolucion AS motivo,
                            us.name AS usuario_generador,
                            us.id AS id_generador,
                            CASE
                            WHEN r.state = 0 THEN 'Radicada'
                            WHEN r.state = 1 THEN 'Aprobada'
                            WHEN r.state = 2 THEN 'Pagada'
                            WHEN r.state = 3 THEN 'Rechazada'
                            END AS estado
                            FROM returns r
                            INNER JOIN attacheds t
                            ON t.id_relation =r.id
                            INNER JOIN users us
                            ON us.id= t.id_user
                            INNER JOIN returns_log l
                            ON l.id_devolucion=r.id
                            INNER JOIN users u
                            ON u.id= l.next_user_id AND l.id = (SELECT MAX(id) FROM returns_log l WHERE l.id_devolucion = r.id)
                            WHERE t.id_user=? AND
                            t.id_function=?
                            GROUP BY r.id",[$user->id,43]);
    
    $count=count($datos);
    
        return view('invoice.historialdevolucion', [
        'modules' => $modules,
        'user' => $user,
        'datos'=>$datos,
        'count'=>$count
        ]);
      
    
    
      }

  public function devolucionesaceptar(Request $request){
    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);
  
    $user_level =DB::SELECT('SELECT id_level_approver+1 AS level
                             FROM returns_approvers
                             WHERE id_user=?',[$user->id]);
                          
  
  
  
  
    $leader = DB::SELECT('SELECT a.id_user AS leader_id,
                                 u.name AS name,
                                 u.email AS email 
                          FROM returns_approvers a
                          INNER JOIN users u
                          ON u.id=a.id_user
                             WHERE a.id_level_approver=?
                             ORDER BY RAND() LIMIT 1',[intval($user_level[0]->level)]);
  
    if (count($leader) != 0) {

      $update=DB::UPDATE('UPDATE returns
        SET state = ?
        WHERE id=?',[1,$request->invoice_id_adjuntar]);
  
  
      $guardado_datos_log=DB::INSERT("INSERT INTO returns_log(user_id,next_user_id,id_devolucion,estado,created_at) VALUES (?,?,?,?,?)",[$user->id,$leader[0]->leader_id,$request->invoice_id_adjuntar,1,date('Y-m-d')]);
      
  
      $valor_devolucion=DB::SELECT('SELECT g.valor_devolucion AS valor,g.forma_pago AS forma_pago,g.motivo_devolucion AS concepto,g.observación AS observacion,g.fecha_pago AS fecha_pago,u.name as nombre,u.email AS email,g.motivo_rechazo AS motivo_rechazo FROM returns g INNER JOIN attacheds a ON (a.id_relation = g.id AND a.id_function = 43)  INNER JOIN users u ON u.id=a.id_user WHERE g.id=?',[$request->invoice_id_adjuntar]);

      if ($request->file != NULL) 
      {
                $file = $request->file;
                $ext = $file->getClientOriginalExtension();
                $nombre = Str::random(6).".".$ext;
                \Storage::disk('facturas')->put($nombre,  \File::get($file));
                $guardado_datos=DB::INSERT("INSERT INTO attacheds(id_user,next_user_id,files,id_relation,id_function,name_module, created_at) VALUES (?,?,?,?,?,?,?)",[$user->id,$leader[0]->leader_id,$nombre,$request->invoice_id_adjuntar,43,'Devolucion',date('Y-m-d')]);
               // $save=DB::INSERT('INSERT INTO gastos (id_user,empresa,fecha_pago,valor_reintregro,forma_pago,concepto,proveedor,estado) VALUES (?,?,?,?,?,?,?,?)',[$user->id,$request->empresa,$request->fecha_anticipo,$request->valor_anticipo,$request->forma_pago,$request->concepto_anticipo,'',0]);
       }
  
      $assignmentuser = $leader[0]->name;
      $Type = 'devoluciones';
      $MailSend= $leader[0]->email;
  
      $request->session()->put('assignmentuser', $leader[0]->name);
  
      
      $data=[$valor_devolucion[0]->nombre,$Type,$request->invoice_id_adjuntar,$leader[0]->name,$valor_devolucion[0]->valor,$valor_devolucion[0]->fecha_pago,$valor_devolucion[0]->valor,$valor_devolucion[0]->forma_pago,$valor_devolucion[0]->concepto,$valor_devolucion[0]->observacion,$valor_devolucion[0]->nombre,$user->name,$valor_devolucion[0]->observacion];
  
      if ($MailSend != NULL) {
        Mail::to($MailSend)->send(new SendMail($data));
      }
  
      $datos = DB::SELECT("SELECT r.id AS id,
                r.nombre_cliente AS cliente,
                r.id_cliente AS id_cliente,
                r.fecha_pago AS fecha_pago,
                CASE
                WHEN r.id_area = 1 THEN 'Punto de venta'
                WHEN r.id_area = 2 THEN 'Domicilios'
                END AS area,
                r.forma_pago AS forma_pago,
                r.valor_devolucion AS valor,
                r.motivo_devolucion AS motivo,
                us.name AS usuario_generador,
                us.id AS id_generador,
                CASE
                WHEN r.state = 0 THEN 'Radicada'
                WHEN r.state = 1 THEN 'Aprobada'
                WHEN r.state = 2 THEN 'Pagada'
                WHEN r.state = 3 THEN 'Rechazada'
                END AS estado
                FROM returns r
                INNER JOIN attacheds t
                ON t.id_relation =r.id
                INNER JOIN users us
                ON us.id= t.id_user
                INNER JOIN returns_log l
                ON l.id_devolucion=r.id
                INNER JOIN users u
                ON u.id= l.next_user_id AND l.id = (SELECT MAX(id) FROM returns_log l WHERE l.id_devolucion = r.id)
                WHERE r.state IN ( ?,?) AND
                u.id=? AND
                t.id_function=?",[0,1,$user->id,43]);
  
  $count=count($datos);
  
      return view('invoice.gestionreturn', [
      'modules' => $modules,
      'user' => $user,
      'datos'=>$datos,
      'count'=>$count
      ]);
    }else{

     $update=DB::UPDATE('UPDATE returns
        SET state = ?
        WHERE id=?',[2,$request->invoice_id_adjuntar]);
  
      $leader=DB::SELECT('SELECT u.name AS name,u.id AS leader_id,u.email AS email FROM users u
      INNER JOIN attacheds a
      ON a.id_relation = ? AND a.id_function = ? AND a.id_user=u.id',[$request->invoice_id_adjuntar,43]);
  
  
      $guardado_datos_log=DB::INSERT("INSERT INTO returns_log(user_id,next_user_id,id_devolucion,estado,created_at) VALUES (?,?,?,?,?)",[$user->id,$leader[0]->leader_id,$request->invoice_id_adjuntar,2,date('Y-m-d')]);
  
      $valor_devolucion=DB::SELECT('SELECT g.valor_devolucion AS valor,g.forma_pago AS forma_pago,g.motivo_devolucion AS concepto,g.observación AS observacion,g.fecha_pago AS fecha_pago,u.name as nombre,u.email AS email,g.motivo_rechazo AS motivo_rechazo FROM returns g INNER JOIN attacheds a ON (a.id_relation = g.id AND a.id_function = 43)  INNER JOIN users u ON u.id=a.id_user WHERE g.id=?',[$request->invoice_id_adjuntar]);
  
      if ($request->file != NULL) 
      {
                $file = $request->file;
                $ext = $file->getClientOriginalExtension();
                $nombre = Str::random(6).".".$ext;
                \Storage::disk('facturas')->put($nombre,  \File::get($file));
                $guardado_datos=DB::INSERT("INSERT INTO attacheds(id_user,next_user_id,files,id_relation,id_function,name_module, created_at) VALUES (?,?,?,?,?,?,?)",[$user->id,$leader[0]->leader_id,$nombre,$request->invoice_id_adjuntar,43,'Devolucion',date('Y-m-d')]);
               // $save=DB::INSERT('INSERT INTO gastos (id_user,empresa,fecha_pago,valor_reintregro,forma_pago,concepto,proveedor,estado) VALUES (?,?,?,?,?,?,?,?)',[$user->id,$request->empresa,$request->fecha_anticipo,$request->valor_anticipo,$request->forma_pago,$request->concepto_anticipo,'',0]);
       }
      
  
      $assignmentuser = $leader[0]->name;
      $Type = 'devolucionespago';
      $MailSend= $leader[0]->email;
  
      $request->session()->put('assignmentuser', $leader[0]->name);
  
      
      $data=[$valor_devolucion[0]->nombre,$Type,$request->invoice_id_adjuntar,$leader[0]->name,$valor_devolucion[0]->valor,$valor_devolucion[0]->fecha_pago,$valor_devolucion[0]->valor,$valor_devolucion[0]->forma_pago,$valor_devolucion[0]->concepto,$valor_devolucion[0]->observacion,$valor_devolucion[0]->nombre,$user->name,$valor_devolucion[0]->observacion];
  
      if ($MailSend != NULL) {
        Mail::to($MailSend)->send(new SendMail($data));
      }
  
      $datos = DB::SELECT("SELECT r.id AS id,
                r.nombre_cliente AS cliente,
                r.id_cliente AS id_cliente,
                r.fecha_pago AS fecha_pago,
                CASE
                WHEN r.id_area = 1 THEN 'Punto de venta'
                WHEN r.id_area = 2 THEN 'Domicilios'
                END AS area,
                r.forma_pago AS forma_pago,
                r.valor_devolucion AS valor,
                r.motivo_devolucion AS motivo,
                us.name AS usuario_generador,
                us.id AS id_generador,
                CASE
                WHEN r.state = 0 THEN 'Radicada'
                WHEN r.state = 1 THEN 'Aprobada'
                WHEN r.state = 2 THEN 'Pagada'
                WHEN r.state = 3 THEN 'Rechazada'
                END AS estado
                FROM returns r
                INNER JOIN attacheds t
                ON t.id_relation =r.id
                INNER JOIN users us
                ON us.id= t.id_user
                INNER JOIN returns_log l
                ON l.id_devolucion=r.id
                INNER JOIN users u
                ON u.id= l.next_user_id AND l.id = (SELECT MAX(id) FROM returns_log l WHERE l.id_devolucion = r.id)
                WHERE r.state IN ( ?,?) AND
                u.id=? AND
                t.id_function=?",[0,1,$user->id,43]);
  
  $count=count($datos);
  
      return view('invoice.gestionreturn', [
      'modules' => $modules,
      'user' => $user,
      'datos'=>$datos,
      'count'=>$count
      ]);    
  
  
    }
  
  
  
  }


  public function distribution_suppliers(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);
  
    $distributions=DB::SELECT('SELECT id as id,
                                    supplier_name AS supplier_name,
                                    id_user AS id_user,
                                    user_name AS user_name,
                                    user_mail AS user_email
                                FROM invoices_distribution');
  
  $users=DB::SELECT('SELECT id as id,
                                    name AS name
                            FROM users
                            WHERE active=?',[1]);
  
  return view('invoice.distributions', [
    'modules' => $modules,
    'user' => $user,
    'distributions' => $distributions,
    'users' => $users
  ]);  
     
  }

  public function usersdistributions(Request $request){

    $distributions=DB::SELECT('SELECT id as id,
                                    name AS name,
                                    email AS email
                                FROM users
                                WHERE active = ?',[1]);
  
  echo json_encode($distributions);
     
  }


  public function usersdistributionsupdate(Request $request){

    $input = $request->all();
    $application = new Application();
    $modules = $application->getModules($input['user_on'],4);
  
    $select_user_data=DB::SELECT("SELECT name AS name,
                                         email AS email
                                  FROM users
                                  WHERE id=?",[$input['id_usuario']]);
  
     $update=DB::UPDATE("UPDATE invoices_distribution
                         SET id_user = ?,
                              user_name = ?,
                              user_mail = ?
                         WHERE id = ?",[$input['id_usuario'],$select_user_data[0]->name,$select_user_data[0]->email,$input['id_proveedor']]);
  
  
  
  $distributions=DB::SELECT('SELECT id as id,
                                  supplier_name AS supplier_name,
                                  id_user AS id_user,
                                  user_name AS user_name,
                                  user_mail AS user_email
                             FROM invoices_distribution');
  
  $users=DB::SELECT('SELECT id as id,
                            name AS name
                      FROM users
                      WHERE active=?',[1]);
  
  echo json_encode(1);
  
     
  }


  public function flowapprovers(Request $request){

    $flujo = $request->flow_id;
  
    $approvers = DB::SELECT("SELECT u.id AS code,
                                    u.name AS name
                             FROM users u
                             INNER JOIN invoice_approvers i
                             ON i.user_id = u.id
                             WHERE i.flow_id=? AND
                                   u.active= ?
                             GROUP BY u.id
                             ORDER BY u.id ASC",[$flujo,1]);
  
    echo json_encode($approvers);
  
  }




  public function rechazofactura(Request $request){
    //$user_envio = $request->id_user;
     $application = new Application();
    //$modules = $application->getModules($user_envio->id,4);
  
    $id_factura = $request->id_factura;
    $id_motivo_rechazo = $request->motivo_rechazo;
    $id_user = $request->id_user;
    $id_user_rechazo=$request->approver_id;
    $modules = $application->getModules($id_user,4);
    $description_rechazo='';
  
  
    $user = $id_user;
    $application = new Application();
    
    switch ($id_motivo_rechazo) {
      case '1':
        $description_rechazo='Error al radicar la factura';
        break;
      case '2':
        $description_rechazo='Selección errada de centros de costo';
        break;
      case '3':
        $description_rechazo='Datos errados en valores de la factura';
        break;
      case '4':
        $description_rechazo='Los productos o servicios no fueron recibidos';
        break;
  
    }
  
  
    $fecha=date("Y-m-d H:i:s");
  
    $original_user = DB::SELECT("SELECT user_id AS user_id
                                  FROM invoice_logg
                                  WHERE id=(SELECT min(id) FROM invoice_logg WHERE invoice_id=?)",[$id_factura]);
  
    $actualizacion_factura=DB::INSERT("INSERT INTO invoice_logg (invoice_id,user_id,state_id,description,next_user_id,created_at, updated_at)
                                       VALUES (?,?,?,?,?,?,?)",[$id_factura,$id_user,5,$description_rechazo,$original_user[0]->user_id,$fecha,$fecha]);
  
  
  
  $date = date ( 'Y-m-d' );
  $timestamp = strtotime($date);

  //echo json_encode(100);
  
  if ($id_motivo_rechazo == '3' || $id_motivo_rechazo == '4') {
          $datos_radian=DB::SELECT('SELECT  i.radian_state AS radian_state,
                                            s.nit AS supplierId,
                                            s.document_type AS document_type,
                                            c.nit AS receiverId,
                                            "01" AS documentTypeCode,
                                            i.number AS documentId,
                                            u.name AS username,
                                            "031" AS statusCode,
                                            "" AS statusReason,
                                            "" AS statusNote,
                                            u.cedula AS id,
                                            "" AS idDv,
                                            "13" AS idType,
                                            u.first_name AS firstName,
                                            u.last_name AS familyName,
                                            u.profile_name AS jobTitle,
                                            u.ubication_name AS OrganizationDepartment
                                            FROM invoices i
                                            INNER JOIN suppliers s
                                            ON s.id=i.supplier_id
                                            INNER JOIN companies c
                                            ON c.id=i.company
                                            INNER JOIN users u
                                            ON u.id=?
                                            WHERE i.id=?',[$user,$id_factura]);
  
      //  if ($datos_radian[0]->document_type == '31') {
      //  $myString = substr($datos_radian[0]->supplierId, 0, -1);
      //  }else{
        $myString = $datos_radian[0]->supplierId;
      //  }
  
      //  $myString = substr($datos_radian[0]->supplierId, 0, -1);
  
  
  
        if ($datos_radian[0]->radian_state != '030') {
            $array_datos_cadena['statusCode'] = $datos_radian[0]->statusCode;
            $array_datos_cadena['statusDate'] = strval($timestamp);
            $array_datos_cadena['statusReason'] = $description_rechazo;
            $array_datos_cadena['statusNote'] = $datos_radian[0]->statusNote;
            $array_datos_cadena['claimCode'] = "01";
      
            $array_cadena['supplierId'] = $myString;
            $array_cadena['receiverId'] = $datos_radian[0]->receiverId;
            $array_cadena['documentTypeCode'] = $datos_radian[0]->documentTypeCode;
            $array_cadena['documentId'] = $datos_radian[0]->documentId;
            $array_cadena['username'] = $datos_radian[0]->username;
            $array_cadena['documentStatus'] = $array_datos_cadena;
            $datos=json_encode($array_cadena);
            $url = "https://apivp.efacturacadena.com/v1/recepcion/estados .";
            $ch = curl_init($url);
            curl_setopt_array($ch, array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $datos,
            CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'efacturaAuthorizationToken: 9f9b4217-81d6-41df-9308-8ebed0235dc7',
            ),
            CURLOPT_RETURNTRANSFER => true,
            ));
            $resultado = curl_exec($ch);
            $json_data=json_decode($resultado, true);
  
            $application = new Application();
  
            $invoice = new Invoice();
            $invoices = $invoice->getActives(Auth::id());
            $countInvoices = count($invoices);
            
           //$modules = $application->getModules(Auth::id(),4);
  
            if ($json_data['statusCode'] == 200) {
                $actualizacion=DB::UPDATE('UPDATE invoices SET radian_state = ? ,cude = ? WHERE id=?',[$json_data['eventCode'],$json_data['cude'],$id_factura]);
            }
  
            echo json_encode($resultado);
        }else{
          //if ($datos_radian[0]->document_type == '31') {
          //    $myString = substr($datos_radian[0]->supplierId, 0, -1);
         // }else{
              $myString = $datos_radian[0]->supplierId;
         // }
  
         // $myString = substr($datos_radian[0]->supplierId, 0, -1);
  
          $array_datos_cadena['statusCode'] = '032';
          $array_datos_cadena['statusDate'] = strval($timestamp);
          $array_datos_cadena['statusReason'] = $datos_radian[0]->statusReason;
          $array_datos_cadena['statusNote'] = $datos_radian[0]->statusNote;
          $array_datos_cadena['id'] = $datos_radian[0]->id;
          $array_datos_cadena['idDv'] = $datos_radian[0]->idDv;
          $array_datos_cadena['idType'] = $datos_radian[0]->idType;
          $array_datos_cadena['firstName'] = $datos_radian[0]->firstName;
          $array_datos_cadena['familyName'] = $datos_radian[0]->familyName;
          $array_datos_cadena['jobTitle'] = $datos_radian[0]->jobTitle;
          $array_datos_cadena['OrganizationDepartment'] = $datos_radian[0]->OrganizationDepartment;               
  
          $array_cadena['supplierId'] = $myString;
          $array_cadena['receiverId'] = $datos_radian[0]->receiverId;
          $array_cadena['documentTypeCode'] = $datos_radian[0]->documentTypeCode;
          $array_cadena['documentId'] = $datos_radian[0]->documentId;
          $array_cadena['username'] = $datos_radian[0]->username;
          $array_cadena['documentStatus'] = $array_datos_cadena;
  
          $datos=json_encode($array_cadena);
          $url = "https://apivp.efacturacadena.com/v1/recepcion/estados .";
          $ch = curl_init($url);
          curl_setopt_array($ch, array(
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => $datos,
              CURLOPT_HTTPHEADER => array(
                  'Content-Type: application/json',
                  'efacturaAuthorizationToken: 9f9b4217-81d6-41df-9308-8ebed0235dc7',
              ),
              CURLOPT_RETURNTRANSFER => true,
          ));
          $resultado = curl_exec($ch);
          $actualizacion=DB::UPDATE('UPDATE invoices SET radian_state = ? WHERE id=?',['032',$id_factura]);
            $json_data2=json_decode($resultado, true);
            //$json_data['eventCode']
            if ($json_data2['eventCode'] == '032') {
              $datos_radian=DB::SELECT('SELECT  i.radian_state AS radian_state,
              s.nit AS supplierId,
              s.document_type AS document_type,
              c.nit AS receiverId,
              "01" AS documentTypeCode,
              i.number AS documentId,
              u.name AS username,
              "031" AS statusCode,
              "" AS statusReason,
              "" AS statusNote,
              u.cedula AS id,
              "" AS idDv,
              "13" AS idType,
              u.first_name AS firstName,
              u.last_name AS familyName,
              u.profile_name AS jobTitle,
              u.ubication_name AS OrganizationDepartment
              FROM invoices i
              INNER JOIN suppliers s
              ON s.id=i.supplier_id
              INNER JOIN companies c
              ON c.id=i.company
              INNER JOIN users u
              ON u.id=?
              WHERE i.id=?',[$user,$id_factura]);
  
              $array_datos_cadena['statusCode'] = $datos_radian[0]->statusCode;
              $array_datos_cadena['statusDate'] = strval($timestamp);
              $array_datos_cadena['statusReason'] = $description_rechazo;
              $array_datos_cadena['statusNote'] = $datos_radian[0]->statusNote;
              $array_datos_cadena['claimCode'] = "01";
        
              $array_cadena['supplierId'] = $myString;
              $array_cadena['receiverId'] = $datos_radian[0]->receiverId;
              $array_cadena['documentTypeCode'] = $datos_radian[0]->documentTypeCode;
              $array_cadena['documentId'] = $datos_radian[0]->documentId;
              $array_cadena['username'] = $datos_radian[0]->username;
              $array_cadena['documentStatus'] = $array_datos_cadena;
              $datos=json_encode($array_cadena);
              $url = "https://apivp.efacturacadena.com/v1/recepcion/estados .";
              $ch = curl_init($url);
              curl_setopt_array($ch, array(
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => $datos,
              CURLOPT_HTTPHEADER => array(
              'Content-Type: application/json',
              'efacturaAuthorizationToken: 9f9b4217-81d6-41df-9308-8ebed0235dc7',
              ),
              CURLOPT_RETURNTRANSFER => true,
              ));
              $resultado = curl_exec($ch);
              echo json_encode($resultado);
          }
          // Read the JSON file 
          
          
        }
    }else{
            echo json_encode(100);
    }
  
  }


  public function unassigned(Request $request) {
    $user = Auth::user();
    $modules = (new Application())->getModules($user->id, 4);
    $invoices = DB::table('invoices as i')
        ->select('i.id', 'i.number', 's.name as supplier', 'i.create_date as fecha_creacion', 'i.due_date', 'i.subtotal', 'i.iva', 'i.total', 'i.concept', 'i.file')
        ->join('suppliers as s', 's.id', '=', 'i.supplier_id')
        ->where('i.flow_id', 60)
        ->get();
    $countInvoices = $invoices->count();
    $suppliers = Supplier::select('suppliers.id', 'suppliers.nit', 'suppliers.name')
    ->where('active', 1)
    ->get();
    return view('invoice.unassignedfiles', [
        'modules' => $modules,
        'user' => $user,
        'invoices' => $invoices,
        'countInvoices' => $countInvoices,
        'suppliers' => $suppliers
    ]);
}



  public function takeinvoice(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);

    $id_factura= $request->id_factura;

    $returninvoice=DB::INSERT('INSERT INTO invoice_logg(invoice_id,user_id,state_id,description,next_user_id) VALUES(?,?,?,?,?)',[$id_factura,2198,1,'Factura en proceso...',$user->id]);
    return redirect()->route('invoices');

    
  }


  public function faccosto(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);
    $role=DB::SELECT('SELECT role_id AS role FROM invoice_approvers WHERE flow_id=? AND user_id = ?',[62,$user->id]);

    $invoices = DB::select('SELECT  L.id log_id,
                                    I.id invoice_id ,
                                    I.number,
                                    I.currency,
                                    I.subtotal,
                                    I.iva,
                                    I.total,
                                    I.priority,
                                    I.file AS file,
                                    I.revision AS revision,
                                    L.next_user_id,
                                    S.name supplier,
                                    DATE_FORMAT(I.created_at, "%Y-%m-%d") AS due_date,
                                    I.flow_id,
                                    L.description AS description,
                                  (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) LOG
                                  FROM invoices I
                                INNER JOIN invoice_logg L 
                                ON L.invoice_id = I.id AND L.id = (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) 
                                AND (next_user_id = 1968 OR next_user_id=1888) AND L.state_id <> 6
                                  INNER JOIN suppliers S ON S.id = I.supplier_id
                                ORDER BY DATE_FORMAT(I.created_at, "%Y-%m-%d") ASC');
      $countInvoices=count($invoices);

      $suppliers = Supplier::select('suppliers.id', 'suppliers.nit', 'suppliers.name')
          ->where('active', 1)
          ->get();

      return view('invoice.faccosto', [
        'modules' => $modules,
        'user' => $user,
        'invoices' => $invoices,
        'countInvoices' =>$countInvoices,
        'role'=>$role[0]->role,
        'suppliers' =>$suppliers
      ]); 

    }


      public function facosto(Request $request){

        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,4);
        $id_user = $request->id_user;

        $role=DB::SELECT('SELECT role_id AS role FROM invoice_approvers WHERE flow_id=? AND user_id = ?',[62,$user->id]);
        
        if ($role[0]->role == 1) {
          $approvers = Approver::where('flow_id','=',62)
          ->where('active','=',1)
          ->orderby('order','asc')->get();
        }else{
          $approvers = Approver::where('flow_id','=',62)
          ->where('active','=',1)
          ->where('user_id','<>',2101)
          ->orderby('order','asc')->get();
        }



        $id=$request->id;


        $supplier_name=$request->supplier_name;
        $invoice = Invoice::find($id);
        $prov = $supplier_name;


        $invoiceCC =DB::SELECT('SELECT c.name AS name
                                  FROM cost_centers c
                                  INNER JOIN distributionscc d
                                  ON d.cost_center_id= c.id
                                  WHERE d.invoice_id=?',[$id]);

        $invoiceCCAutorizations =DB::SELECT('SELECT autorizacion AS name
        FROM invoice_logg 
        WHERE invoice_id=?',[$id]);
        $totalusers=DB::SELECT('SELECT id AS id, 
                                      name AS name 
                                FROM users
                                WHERE active=?',[1]);

        $totalubications=DB::SELECT('SELECT ubication_name AS ubication_name 
        FROM users
        GROUP BY ubication_name');
        
        $flow = $invoice->flow;

        $costCenters = CostCenter::where('active','=',1)
                       ->orderby('name','asc')->get();

        $suppliers = Supplier::where('active','=',1)
        ->orderby('name','asc')
        ->get();

        $invoice_news=DB::SELECT('SELECT description AS description 
                                  FROM invoices_news
                                  WHERE active =?',['1']);
               
        
          return view('invoice.showfaccosto',[
            'modules' => $modules,
            'user' => $id_user,
            'invoice' => $invoice,
            'approvers' => $approvers,
            'costCenters' => $costCenters,
            'flow_id'=>$flow->id,
            'totalusers'=>$totalusers,
            'totalubications'=>$totalubications,
            'invoiceCCS'=>$invoiceCC,
            'invoiceCCAutorizations'=>$invoiceCCAutorizations,
            'role'=>$role[0]->role,
            'suppliers'=>$suppliers,
            'invoice_news'=>$invoice_news
          ]);
      
  }




  public function logfaccosto(Request $request){

    $user = Auth::user();
    $application = new Application();
    $modules = $application->getModules($user->id,4);
    $id_user = $request->id_user;
    $input = $request->all();
    $nombre='';
    $next_user_info='';
    $role=DB::SELECT('SELECT role_id AS role FROM invoice_approvers WHERE flow_id=? AND user_id = ?',[62,$user->id]);

    $description_user='';
    $fecha_actual = date("Y-m-d h:i:s");

    if ($input['description'] == NULL) {
      $description_user = 'Factura en proceso...';
    }else{
      $description_user = $input['description'];
    }

    if ($input['action'] == 'Validar' || $input['action'] == 'Aprobar') {
      $update_flow=DB::UPDATE('UPDATE invoices SET flow_id = ? WHERE id = ?',[62,$input['invoice_id']]);
    }

    if ($input['action'] == 'Sin ingreso') {
      $update_flow=DB::UPDATE('UPDATE invoices SET flow_id = ? WHERE id = ?',[63,$input['invoice_id']]);
    }


    if ($input['action'] == 'Sin ingreso') {
      $file = $request->file('file');
      if(isset($file))
      {
          $ext = $file->getClientOriginalExtension();
          $nombre = $input['invoice_id']."_".Str::random(11).".".$ext;
          \Storage::disk('facturas')->put($nombre,  \File::get($file));
      }
      $tamaño_nombre=strlen($nombre);
      if ($tamaño_nombre > 0) {
        $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at,file) VALUES (?,?,?,?,?,?,?,?,?)',[$input['invoice_id'],$user->id,3,$description_user,2198,'COMPRAS',$fecha_actual,$fecha_actual,$nombre]);
        
      }else{
        $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)',[$input['invoice_id'],$user->id,3,$description_user,2198,'COMPRAS',$fecha_actual,$fecha_actual]);
      }
    }  

    if ($input['action'] == 'Validar') {
      $file = $request->file('file');
      if(isset($file))
      {
          $ext = $file->getClientOriginalExtension();
          $nombre = $input['invoice_id']."_".Str::random(11).".".$ext;
          \Storage::disk('facturas')->put($nombre,  \File::get($file));
      }
      $tamaño_nombre=strlen($nombre);
      if ($tamaño_nombre > 0) {
        $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at,file) VALUES (?,?,?,?,?,?,?,?,?)',[$input['invoice_id'],$user->id,3,$description_user,$input['approver_id'],'COMPRAS',$fecha_actual,$fecha_actual,$nombre]);
        
      }else{
        $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)',[$input['invoice_id'],$user->id,3,$description_user,$input['approver_id'],'COMPRAS',$fecha_actual,$fecha_actual]);
      }
    }

    if ($input['action'] == 'Aprobar') {
      $file = $request->file('file');
      if(isset($file))
      {
          $ext = $file->getClientOriginalExtension();
          $nombre = $input['invoice_id']."_".Str::random(11).".".$ext;
          \Storage::disk('facturas')->put($nombre,  \File::get($file));
      }
      $tamaño_nombre=strlen($nombre);
      if ($tamaño_nombre > 0) {
        $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at,file) VALUES (?,?,?,?,?,?,?,?,?)',[$input['invoice_id'],$user->id,4,$description_user,$input['approver_id'],'COMPRAS',$fecha_actual,$fecha_actual,$nombre]);
      }else{
        $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)',[$input['invoice_id'],$user->id,4,$description_user,$input['approver_id'],'COMPRAS',$fecha_actual,$fecha_actual]);
      }
    }

    if ($input['action'] != 'Sin ingreso') {
      $next_user_info=DB::SELECT('SELECT email AS email,first_name AS name, ubication_name AS ubication FROM users WHERE id=?',[$input['approver_id']]);
    }else{
      $next_user_info=DB::SELECT('SELECT email AS email,first_name AS name, ubication_name AS ubication FROM users WHERE id=?',[2198]);
    }

   if($input['role_user'] == '2'){
    if ($input['novedad']) {
      $novedad=DB::UPDATE('UPDATE invoices SET tipo_novedad = ? WHERE id=?',[$input['novedad'],$input['invoice_id']]);
    }
  }


  if($input['role_user'] == '2'){
    if ($input['orden_compra']) {
      $novedad=DB::UPDATE('UPDATE invoices SET orden_compra = ? WHERE id=?',[$input['orden_compra'],$input['invoice_id']]);
    }
  }



    

    // $next_user_info=DB::SELECT('SELECT email AS email,first_name AS name, ubication_name AS ubication FROM users WHERE id=?',[$input['approver_id']]);
    //$invoice_number=Invoice::where('id','=',$input['invoice_id'])->get();
    $invoice = Invoice::find($input['invoice_id']);

   // if (($next_user_info[0]->ubication != 'CONTABILIDAD') || ($next_user_info[0]->ubication != 'COMPRAS')) {
   //   Mail::to($next_user_info[0]->email)->send(new NofiticationInvoiceMailCost($next_user_info[0]->name,$invoice,$input['approver_id']));
  //  }
   // Mail::to($next_user_info[0]->email)->send(new NofiticationInvoiceMailCost($next_user_info[0]->name,$invoice,$input['approver_id']));


    $invoices = DB::select('SELECT  L.id log_id,
                                    I.id invoice_id ,
                                    I.number,
                                    I.currency,
                                    I.subtotal,
                                    I.iva,
                                    I.total,
                                    I.priority,
                                    I.file AS file,
                                    I.revision AS revision,
                                    L.next_user_id,
                                    S.name supplier,
                                    DATE_FORMAT(I.created_at, "%Y-%m-%d") AS due_date,
                                    I.flow_id,
                                    L.description AS description,
                                  (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) LOG
                                  FROM invoices I
                                INNER JOIN invoice_logg L 
                                ON L.invoice_id = I.id AND L.id = (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) 
                                AND (next_user_id = 1968 OR next_user_id=1888) AND L.state_id <> 6
                                  INNER JOIN suppliers S ON S.id = I.supplier_id
                                ORDER BY DATE_FORMAT(I.created_at, "%Y-%m-%d") asc');
      $countInvoices=count($invoices);

      $invoices2 = DB::select('SELECT  L.id log_id,
      I.id invoice_id ,
      I.number,
      I.currency,
      I.subtotal,
      I.iva,
      I.total,
      I.priority,
      I.file AS file,
      L.next_user_id,
      I.revision AS revision,
      S.name supplier,
      DATE_FORMAT(I.created_at, "%Y-%m-%d") AS due_date,
      I.flow_id,
      L.description AS description,
    (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) LOG
    FROM invoices I
    INNER JOIN invoice_logg L 
    ON L.invoice_id = I.id AND L.id = (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) 
    AND next_user_id = ? AND L.state_id <> 6
    INNER JOIN suppliers S ON S.id = I.supplier_id
    WHERE I.flow_id=?
    ORDER BY DATE_FORMAT(I.created_at, "%Y-%m-%d") asc;',[$user->id,62]);

   $countInvoices2=count($invoices2);

   $suppliers = Supplier::where('active','=',1)
   ->orderby('name','asc')
   ->get();

   if ($role[0]->role == 1) {

      return view('invoice.faccosto', [
        'modules' => $modules,
        'user' => $user,
        'invoices' => $invoices,
        'countInvoices' =>$countInvoices,
        'role'=>$role[0]->role,
        'suppliers'=>$suppliers
      ]);
    }else{

      return view('invoice.faccosto', [
        'modules' => $modules,
        'user' => $user,
        'invoices' => $invoices2,
        'countInvoices' =>$countInvoices2,
        'role'=>$role[0]->role,
        'suppliers'=>$suppliers
      ]);

    }
}


public function fcompras(Request $request){

  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);
  $id_user = $request->id_user;


  $role=DB::SELECT('SELECT role_id AS role FROM invoice_approvers WHERE flow_id=? AND user_id = ?',[62,$user->id]);


  $invoices = DB::select('SELECT  L.id log_id,
                                  I.id invoice_id ,
                                  I.number,
                                  I.currency,
                                  I.subtotal,
                                  I.iva,
                                  I.total,
                                  I.priority,
                                  I.revision AS revision,
                                  I.file AS file,
                                  L.next_user_id,
                                  S.name supplier,
                                  DATE_FORMAT(I.created_at, "%Y-%m-%d") AS due_date,
                                  I.flow_id,
                                  L.description AS description,
                                (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) LOG
                                FROM invoices I
                                INNER JOIN invoice_logg L 
                                ON L.invoice_id = I.id AND L.id = (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) 
                                AND next_user_id = ? AND L.state_id <> 6
                                INNER JOIN suppliers S ON S.id = I.supplier_id
                                WHERE I.flow_id=?
                                ORDER BY DATE_FORMAT(I.created_at, "%Y-%m-%d") asc;',[$user->id,62]);
    $countInvoices=count($invoices);

    $suppliers = Supplier::select('suppliers.id', 'suppliers.nit', 'suppliers.name')
    ->where('active', 1)
    ->get();

    return view('invoice.faccosto', [
      'modules' => $modules,
      'user' => $user,
      'invoices' => $invoices,
      'countInvoices' =>$countInvoices,
      'role'=>$role[0]->role,
      'suppliers'=>$suppliers
    ]);  
}


public function invoicesdistributionsupdate(Request $request){
  $input = $request->all();
  $application = new Application();
  $modules = $application->getModules($input['user_on'],4);
  $user = User::where('id','=',$input['user_on']);

  $fecha_actual = date("Y-m-d h:i:s");
  
  $role=DB::SELECT('SELECT role_id AS role FROM invoice_approvers WHERE flow_id=? AND user_id = ?',[62,$input['user_on']]);


  $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)',[$input['id_invoice'],$input['user_on'],3,'Factura en proceso...',$input['id_usuario'],'',$fecha_actual,$fecha_actual]);

  $invoices = DB::select('SELECT  L.id log_id,
                                  I.id invoice_id ,
                                  I.number,
                                  I.currency,
                                  I.subtotal,
                                  I.iva,
                                  I.total,
                                  I.priority,
                                  I.file AS file,
                                  I.revision AS revision,
                                  L.next_user_id,
                                  S.name supplier,
                                  DATE_FORMAT(I.created_at, "%Y-%m-%d") AS due_date,
                                  I.flow_id,
                                  L.description AS description,
                                (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) LOG
                                FROM invoices I
                                INNER JOIN invoice_logg L 
                                ON L.invoice_id = I.id AND L.id = (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) 
                                AND (next_user_id = 1968 OR next_user_id=1888) AND L.state_id <> 6
                                INNER JOIN suppliers S ON S.id = I.supplier_id
                                ORDER BY DATE_FORMAT(I.created_at, "%Y-%m-%d") asc');
  $countInvoices=count($invoices);

  $suppliers = Supplier::where('active','=',1)
  ->orderby('name','asc')
  ->get();


  return view('invoice.faccosto', [
    'modules' => $modules,
    'user' => $user,
    'invoices' => $invoices,
    'countInvoices' =>$countInvoices,
    'role'=>$role[0]->role,
    'suppliers'=>$suppliers
  ]);
  
}


public function invoicesfinderunassigned(Request $request){

  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);

  $id_proveedor=$request->supplier_nit;
  $id_factura=$request->invoice;

  if ($id_proveedor != '0') {
        $invoices=DB::SELECT('SELECT i.id AS id, 
        i.number AS number,
        s.name AS supplier,
        i.create_date AS fecha_creacion,
        i.due_date AS due_date,
        i.subtotal AS subtotal,
        i.iva AS iva,
        i.total AS total,
        i.concept AS concept,
        i.file AS file
    FROM invoices i
    INNER JOIN suppliers s
    ON s.id=i.supplier_id
    WHERE i.flow_id=? AND i.supplier_id = ?',[60,$id_proveedor]);
  }elseif($id_factura != '0'){
            $invoices=DB::SELECT('SELECT i.id AS id, 
            i.number AS number,
            s.name AS supplier,
            i.create_date AS fecha_creacion,
            i.due_date AS due_date,
            i.subtotal AS subtotal,
            i.iva AS iva,
            i.total AS total,
            i.concept AS concept,
            i.file AS file
        FROM invoices i
        INNER JOIN suppliers s
        ON s.id=i.supplier_id
        WHERE i.flow_id=? AND i.id = ?',[60,$id_factura]);    
  }else{
    $invoices=DB::SELECT('SELECT i.id AS id, 
                          i.number AS number,
                          s.name AS supplier,
                          i.create_date AS fecha_creacion,
                          i.due_date AS due_date,
                          i.subtotal AS subtotal,
                          i.iva AS iva,
                          i.total AS total,
                          i.concept AS concept,
                          i.file AS file
                      FROM invoices i
                      INNER JOIN suppliers s
                      ON s.id=i.supplier_id
                      WHERE i.flow_id=? AND i.supplier_id',[60]);
  }


  $countInvoices=count($invoices);
  $suppliers = Supplier::where('active','=',1)
  ->orderby('name','asc')
  ->get();

return view('invoice.unassignedfiles', [
'modules' => $modules,
'user' => $user,
'invoices' => $invoices,
'countInvoices' =>$countInvoices,
'suppliers'=>$suppliers
]); 

}




public function invoicesdistributionsupdategestion(Request $request){
  $input = $request->all();
  $application = new Application();
  $modules = $application->getModules($input['user_on'],4);
  $user = User::where('id','=',$input['user_on']);

  $fecha_actual = date("Y-m-d h:i:s");
  
  $role=DB::SELECT('SELECT role_id AS role FROM invoice_approvers WHERE flow_id=? AND user_id = ?',[62,$input['user_on']]);


  $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)',[$input['id_invoice'],$input['user_on'],3,'Factura en proceso...',$input['id_usuario'],'',$fecha_actual,$fecha_actual]);

  $modules = $application->getModules($user->id,4);


  $invoice = new Invoice();
  $invoices = $invoice->getActives(Auth::id());
  $countInvoices = count($invoices);


  return view('invoice.index',['modules' => $modules,'user' => $user,'invoices' => $invoices,'countInvoices' => $countInvoices]);
  
}


public function invoicesrevision(Request $request){
  $input = $request->all();

  $insert_log=DB::UPDATE('UPDATE invoices SET revision = ? WHERE id=?',[$input['estado'],$input['factura']]);
 
  echo json_encode(1);




  
}


public function invoicesfindercosto(Request $request){
  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);

  $id_proveedor=$request->supplier_nit;
  $id_factura=$request->invoice;

  if ($id_proveedor != '0') {
    $invoices=DB::SELECT('SELECT  L.id log_id,
                                  I.id invoice_id ,
                                  I.number,
                                  I.currency,
                                  I.subtotal,
                                  I.iva,
                                  I.total,
                                  I.priority,
                                  I.file AS file,
                                  I.revision AS revision,
                                  L.next_user_id,
                                  S.name supplier,
                                  DATE_FORMAT(I.created_at, "%Y-%m-%d") AS due_date,
                                  I.flow_id,
                                  L.description AS description,
                                (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) LOG
                                FROM invoices I
                              INNER JOIN invoice_logg L 
                              ON L.invoice_id = I.id AND L.id = (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) 
                              AND (next_user_id = ?) AND L.state_id <> 6
                                INNER JOIN suppliers S ON S.id = I.supplier_id
                              WHERE I.flow_id=? AND I.supplier_id=?
                              ORDER BY DATE_FORMAT(I.created_at, "%Y-%m-%d") ASC',[$user->id,62,$id_proveedor]); 
  }elseif($id_factura != '0'){
            $invoices=DB::SELECT('SELECT  L.id log_id,
            I.id invoice_id ,
            I.number,
            I.currency,
            I.subtotal,
            I.iva,
            I.total,
            I.priority,
            I.file AS file,
            I.revision AS revision,
            L.next_user_id,
            S.name supplier,
            DATE_FORMAT(I.created_at, "%Y-%m-%d") AS due_date,
            I.flow_id,
            L.description AS description,
          (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) LOG
          FROM invoices I
        INNER JOIN invoice_logg L 
        ON L.invoice_id = I.id AND L.id = (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) 
        AND (next_user_id = ?) AND L.state_id <> 6
          INNER JOIN suppliers S ON S.id = I.supplier_id
        WHERE I.flow_id=? AND I.id=?
        ORDER BY DATE_FORMAT(I.created_at, "%Y-%m-%d") ASC',[$user->id,62,$id_factura]);    
  }else{
    $invoices=DB::SELECT('SELECT  L.id log_id,
                                  I.id invoice_id ,
                                  I.number,
                                  I.currency,
                                  I.subtotal,
                                  I.iva,
                                  I.total,
                                  I.priority,
                                  I.file AS file,
                                  I.revision AS revision,
                                  L.next_user_id,
                                  S.name supplier,
                                  DATE_FORMAT(I.created_at, "%Y-%m-%d") AS due_date,
                                  I.flow_id,
                                  L.description AS description,
                                (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) LOG
                                FROM invoices I
                              INNER JOIN invoice_logg L 
                              ON L.invoice_id = I.id AND L.id = (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) 
                              AND (next_user_id = ?) AND L.state_id <> 6
                                INNER JOIN suppliers S ON S.id = I.supplier_id
                              ORDER BY DATE_FORMAT(I.created_at, "%Y-%m-%d") ASC',[$user->id]); 
  }

  $role=DB::SELECT('SELECT role_id AS role FROM invoice_approvers WHERE flow_id=? AND user_id = ?',[62,$user->id]);

  $countInvoices=count($invoices);
  $suppliers = Supplier::where('active','=',1)
  ->orderby('name','asc')
  ->get();

  return view('invoice.faccosto', [
    'modules' => $modules,
    'user' => $user,
    'invoices' => $invoices,
    'countInvoices' =>$countInvoices,
    'role'=>$role[0]->role,
    'suppliers'=>$suppliers
  ]);



}

public function siningreso(Request $request){
  $user = Auth::user();
  $application = new Application();
  $modules = $application->getModules($user->id,4);


  $invoices=DB::SELECT('SELECT  L.id log_id,
                                I.id invoice_id ,
                                I.number,
                                I.currency,
                                I.subtotal,
                                I.iva,
                                I.total,
                                I.priority,
                                I.file AS file,
                                I.revision AS revision,
                                L.next_user_id,
                                S.name supplier,
                                DATE_FORMAT(I.created_at, "%Y-%m-%d") AS due_date,
                                I.flow_id,
                                L.description AS description,
                              (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) LOG
                              FROM invoices I
                              INNER JOIN invoice_logg L 
                              ON L.invoice_id = I.id AND L.id = (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) 
                              AND L.state_id <> 6
                              INNER JOIN suppliers S ON S.id = I.supplier_id
                              WHERE I.flow_id=?
                              ORDER BY DATE_FORMAT(I.created_at, "%Y-%m-%d") ASC',[63]); 

    $role=DB::SELECT('SELECT role_id AS role FROM invoice_approvers WHERE flow_id=? AND user_id = ?',[62,$user->id]);

    $countInvoices=count($invoices);
    $suppliers = Supplier::where('active','=',1)
    ->orderby('name','asc')
    ->get();
  
    return view('invoice.faccostosiningreso', [
      'modules' => $modules,
      'user' => $user,
      'invoices' => $invoices,
      'countInvoices' =>$countInvoices,
      'role'=>$role[0]->role,
      'suppliers'=>$suppliers
    ]);






}



public function envioEstadoCadenaCompras(Request $request){

  $id_user = $request->id_user;
  $user = User::where('id','=',$id_user);
  $application = new Application();
  $modules = $application->getModules($id_user,4);
  $nombre='';
  $next_user_info='';
  $role=DB::SELECT('SELECT role_id AS role FROM invoice_approvers WHERE flow_id=? AND user_id = ?',[62,$id_user]);

  $description_user='';
  $fecha_actual = date("Y-m-d h:i:s");

  if ($request->note == NULL) {
    $description_user = 'Factura en proceso...';
  }else{
    $description_user = $request->note;
  }

  if ($request->estado == 'Validar' || $request->estado == 'Aprobar') {
    $update_flow=DB::UPDATE('UPDATE invoices SET flow_id = ? WHERE id = ?',[62,$request->id_factura]);
  }

  if ($request->estado == 'Validar' || $request->estado == 'Sin ingreso') {
    $update_flow=DB::UPDATE('UPDATE invoices SET flow_id = ? WHERE id = ?',[63,$request->id_factura]);
  }


  if ($request->estado == 'Sin ingreso') {
    $file = $request->file('file');
    if(isset($file))
    {
        $ext = $file->getClientOriginalExtension();
        $nombre = $request->id_factura."_".Str::random(11).".".$ext;
        \Storage::disk('facturas')->put($nombre,  \File::get($file));
    }
    $tamaño_nombre=strlen($nombre);
    if ($tamaño_nombre > 0) {
      $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at,file) VALUES (?,?,?,?,?,?,?,?,?)',[$request->id_factura,$user->id,3,$description_user,2198,'COMPRAS',$fecha_actual,$fecha_actual,$nombre]);
      
    }else{
      $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)',[$request->id_factura,$user->id,3,$description_user,2198,'COMPRAS',$fecha_actual,$fecha_actual]);
    }
  }  

  if ($request->estado == 'Validar') {
    $file = $request->file('file');
    if(isset($file))
    {
        $ext = $file->getClientOriginalExtension();
        $nombre = $input['invoice_id']."_".Str::random(11).".".$ext;
        \Storage::disk('facturas')->put($nombre,  \File::get($file));
    }
    $tamaño_nombre=strlen($nombre);
    if ($tamaño_nombre > 0) {
      $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at,file) VALUES (?,?,?,?,?,?,?,?,?)',[$request->id_factura,$user->id,3,$description_user,$request->approber_id,'COMPRAS',$fecha_actual,$fecha_actual,$nombre]);
      
    }else{
      $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)',[$request->id_factura,$user->id,3,$description_user,$request->approber_id,'COMPRAS',$fecha_actual,$fecha_actual]);
    }
  }

  if ($request->estado == 'Aprobar') {
    $file = $request->file('file');
    if(isset($file))
    {
        $ext = $file->getClientOriginalExtension();
        $nombre = $input['invoice_id']."_".Str::random(11).".".$ext;
        \Storage::disk('facturas')->put($nombre,  \File::get($file));
    }
    $tamaño_nombre=strlen($nombre);
    if ($tamaño_nombre > 0) {
      $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at,file) VALUES (?,?,?,?,?,?,?,?,?)',[$request->id_factura,$id_user,4,$description_user,$request->approber_id,'COMPRAS',$fecha_actual,$fecha_actual,$nombre]);
    }else{
      $insert_log=DB::INSERT('INSERT INTO  invoice_logg (invoice_id,user_id,state_id,description,next_user_id,autorizacion,created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)',[$request->id_factura,$id_user,4,$description_user,$request->approber_id,'COMPRAS',$fecha_actual,$fecha_actual]);
    }
  }

  if ($request->estado != 'Sin ingreso') {
    $next_user_info=DB::SELECT('SELECT email AS email,first_name AS name, ubication_name AS ubication FROM users WHERE id=?',[$request->approber_id]);
  }else{
    $next_user_info=DB::SELECT('SELECT email AS email,first_name AS name, ubication_name AS ubication FROM users WHERE id=?',[2198]);
  }

  $invoice = Invoice::find($request->id_factura);

  $invoices = DB::select('SELECT  L.id log_id,
                                  I.id invoice_id ,
                                  I.number,
                                  I.currency,
                                  I.subtotal,
                                  I.iva,
                                  I.total,
                                  I.priority,
                                  I.file AS file,
                                  I.revision AS revision,
                                  L.next_user_id,
                                  S.name supplier,
                                  DATE_FORMAT(I.created_at, "%Y-%m-%d") AS due_date,
                                  I.flow_id,
                                  L.description AS description,
                                (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) LOG
                                FROM invoices I
                              INNER JOIN invoice_logg L 
                              ON L.invoice_id = I.id AND L.id = (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) 
                              AND (next_user_id = 1968 OR next_user_id=1888) AND L.state_id <> 6
                                INNER JOIN suppliers S ON S.id = I.supplier_id
                              ORDER BY DATE_FORMAT(I.created_at, "%Y-%m-%d") asc');
    $countInvoices=count($invoices);

    $invoices2 = DB::select('SELECT  L.id log_id,
    I.id invoice_id ,
    I.number,
    I.currency,
    I.subtotal,
    I.iva,
    I.total,
    I.priority,
    I.file AS file,
    L.next_user_id,
    I.revision AS revision,
    S.name supplier,
    DATE_FORMAT(I.created_at, "%Y-%m-%d") AS due_date,
    I.flow_id,
    L.description AS description,
  (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) LOG
  FROM invoices I
  INNER JOIN invoice_logg L 
  ON L.invoice_id = I.id AND L.id = (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) 
  AND next_user_id = ? AND L.state_id <> 6
  INNER JOIN suppliers S ON S.id = I.supplier_id
  WHERE I.flow_id=?
  ORDER BY DATE_FORMAT(I.created_at, "%Y-%m-%d") asc;',[$id_user,62]);

 $countInvoices2=count($invoices2);

 $suppliers = Supplier::where('active','=',1)
 ->orderby('name','asc')
 ->get();

/////////////////////////////De aqui para abajo es el proceso de Radian//////////////////////////////////////
      $date = date ( 'Y-m-d' );
      $timestamp = strtotime($date);
  
      $datos_radian=DB::SELECT('SELECT  s.nit AS supplierId,
                                          s.document_type AS document_type,
                                          c.nit AS receiverId,
                                          "01" AS documentTypeCode,
                                          i.number AS documentId,
                                          u.name AS username,
                                          "032" AS statusCode,
                                          "" AS statusReason,
                                          "" AS statusNote,
                                          u.cedula AS id,
                                          "" AS idDv,
                                          "13" AS idType,
                                          u.first_name AS firstName,
                                          u.last_name AS familyName,
                                          u.profile_name AS jobTitle,
                                          u.ubication_name AS OrganizationDepartment
                                        FROM invoices i
                                        INNER JOIN suppliers s
                                        ON s.id=i.supplier_id
                                        INNER JOIN companies c
                                        ON c.id=i.company
                                        INNER JOIN users u
                                        ON u.id=?
                                        WHERE i.id=?',[$request->id_user,$request->id_factura]);



  
//

 if(($request->estado =="Aprobar") && ($request->approber_id == '2101')){

    $myString = $datos_radian[0]->supplierId;


    $myString = substr($datos_radian[0]->supplierId, 0, -1);

    $array_datos_cadena['statusCode'] = $datos_radian[0]->statusCode;
    $array_datos_cadena['statusDate'] = strval($timestamp);
    $array_datos_cadena['statusReason'] = $datos_radian[0]->statusReason;
    $array_datos_cadena['statusNote'] = $request->note;
    $array_datos_cadena['id'] = $datos_radian[0]->id;
    $array_datos_cadena['idDv'] = $datos_radian[0]->idDv;
    $array_datos_cadena['idType'] = $datos_radian[0]->idType;
    $array_datos_cadena['firstName'] = $datos_radian[0]->firstName;
    $array_datos_cadena['familyName'] = $datos_radian[0]->familyName;
    $array_datos_cadena['jobTitle'] = $datos_radian[0]->jobTitle;
    $array_datos_cadena['OrganizationDepartment'] = $datos_radian[0]->OrganizationDepartment;               

    $array_cadena['supplierId'] = $myString;
    $array_cadena['receiverId'] = $datos_radian[0]->receiverId;
    $array_cadena['documentTypeCode'] = $datos_radian[0]->documentTypeCode;
    $array_cadena['documentId'] = $datos_radian[0]->documentId;
    $array_cadena['username'] = $datos_radian[0]->username;
    $array_cadena['documentStatus'] = $array_datos_cadena;

   

    $datos=json_encode($array_cadena);
   // echo($datos);

    $url = "https://apivp.efacturacadena.com/v1/recepcion/estados";
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $datos,
    CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'efacturaAuthorizationToken: 9f9b4217-81d6-41df-9308-8ebed0235dc7',
    ),
    CURLOPT_RETURNTRANSFER => true,
    ));
    $resultado = curl_exec($ch);
    $json_data=json_decode($resultado, true);

    if ($json_data['statusCode'] == 200) {
    $actualizacion=DB::UPDATE('UPDATE invoices SET radian_state = ? ,cude = ? WHERE id=?',[$json_data['eventCode'],$json_data['cude'],$request->id_factura]);
    }

    
    echo json_encode($resultado);



    }
 }
}
