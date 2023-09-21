<?php

namespace App\Exports;

use App\Pdi;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PdiExport implements FromView
{

    public function view(): View
    {
    	$data= session('data');
    	$datapdiser= session('datapdiser');
    	$datapdisaber= session('datapdisaber');
    	$datapdihacer= session('datapdihacer');
    	$datapditotal= session('datapditotal');
        return view('performance.performance_table', [
            'data' => $data,'datapdiser'=>$datapdiser,'datapdisaber'=>$datapdisaber,'datapdihacer'=>$datapdihacer,'datapditotal'=>$datapditotal
        ]);
    }


}
