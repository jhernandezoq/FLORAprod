<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\Tool;
use App\TicketLog;
use App\Application;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,3);

        $tickets = Ticket::where('user_id','=',$user->id)
                ->orderby('status_id','asc')
                ->orderby('created_at','desc')
                ->get();
        $count=count($tickets);

        $states= DB::SELECT('SELECT id AS id,
                            name AS name
                      FROM ticket_states');
        return view('service',['modules' => $modules,'user' => $user,'tickets' => $tickets,'states'=>$states,'count'=>$count]);
    }

    public function search(Request $request)
    {

        $user = Auth::user();
        $application = new Application();
    if ($user) {
        $modules = $application->getModules($user->id,3);
        $states= DB::SELECT('SELECT id AS id,
                                    name AS name
                              FROM ticket_states');


        if($request->user_type == "user")
        {   
            if ($request->TipoBusquedas == 2) {
            $tickets = Ticket::where('user_id','=',$user->id)
                        ->where('status_id','=',$request->state)
                        ->orderby('status_id','asc')
                        ->orderby('created_at','desc')
                        ->get();
            $count=count($tickets);
            }else{
            if (is_numeric($request->text)) {

            $tickets = Ticket::where('user_id','=',$user->id)
                        ->where('id','=',$request->text)
                        ->orderby('status_id','asc')
                        ->orderby('created_at','desc')
                        ->get();
            $count=count($tickets);
            }else{
            $tickets = Ticket::where('user_id','=',$user->id)
                        ->where('text','like',"%".$request->text."%")
                        ->orderby('status_id','asc')
                        ->orderby('created_at','desc')
                        ->get();
            $count=count($tickets);
            }
           }
            return view('service',['modules' => $modules,'user' => $user,'tickets' => $tickets,'states'=>$states,'count'=>$count]);
        }
        else
        {   

            if ($request->TipoBusquedas != 2) {

                if (is_numeric($request->text)) {
                $tickets = Ticket::where('agent_id','=',$user->id)
                            ->where('id','=',$request->text)
                            ->orderby('status_id','asc')
                            ->orderby('created_at','desc')
                            ->get();
                $count=count($tickets);
                }else{
                $tickets = Ticket::where('agent_id','=',$user->id)
                            ->where('text','like',"%".$request->text."%")
                            ->orderby('status_id','asc')
                            ->orderby('created_at','desc')
                            ->get();
                $count=count($tickets);               
                }
           }else{
                
                $tickets = Ticket::where('agent_id','=',$user->id)
                       ->where('status_id','=',$request->state)
                       ->orderby('status_id','asc')
                       ->orderby('created_at','desc')
                       ->get();
                $count=count($tickets); 
           }
            return view('ticket.pending',['modules' => $modules,'user' => $user,'tickets' => $tickets,'states'=>$states,'count'=>$count]);
        }
    }else{
             if ($request->TipoBusquedas != 2) {

                 $states= DB::SELECT('SELECT id AS id,
                                    name AS name
                              FROM ticket_states');
                
                $agent=DB::SELECT('SELECT agent_id as agent_id FROM tickets
                                   WHERE id=?',[intval($request->text)]);

                $application = new Application();
                $modules = $application->getModules($agent[0]->agent_id,3);
                $tickets = Ticket::where('agent_id','=',$agent[0]->agent_id)
                            ->where('id','=',$request->text)
                            ->orderby('status_id','asc')
                            ->orderby('created_at','desc')
                            ->get();
                $count=count($tickets);
                return view('ticket.pending',['modules' => $modules,'user' => $user,'tickets' => $tickets,'states'=>$states,'count'=>$count]);
           }


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
        $modules = $application->getModules($user->id,3);

        $tools = Tool::where('active','=',1)
                            ->orderby('name','asc')
                            ->get();
        return view('ticket.create',['modules' => $modules,'user' => $user,'tools' => $tools]);
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
        $data=array();
        $tool = Tool::where('id','=',$request->tool_id)->get();
        $ticket = new Ticket();
        $ticket->user_id = $request->user_id;
        $ticket->tool_id = $request->tool_id;
        $ticket->agent_id = $tool[0]->user->id;
        $ticket->status_id = 2;
        $ticket->text = $request->text;

        $file = $request->file('file');
        if(isset($file))
        {
            
            $ext = $file->getClientOriginalExtension();
            $nombre =  $request->user_id."_".Str::random(16).".".$ext;
            $ticket->file = $nombre;
            \Storage::disk('tickets')->put($nombre,  \File::get($file));
        }

        $ticket->active = 1;
        $ticket->save();

        $ticketLog = new TicketLog();
        $ticketLog->ticket_id = $ticket->id;
        $ticketLog->user_id = $request->user_id;
        $ticketLog->text = "El caso fue creado";
        $ticketLog->status_id = 1;
        $ticketLog->active = 1;
        $ticketLog->save();

        $ticketLog = new TicketLog();
        $ticketLog->ticket_id = $ticket->id;
        $ticketLog->user_id = $request->user_id;
        $ticketLog->text = "El caso fue Asigando a {$tool[0]->user->name}";
        $ticketLog->status_id = 2;
        $ticketLog->active = 1;
        $ticketLog->save();
        
        $Case = Ticket::orderby('id','DESC')->limit(1)->get();
        $TicketUser= User::where('id','=',$request->user_id);

        $assignmentuser = $tool[0]->user->first_name;
        $Type = 'caso';
        $CaseNumber =$Case[0]->id;
        $MailSend= $tool[0]->user->email;

        $request->session()->put('assignmentuser', $assignmentuser);
        
        $data=[$assignmentuser,$Type,$CaseNumber,$user->name];


        Mail::to($MailSend)->send(new SendMail($data));

        return redirect()->route('service');
        

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
        $application = new Application();

        if ($user) {
        $modules = $application->getModules($user->id,3);

        $ticket = Ticket::find($id);
        $showSolution = 0;
        $solution = TicketLog::where('ticket_id','=',$id)
                    ->where('status_id','=',4)
                    ->orderby('created_at','desc')
                    ->first();

        $data = TicketLog::where('ticket_id','=',$id)
                    ->orderby('created_at','desc')
                    ->get();
        $filescount = TicketLog::where('ticket_id','=',$id)
                    ->where('file','<>',null) 
                    ->orderby('created_at','desc')
                    ->get();
        $tickets = Ticket::where('agent_id','=',$user->id)
                    ->orderby('status_id','asc')
                    ->orderby('created_at','desc')
                    ->get();

        $count=count($tickets);

        $numberofelements=count($filescount);
        if($user->id == $ticket->user_id)
        {
            $showSolution = 1;
        }
        $states= DB::SELECT('SELECT id AS id,
                                    name AS name
                              FROM ticket_states');
        $tools = Tool::all()->where('active','=',1);
        return view('ticket.show',['modules' => $modules,'user' => $user,'ticket' => $ticket,'showSolution' => $showSolution,'solution' => $solution,'data'=>$data,'elements'=>$numberofelements,'states'=>$states,'count'=>$count]);
        }else{

        $agent=DB::SELECT('SELECT agent_id as agent_id FROM tickets
                           WHERE id=?',[$id]);

        $application = new Application();
        $modules = $application->getModules($agent[0]->agent_id,3);

        $ticket = Ticket::find($id);
        $showSolution = 0;
        $solution = TicketLog::where('ticket_id','=',$id)
                    ->where('status_id','=',4)
                    ->orderby('created_at','desc')
                    ->first();

        $data = TicketLog::where('ticket_id','=',$id)
                    ->orderby('created_at','desc')
                    ->get();
        $filescount = TicketLog::where('ticket_id','=',$id)
                    ->where('file','<>',null) 
                    ->orderby('created_at','desc')
                    ->get();
        $tickets = Ticket::where('agent_id','=',$agent[0]->agent_id)
                    ->orderby('status_id','asc')
                    ->orderby('created_at','desc')
                    ->get();

        $count=count($tickets);

        $numberofelements=count($filescount);
        if($agent[0]->agent_id == $ticket->user_id)
        {
            $showSolution = 1;
        }
        $states= DB::SELECT('SELECT id AS id,
                                    name AS name
                              FROM ticket_states');
        $tools = Tool::all()->where('active','=',1);
        return view('ticket.show',['modules' => $modules,'user' => $user,'ticket' => $ticket,'showSolution' => $showSolution,'solution' => $solution,'data'=>$data,'elements'=>$numberofelements,'states'=>$states,'count'=>$count]);



        }


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

    public function pending()
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules($user->id,3);

        $tickets = Ticket::where('agent_id','=',$user->id)
                    ->orderby('status_id','asc')
                    ->orderby('created_at','desc')
                    ->get();

        $states= DB::SELECT('SELECT id AS id,
                                    name AS name
                              FROM ticket_states');
        $count=count($tickets);

        return view('ticket.pending',['modules' => $modules,'user' => $user,'tickets' => $tickets,'states'=>$states,'count'=>$count]);
    }

    public function solution($id)
    {

        $user = Auth::user();

        if ($user) {
        $application = new Application();
        $modules = $application->getModules($user->id,3);

        $ticket = Ticket::find($id);

        return view('ticket.solution',['modules' => $modules,'user' => $user,'ticket' => $ticket]);
        }else{
        $agent=DB::SELECT('SELECT agent_id as agent_id FROM tickets
                           WHERE id=?',[$id]);

        $application = new Application();
        $modules = $application->getModules($agent[0]->agent_id,3);

        $ticket = Ticket::find($id);

        return view('ticket.solution',['modules' => $modules,'user' => $user,'ticket' => $ticket]);
       }
    }

    public function solve(Request $request, $id)
    {
        $user = Auth::user();
        

if ($user) {
        $ticket = Ticket::find($id);
        $ticket->status_id = 4;
        $ticket->save();

        $ticketLog = new TicketLog();
        $ticketLog->ticket_id = $id;
        $ticketLog->user_id = $user->id;
        $ticketLog->text = $request->text;
        $ticketLog->status_id = 4;
        $ticketLog->active = 1;
        $ticketLog->save();


        $Case = $id;
        $TicketUser= User::where('id','=',$ticket->user_id);


        $Type = 'casor';
        $CaseNumber =$id;
        $UserEmail= DB::SELECT('SELECT email AS email, first_name AS name FROM users
                                WHERE id=?',[$ticket->user_id]);

        $agent = DB::SELECT('SELECT name AS name FROM users 
                             WHERE id=?',[$ticket->agent_id]);

        $MailSend= $UserEmail[0]->email;


        $request->session()->put('assignmentuser', $UserEmail[0]->name);
        
        $data=[$UserEmail[0]->name,$Type,$CaseNumber,$agent[0]->name];


        Mail::to($MailSend)->send(new SendMail($data));




        return redirect()->route('ticket.pending');
}else{

        $agent=DB::SELECT('SELECT agent_id as agent_id FROM tickets
                           WHERE id=?',[$id]);    
        $ticket = Ticket::find($id);
        $ticket->status_id = 4;
        $ticket->save();

        $ticketLog = new TicketLog();
        $ticketLog->ticket_id = $id;
        $ticketLog->user_id = $agent[0]->agent_id;
        $ticketLog->text = $request->text;
        $ticketLog->status_id = 4;
        $ticketLog->active = 1;
        $ticketLog->save();

        $Case = $id;
        $TicketUser= User::where('id','=',$ticket->user_id);


        $Type = 'casor';
        $CaseNumber =$id;
        $UserEmail= DB::SELECT('SELECT email AS email, first_name AS name FROM users
                                WHERE id=?',[$ticket->user_id]);

        $agent = DB::SELECT('SELECT name AS name FROM users 
                             WHERE id=?',[$ticket->agent_id]);

        $MailSend= $UserEmail[0]->email;


        $request->session()->put('assignmentuser', $UserEmail[0]->name);
        
        $data=[$UserEmail[0]->name,$Type,$CaseNumber,$agent[0]->name];


        Mail::to($MailSend)->send(new SendMail($data));




        return view('welcome');
}

    }

    public function solutiona($id)
    {
        $user = Auth::user();

        if ($user) {
        $application = new Application();
        $modules = $application->getModules($user->id,3);

        $ticket = Ticket::find($id);

        return view('ticket.solutiona',['modules' => $modules,'user' => $user,'ticket' => $ticket]);
        }else{
          $userid=DB::SELECT('SELECT user_id AS user_id
                              FROM tickets 
                              WHERE id=?',[$id]);
        $application = new Application();
        $modules = $application->getModules($userid[0]->user_id,3);

        $ticket = Ticket::find($id);

        return view('ticket.solutiona',['modules' => $modules,'user' => $user,'ticket' => $ticket]);

        }

    }

    public function accept(Request $request, $id)
    {
        $user = Auth::user();

        if ($user) {
        $finaltext='';
        if ($request->text == NULL) {
            $finaltext= 'Solucion aceptada';
        }else{
            $finaltext= $request->text;
        }

        
        $ticket = Ticket::find($id);
        $ticket->status_id = 5;
        $ticket->save();

        $ticketLog = new TicketLog();
        $ticketLog->ticket_id = $id;
        $ticketLog->user_id = $user->id;
        $ticketLog->text = $finaltext;
        $ticketLog->status_id = 5;
        $ticketLog->active = 1;
        $ticketLog->save();

        return redirect()->route('service');
        }else{

        $finaltext='';
        if ($request->text == NULL) {
            $finaltext= 'Solucion aceptada';
        }else{
            $finaltext= $request->text;
        }

        
        $ticket = Ticket::find($id);
        $ticket->status_id = 5;
        $ticket->save();

        $ticketLog = new TicketLog();
        $ticketLog->ticket_id = $id;
        $ticketLog->user_id = $ticket->user_id;
        $ticketLog->text = $finaltext;
        $ticketLog->status_id = 5;
        $ticketLog->active = 1;
        $ticketLog->save();

         return view('welcome');
        }     

    }

    public function solutiond($id)
    {
        $user = Auth::user();
        if ($user) {
        $application = new Application();
        $modules = $application->getModules($user->id,3);

        $ticket = Ticket::find($id);

        return view('ticket.solutiond',['modules' => $modules,'user' => $user,'ticket' => $ticket]);
        }else{
        $ticket = Ticket::find($id);
        $application = new Application();
        $modules = $application->getModules($ticket->user_id,3);
        return view('ticket.solutiond',['modules' => $modules,'user' => $user,'ticket' => $ticket]);            
        }

    }

    public function deny(Request $request, $id)
    {
        $user = Auth::user();
        
        if ($user) {
        $ticket = Ticket::find($id);
        $ticket->status_id = 6;
        $ticket->save();

        $ticketLog = new TicketLog();
        $ticketLog->ticket_id = $id;
        $ticketLog->user_id = $user->id;
        $ticketLog->text = $request->text;
        $ticketLog->status_id = 6;
        $ticketLog->active = 1;
        $ticketLog->save();

        return redirect()->route('service');
        }else{
        $ticket = Ticket::find($id);
        $ticket->status_id = 6;
        $ticket->save();

        $ticketLog = new TicketLog();
        $ticketLog->ticket_id = $id;
        $ticketLog->user_id = $ticket->user_id;
        $ticketLog->text = $request->text;
        $ticketLog->status_id = 6;
        $ticketLog->active = 1;
        $ticketLog->save();

         return view('welcome');            
        }

    }

    public function close($id)
    {
        $user = Auth::user();

        if ($user) {
        $application = new Application();
        $modules = $application->getModules($user->id,3);

        $ticket = Ticket::find($id);

        return view('ticket.close',['modules' => $modules,'user' => $user,'ticket' => $ticket]);
        }else{

        $ticket_user= DB::SELECT('SELECT user_id AS user_id
                                 FROM tickets 
                                 WHERE id=?',[$id]);
        $application = new Application();
        $modules = $application->getModules($ticket_user[0]->user_id,3);

        $ticket = Ticket::find($id);

        return view('ticket.close',['modules' => $modules,'user' => $user,'ticket' => $ticket]);
        }


    }

    public function finalize(Request $request, $id)
    {
        $user = Auth::user();

        if ($user) {
        $application = new Application();
        $modules = $application->getModules($user->id,3);
        
        $ticket = Ticket::find($id);
        $ticket->status_id = 7;
        $ticket->save();

        $ticketLog = new TicketLog();
        $ticketLog->ticket_id = $id;
        $ticketLog->user_id = $user->id;
        $ticketLog->text = $request->text;
        $ticketLog->status_id = 7;
        $ticketLog->active = 1;
        $ticketLog->save();


        $tickets = Ticket::where('agent_id','=',$user->id)
                    ->orderby('status_id','asc')
                    ->orderby('created_at','desc')
                    ->get();

        $states= DB::SELECT('SELECT id AS id,
                                    name AS name
                              FROM ticket_states');

        $count=count($tickets);

        return view('ticket.pending',['modules' => $modules,'user' => $user,'tickets' => $tickets,'states'=>$states,'count'=>$count]);

        }else{
        $ticket = Ticket::find($id);
        $ticket->status_id = 7;
        $ticket->save();

        $ticketLog = new TicketLog();
        $ticketLog->ticket_id = $id;
        $ticketLog->user_id = $ticket->user_id;
        $ticketLog->text = $request->text;
        $ticketLog->status_id = 7;
        $ticketLog->active = 1;
        $ticketLog->save();

        return view('welcome');
       }

    }


}
