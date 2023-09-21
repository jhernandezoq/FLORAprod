<?php

namespace App\Http\Controllers;

use App\Mail\NofiticationInvoiceMail;
use App\Log;
use App\Distribution;
use App\Application;
use App\Invoice;
use App\Flow;
use App\Supplier;
use App\Company;
use App\Approver;
use App\CostCenter;
use App\DistributionCC;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function store(Request $request)
    {
        $array_cadena=array();
        $array_datos_cadena=array();
        $input = $request->all();

        $flow_id=DB::SELECT('SELECT flow_id AS flow_id FROM invoices WHERE id=?',[$input['invoice_id']]);

    if ($flow_id[0]->flow_id != 60) {

        $flow_id=DB::SELECT('SELECT flow_id AS flow_id FROM invoices WHERE id=?',[$input['invoice_id']]);
        $level_user_flow=DB::SELECT('SELECT invoice_approvers.order AS orden FROM invoice_approvers
                                     WHERE user_id = ? AND flow_id=? LIMIT 1',[Auth::id(),$flow_id[0]->flow_id]);
       // var_dump($level_user_flow[0]->orden);



        $next_user=DB::SELECT('SELECT next_user_id AS user_id,
                                      user_id AS actual_user
                               FROM invoice_logg
                               WHERE invoice_id=? AND 
                               id = (SELECT MAX(id) FROM invoice_logg WHERE invoice_id=?)',[$request->invoice_id,$request->invoice_id]);

        $level_next_user_flow=DB::SELECT('SELECT invoice_approvers.order AS orden FROM invoice_approvers
                                     WHERE user_id = ? AND flow_id=? LIMIT 1',[$input['approver_id'],$flow_id[0]->flow_id]);
      
        $user=Auth::id();
        $log = new Log();

        $log->invoice_id = $input['invoice_id'];
            if ($user) {
            $log->user_id = Auth::id();
            }else{
            $log->user_id = $next_user[0]->user_id;
            }
            if ($input['description'] != null) {
                $log->description = $input['description'];
            }else{
                $log->description = 'Factura en proceso...';
            }


        

        if($input['action'] =="Validar")
        {
            $log->state_id = 3;

            if ($input['autorization_user'] ) {
                $log->autorizacion = $input['autorization_user'];
            }


        }
        else
        {
            if($input['action'] =="Aprobar")
            {
                $log->state_id = 4;

            }
            else
            {
                if($input['action'] =="Finalizar")
                {
                    $log->state_id = 6;

                }
                else
                {
                    $log->state_id = 5;
                }
            }
        }

        if($input['role_id'] == 1)
        {

            Distribution::where('invoice_id',$input['invoice_id'])
            ->where('active',1)
            ->update(['active' => 0]);
             
             $cantidad=intval($input['countfields']);
             $cantidadCC=intval($input['countfieldsCC']);
             for ($i=1; $i <=$cantidad ; $i++) { 
                        $distribution = new Distribution();
                        $distribution->invoice_id = $input['invoice_id'];
                        $distribution->cost_center_id = $input['coce'.$i];
                        $distribution->percentage = $input['percenta'.$i];
                        $distribution->value = str_replace('.','',$input['value'.$i]);
                        $distribution->active = 1;
                        $distribution->save();
             }

             for ($k=1; $k <=$cantidadCC ; $k++) { 
                        $distributionCC = new DistributionCC();
                        $distributionCC->invoice_id = $input['invoice_id'];
                       // $distributionCC->cost_center_id = $input['ubication'.$k];
                        $distributionCC->save();
             }
        }

        $file = $request->file('file');
        if(isset($file))
        {
            $ext = $file->getClientOriginalExtension();
            $nombre = $input['invoice_id']."_".Str::random(11).".".$ext;
            $log->file = $nombre;
            \Storage::disk('facturas')->put($nombre,  \File::get($file));
        }


        $log->next_user_id = $input['approver_id'];
        $log->save();
        if ($input['egreso'] != 'N/A') {
            $egreso= DB::UPDATE('UPDATE invoices
                                 SET egress=?
                                 WHERE id=?',[$input['egreso'],$input['invoice_id']]);
        }

        $invoice = Invoice::find($input['invoice_id']);

        $log = Log::where('invoice_id','=',$input['invoice_id'])
                    ->orderby('created_at','desc')
                    ->first();  

        $next_user_data=DB::SELECT('SELECT first_name AS name,
                                    email AS email
                                    FROM users
                                    WHERE id=?',[$input['approver_id']]);
                                    
        $date = date ( 'Y-m-d' );
        $timestamp = strtotime($date);
                    
        $user = $log->next_user;
        $user_actual = $next_user[0]->actual_user;
        
        Mail::to($next_user_data[0]->email)->send(new NofiticationInvoiceMail($next_user_data[0]->name,$invoice,$input['approver_id']));

        
        $datos_radian=DB::SELECT('SELECT    s.nit AS supplierId,
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
                                    WHERE i.id=?',[$user->id,$input['invoice_id']]);

            $application = new Application();

            $invoice = new Invoice();
            $invoices = $invoice->getActives(Auth::id());
            $countInvoices = count($invoices);


            $modules = $application->getModules($user_actual,4);
            return redirect()->route('invoices');   


    }else{


        if($input['action'] != "Retornar"){
             


       $update_invoice=DB::UPDATE("UPDATE invoices
                                   SET flow_id=?
                                   WHERE id = ?",[$input['flow_id'],$input['invoice_id']]);


        $flow_id=DB::SELECT('SELECT flow_id AS flow_id FROM invoices WHERE id=?',[$input['invoice_id']]);
        $level_user_flow=DB::SELECT('SELECT invoice_approvers.order AS orden FROM invoice_approvers
                                     WHERE user_id = ? AND flow_id=? LIMIT 1',[Auth::id(),$flow_id[0]->flow_id]);



        $next_user=DB::SELECT('SELECT next_user_id AS user_id
                               FROM invoice_logg
                               WHERE invoice_id=? AND 
                               id = (SELECT MAX(id) FROM invoice_logg WHERE invoice_id=?)',[$request->invoice_id,$request->invoice_id]);

        $level_next_user_flow=DB::SELECT('SELECT invoice_approvers.order AS orden FROM invoice_approvers
                                     WHERE user_id = ? AND flow_id=? LIMIT 1',[$input['approver_id'],$flow_id[0]->flow_id]);
      
        $user=Auth::id();
        $log = new Log();
        $log->invoice_id = $input['invoice_id'];
        if ($user) {
          $log->user_id = Auth::id();
        }else{
          $log->user_id = $next_user[0]->user_id;
        }
        if ($input['description'] != null) {
            $log->description = $input['description'];
        }else{
            $log->description = 'Factura en proceso...';
        }

    
        if($input['action'] =="Validar")
        {
            $log->state_id = 3;

            if ($input['autorization_user'] ) {
                $log->autorizacion = $input['autorization_user'];
            }

        }
        else
        {
            if($input['action'] =="Aprobar")
            {
                $log->state_id = 4;

            }
            else
            {
                if($input['action'] =="Finalizar")
                {
                    $log->state_id = 6;

                }
                else
                {
                    $log->state_id = 5;
                }
            }
        }


        if($input['role_id'] == 1)
        {

            Distribution::where('invoice_id',$input['invoice_id'])
            ->where('active',1)
            ->update(['active' => 0]);
             
             $cantidad=intval($input['countfields']);
             for ($i=1; $i <=$cantidad ; $i++) { 
                        $distribution = new Distribution();
                        $distribution->invoice_id = $input['invoice_id'];
                        $distribution->cost_center_id = $input['coce'.$i];
                        $distribution->percentage = $input['percenta'.$i];
                        $distribution->value = str_replace('.','',$input['value'.$i]);
                        $distribution->active = 1;
                        $distribution->save();
             }
        }

        $file = $request->file('file');
        if(isset($file))
        {
            $ext = $file->getClientOriginalExtension();
            $nombre = $input['invoice_id']."_".Str::random(11).".".$ext;
            $log->file = $nombre;
            \Storage::disk('facturas')->put($nombre,  \File::get($file));
        }


        $log->next_user_id = $input['approver_id'];
        $log->save();
        if ($input['egreso'] != 'N/A') {
            $egreso= DB::UPDATE('UPDATE invoices
                                 SET egress=?
                                 WHERE id=?',[$input['egreso'],$input['invoice_id']]);
        }

        $invoice = Invoice::find($input['invoice_id']);

        $log = Log::where('invoice_id','=',$input['invoice_id'])
                    ->orderby('created_at','desc')
                    ->first();  

        $next_user_data=DB::SELECT('SELECT first_name AS name,
                                    email AS email
                                    FROM users
                                    WHERE id=?',[$input['approver_id']]);           
                    
        $user = $log->next_user;
           
        Mail::to($next_user_data[0]->email)->send(new NofiticationInvoiceMail($next_user_data[0]->name,$invoice,$input['approver_id']));


  


        return redirect()->route('invoices');

    }else{
        $user_invoice_final=$input['approver_id'];
        $returninvoice=DB::INSERT('INSERT INTO invoice_logg(invoice_id,user_id,state_id,description,next_user_id) VALUES(?,?,?,?,?)',[$input['invoice_id'],Auth::id(),1,'Factura en proceso...',$user_invoice_final]);
        return redirect()->route('invoices');
    }
  }
    
 }
}
