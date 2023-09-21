<?php

namespace App\Http\Controllers;

use DB;
use App\DianResolution;
use Illuminate\Http\Request;

use App\Application;
use Illuminate\Support\Facades\Auth;


class DianResolutionController extends Controller
{
    /**
     * Mostrar Errores.
     */
    public function result( $view, $message ) {
        $params['message'] = $message;
        return view( $view, $params );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $message = '';
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules( $user->id, 4 );

        $dianResolution = new DianResolution();
        $rows = $dianResolution->getAll( );
        $count = count( $rows );

        return view('dianresolution/index',[
            'modules' => $modules,
            'user' => $user,
            'rows' => $rows,
            'count' => $count,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $id = -1 )
    {
        $message = '';
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules( $user->id, 4 );

        $companies = DB::SELECT('SELECT id, name FROM companies WHERE active = 1;');

        $dianResolution = new DianResolution();
        if( $id === -1 )
            $row = $dianResolution->initRow( NULL );
        else{
            $rows = $dianResolution->getOne( $id );
            if( count( $rows ) == 0 ){
                $row = $dianResolution->initRow( NULL );
                $message = 'No se encontro el registro con ID = ' . $id;
            }
            else{
                $row = $dianResolution->initRow( $rows[0] );
            }
        }

        return view('dianresolution/create',[
            'message' => $message,
            'modules' => $modules,
            'user' => $user,
            'companies' => $companies,
            'row' => $row,
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
        $message = '';
        $user = Auth::user();
        $inputs = $request->all();

        
        if( $inputs['id'] != 0 ){
            $row = DianResolution::find( $inputs['id'] );
        }
        else{
            $row = new DianResolution();
        }

        $row->type = $inputs['type'];
        $row->company_id = $inputs['company_id'];
        $row->resolution = $inputs['resolution'];
        $row->start = $inputs['start'];
        $row->end = $inputs['end'];
        $row->current = $inputs['current'];
        $row->date_due = $inputs['date_due'];
        $row->prefix = $inputs['prefix'];

        

        $row->save();

        $json = json_encode([
            'error' => false,
            'data' => $row,
            'message' => '',
        ]);
        exit( $json );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DianResolution  $dianResolution
     * @return \Illuminate\Http\Response
     */
    public function show(DianResolution $dianResolution)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DianResolution  $dianResolution
     * @return \Illuminate\Http\Response
     */
    public function edit(DianResolution $dianResolution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DianResolution  $dianResolution
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DianResolution $dianResolution)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DianResolution  $dianResolution
     * @return \Illuminate\Http\Response
     */
    public function destroy(DianResolution $dianResolution)
    {
        //
    }
}
