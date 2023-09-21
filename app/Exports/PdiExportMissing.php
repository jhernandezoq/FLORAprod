<?php

namespace App\Exports;

use App\Pdi;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PdiExportMissing implements FromView
{

    public function view(): View
    {
    	$data= session('data');

        return view('performance.performance_table_missing', [
            'data' => $data
        ]);
    }


}