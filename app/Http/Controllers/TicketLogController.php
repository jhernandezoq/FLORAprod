<?php

namespace App\Http\Controllers;

use App\TicketLog;
use App\Ticket;
use App\Application;
use App\User;
use App\Tool;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TicketLogController extends Controller
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
        $user = Auth::user();
        if ($user) {
        $application = new Application();
        $modules = $application->getModules($user->id,3);

        $tl = new TicketLog();

        $tl->ticket_id = $request->ticket_id;
        $tl->user_id = $user->id;
        $tl->text = $request->text;
        $tl->status_id = 3;
        $tl->active = 1;
        $tl->save();


        $tickets = Ticket::where('agent_id','=',$user->id)
                    ->orderby('status_id','asc')
                    ->orderby('created_at','desc')
                    ->get();
        $states= DB::SELECT('SELECT id AS id,
                                    name AS name
                              FROM ticket_states');
        $tickets = Ticket::where('agent_id','=',$user->id)
                    ->orderby('status_id','asc')
                    ->orderby('created_at','desc')
                    ->get();
        $mail = DB::SELECT('SELECT u.email AS mail
                            FROM users u
                            INNER JOIN tickets t
                            WHERE t.user_id=u.id AND
                            u.id=?',[$user->id]);

        $count=count($tickets);

        $Type = 'actualizacion';
        $CaseNumber =$request->ticket_id;
        $reasignado=$user->name;
        $MailSend= $mail[0]->mail;

        //$request->session()->put('assignmentuser', $assignmentuser);
        
        $data=[$reasignado,$Type,$CaseNumber,$reasignado];


        Mail::to($MailSend)->send(new SendMail($data));

        return view('ticket.pending',['modules' => $modules,'user' => $user,'tickets' => $tickets,'states'=>$states,'count'=>$count]);
        }else{

        $agent=DB::SELECT('SELECT agent_id as agent_id FROM tickets
                           WHERE id=?',[$request->ticket_id]);

        $application = new Application();
        $modules = $application->getModules($agent[0]->agent_id,3);

        $tl = new TicketLog();

        $tl->ticket_id = $request->ticket_id;
        $tl->user_id = $agent[0]->agent_id;
        $tl->text = $request->text;
        $tl->status_id = 3;
        $tl->active = 1;
        $tl->save();


        $tickets = Ticket::where('agent_id','=',$agent[0]->agent_id)
                    ->orderby('status_id','asc')
                    ->orderby('created_at','desc')
                    ->get();
        $states= DB::SELECT('SELECT id AS id,
                                    name AS name
                              FROM ticket_states');
        $tickets = Ticket::where('agent_id','=',$agent[0]->agent_id)
                    ->orderby('status_id','asc')
                    ->orderby('created_at','desc')
                    ->get();

        $count=count($tickets);

        return view('welcome');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for pre-editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function preedit(Request $request)
    {
           $user = Auth::user();

           if ($user) {
           $assignments = User::where('ubication_name','=','SISTEMAS')
                                ->where('active','=','1')
                               ->get();        
            $ticket_id=$request->ticket_id;
            $application = new Application();
            $modules = $application->getModules($user->id,3);

            $states= DB::SELECT('SELECT id AS id,
                                    name AS name
                              FROM ticket_states');

            $state_mail=0;

            $tools = Tool::all()->where('active','=',1);
            return view('ticket.edit',['modules' => $modules,'user' => $user,'tools' => $tools,'ticket_id'=>$ticket_id,'assignments'=>$assignments,'states'=>$states,'state_mail'=>$state_mail]);
           }else{
           $assignments = User::where('ubication_name','=','SISTEMAS')
                                ->where('active','=','1')
                               ->get();     
           $agent=DB::SELECT('SELECT agent_id as agent_id FROM tickets
                           WHERE id=?',[$request->ticket_id]);
           $users= DB::SELECT('SELECT id AS id FROM users WHERE id=?',[$agent[0]->agent_id]);

            $user= $users[0]->id;
            $state_mail=1;
            $application = new Application();
            $modules = $application->getModules($agent[0]->agent_id,3);
            $ticket_id=$request->ticket_id;

            $states= DB::SELECT('SELECT id AS id,
                                    name AS name
                              FROM ticket_states');

            $tools = Tool::all()->where('active','=',1);
            return view('ticket.edit',['modules' => $modules,'user' => $user,'tools' => $tools,'ticket_id'=>$ticket_id,'assignments'=>$assignments,'states'=>$states,'state_mail'=>$state_mail]);            
           }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $input=request()->except('_token');

        if ($input['agent_id'] == NULL) {
        $assignments['assignmentsdata']=Tool::where('id','=',$input['tool_id'])->inRandomOrder()->first();
        $User=User::where('id','=',$assignments['assignmentsdata']->owner_id)->first();
        
        $ticketupdate = Ticket::where('id', '=', $input['ticket_id'])->first();
        $ticketupdate->tool_id = $input['tool_id'];
        $ticketupdate->agent_id = $assignments['assignmentsdata']->owner_id;
        $ticketupdate->save();

        $tl = new TicketLog();
        $tl->ticket_id = $input['ticket_id'];
        $tl->user_id = $assignments['assignmentsdata']->owner_id;
        $tl->text = $input['text'];
        $tl->status_id = 3;

        $file = $request->file('file');
        if(isset($file))
        {
            
            $ext = $file->getClientOriginalExtension();
            $nombre =  $request->user_id."_".Str::random(16).".".$ext;
            $tl->file = $nombre;
            \Storage::disk('tickets')->put($nombre,  \File::get($file));
        }
        $tl->active = 1;
        $tl->save();

        $tl = new TicketLog();
        $tl->ticket_id = $input['ticket_id'];
        $tl->user_id = $assignments['assignmentsdata']->owner_id;
        $tl->text = "El caso fue Asigando a {$User->name}";
        $tl->status_id = 3;
        $tl->active = 1;
        $tl->save();

        $user = Auth::user();
        $application = new Application();

        if ($user) {
        $modules = $application->getModules($user->id,3);

        $tickets = Ticket::where('agent_id','=',$user->id)
                    ->orderby('status_id','asc')
                    ->orderby('created_at','desc')
                    ->get();
        $count=count($tickets);
        $states= DB::SELECT('SELECT id AS id,
                                    name AS name
                              FROM ticket_states');

        //$assignmentuser = $tool[0]->user->first_name;
        $Type = 'reasignacion';
        $CaseNumber =$input['ticket_id'];
        $reasignado=$User->name;
        $MailSend= $User->email;

        $request->session()->put('assignmentuser', $assignmentuser);
        
        $data=[$reasignado,$Type,$CaseNumber,$reasignado];


        Mail::to($MailSend)->send(new SendMail($data));


        return view('ticket.pending',['modules' => $modules,'user' => $user,'tickets' => $tickets,'states'=>$states,'count'=>$count]);
        }else{
        return view('welcome');
        }
        }else{
        $User=User::where('id','=',$input['agent_id'])->first();
        $ticketupdate = Ticket::where('id', '=', $input['ticket_id'])->first();
        $ticketupdate->tool_id = $input['tool_id'];
        $ticketupdate->agent_id = $input['agent_id'];
        $ticketupdate->save();

        $tl = new TicketLog();
        $tl->ticket_id = $input['ticket_id'];
        $tl->user_id = $input['agent_id'];
        $tl->text = $input['text'];
        $tl->status_id = 3;
        $file = $request->file('file');
        if(isset($file))
        {
            
            $ext = $file->getClientOriginalExtension();
            $nombre =  $request->user_id."_".Str::random(16).".".$ext;
            $tl->file = $nombre;
            \Storage::disk('tickets')->put($nombre,  \File::get($file));
        }
        $tl->active = 1;
        $tl->save();

        $tl = new TicketLog();
        $tl->ticket_id = $input['ticket_id'];
        $tl->user_id = $input['agent_id'];
        $tl->text = "El caso fue Asigando a {$User->name}";
        $tl->status_id = 3;
        $tl->active = 1;
        $tl->save();

        $user = Auth::user();
        $application = new Application();

        if ($user) {
        $modules = $application->getModules($user->id,3);

        $tickets = Ticket::where('agent_id','=',$user->id)
                    ->orderby('status_id','asc')
                    ->orderby('created_at','desc')
                    ->get();
        $count=count($tickets);
        $states= DB::SELECT('SELECT id AS id,
                                    name AS name
                              FROM ticket_states');
        $Type = 'reasignacion';
        $CaseNumber =$input['ticket_id'];
        $reasignado=$User->name;
        $MailSend= $User->email;

       // $request->session()->put('assignmentuser', $assignmentuser);
        
        $data=[$reasignado,$Type,$CaseNumber,$reasignado];


        Mail::to($MailSend)->send(new SendMail($data));

        return view('ticket.pending',['modules' => $modules,'user' => $user,'tickets' => $tickets,'states'=>$states,'count'=>$count]);
        }else{
        return view('welcome');
        }
        }
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
        //
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
}
