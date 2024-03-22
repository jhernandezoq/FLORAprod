<?php

namespace App\Http\Controllers;

use DB;
use App\Document;
use Illuminate\Http\Request;

use App\Application;
use Illuminate\Support\Facades\Auth;

use App\DianResolution;
use App\DocumentLine;

use App\Http\Controllers\XmlController;

class DocumentController extends Controller
{
    public function send_data( Request $request )
    {
        try {
            $message = '';
            $json = [
                'error' => false,
                'data' => [],
                'message' => '',
                'errorMessage' => '',
                'errorReason' => '',
                'datos' => '',
                'url-dian' => '',
            ];
            $user = Auth::user();
            $inputs = $request->all();

            $id = $inputs['document_id'];
            if( $id == 0 || $id == ''  ){
                $json['error'] = true;
                $json['message'] = 'No se recibió un ID válido';
                exit( json_encode( $json ) );
            }

            $nameXML = 'xml1.xml';
            $pathDS = storage_path('app') . '/ds/' . $nameXML;
            if( ! file_exists($pathDS) ){
                $json['error'] = true;
                $json['message'] = 'No se encontró el XML';
                exit( json_encode( $json ) );
            }

            // Read the XML to send to the Web Service
            $request_file = $pathDS;
            $fh = fopen( $request_file, 'r' );
            $xml_data = fread( $fh, filesize($request_file) );
            fclose($fh);

            $xml_data_base_64 = base64_encode($xml_data);

            $URLS_BASE = config('app.URLS_BASE');
            $urlBase = config('app.getUrlBase');

            $doc = Document::find( $id );

           // $company = config('app.equivalenDocument')['companies'][ $doc->document ];

            $url = 'https://apivp.efacturacadena.com/v1/vp/documentos/proceso/sincrono';
            $headers = array(
                'Content-Type: text/plain',
                'efacturaAuthorizationToken: 9f9b4217-81d6-41df-9308-8ebed0235dc7',
            );

            /*if( $urlBase == $URLS_BASE['local'] || $urlBase == $URLS_BASE['dev'] )
            {
                //$url = 'https://apivp.efacturacadena.com/staging/vp/documentos/proceso/sincrono';
                $url = $company['dev']['url'];
                //$credentials = "username:password";
                //$page = "/services/calculation";
                $headers = array(
                    //'POST '.$page.' HTTP/1.0',
                    //'Authorization: Basic ' . base64_encode($credentials),
                    //'Cache-Control: no-cache',
                    //'Pragma: no-cache',
                    //'SOAPAction: "run"',
                    //'Content-length: '.strlen($xml_data_base_64),
                    //'Accept: text/xml',
                    //'Content-type: text/xml;charset="utf-8"',
                    //'Content-Type: application/xml',
                    'Content-Type: text/plain',
                    'efacturaAuthorizationToken: ' . $company['dev']['token'],
                );
            }*/
            $json['url-dian'] = $url;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            //curl_setopt($ch, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);

            // Apply the XML to our curl call
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data_base_64);

            $data = curl_exec($ch);

            if( curl_errno($ch) ){
                $error_ = "Error-1: " . curl_error($ch);
                $json['datos'] = $error_;
            }
            else{
                $dataJson = json_decode( $data );
                if( $dataJson->{'statusCode'} == 200 )
                {
                    // RESOLUCION => Consecutivo Ste
                    $resDian = new DianResolution();
                    $resolutionDian = $resDian->getOneByCompanyId( $doc->company_id );
                    $document_number = $resolutionDian->prefix . ( $resolutionDian->current + 1 );

                    $doc->document_number = $document_number;
                    $doc->document_prefix = $resolutionDian->prefix;
                    $doc->save();

                    // Actualizo Consecutivo Resolucion
                    $resolDian = DianResolution::find( $resolutionDian->id );
                    $resolDian->current = $resolutionDian->current + 1;
                    $resolDian->save();

                    $json['data'] = 'Documento procesado correctamente';
                }
                else{
                    $json['error'] = true;
                    $json['data'] = $dataJson;
                }
                $json['datos'] = $dataJson;
                curl_close($ch);
            }
            // --------------------- --------------------- ---------------------


            exit( json_encode( $json ) );
        }
        catch (\Throwable $th) {
            $json = json_encode([
                'error' => true,
                'data' => $dataJson,
                'message' => 'Error en el servidor: '.$th->getMessage().
                '<br />Línea: '.$th->getLine(),
            ]);
            exit( $json );
        }
    }

    public function setOptionSelect( $id, $text ){
        $option = new \stdClass();
        $option->id = $id;
        $option->text = $text;
        return $option;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules( $user->id, 4 );

        $document = new Document();
        $rows = $document->getAll( );
        $count = count( $rows );

        return view('document/index',[
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

        $companies = DB::SELECT('SELECT * FROM companies WHERE active = 1;');

        $resolutions_dians = new DianResolution();
        $resolutions_dian = $resolutions_dians->getAllActives();

        $document = new Document();

        if( $id === -1 ){
            $row = $document->initRow( NULL );
        }else{
            $rows = $document->getOne( $id );
            if( count( $rows ) == 0 ){
                $row = $document->initRow( NULL );
                $message = 'No se encontro el registro con ID = ' . $id;
            }
            else{
                $row = $document->initRow( $rows[0] );
            }
        }



        return view('document/create',[
            'message' => $message,
            'modules' => $modules,
            'user' => $user,
            'companies' => $companies,
            'resolutions_dian' => $resolutions_dian,
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

       // $equivalenDocument = config('app.equivalenDocument');

        try {
            if( $inputs['id'] != 0 ){
                $row = Document::find( $inputs['id'] );
            }else{
                $row = new Document();
                $row->status = 'Creado';

                $row->type = 'Documento de soporte';
               // $row->type_code = $equivalenDocument['DocumentTypeCode'][$row->type];

               $row->type_code ='05';
               /*array(
                'Documento de soporte' => '05',
               );*/
            }


            $URLS_BASE = array(
                'production' => 'https://flora.tierragro.com',
                //'dev' => 'http://3.238.161.180/flora/public',
                'dev' => 'http://172.31.113.4/flora/public',
                'local' => 'http://localhost/flora/public',
            );
           // $urlBase = config('app.getUrlBase');
           // var_dump("La url base es:".$urlBase);

          /*  $environments = array(
                'prod' => 1,
                'dev' => 2,
            );
            $environment = $environments['prod'];
            if( $urlBase == $URLS_BASE['local'] || $urlBase == $URLS_BASE['dev'] ){
                $environment = $environments['dev'];
            }*/
            $row->environment = 1;

            $row->company_id = $inputs['company_id'];
            $row->reason = $inputs['reason'];
            $row->document_type = $inputs['document_type'];
            $row->document = $inputs['document'];
            $row->verification_digit = $inputs['verification_digit'];
            $row->legal_organization = $inputs['legal_organization'];
            $row->tribute = $inputs['tribute'];
            $row->fiscal_obligations_code = $inputs['fiscal_obligations_code'];
            //
            $row->resolution_id = $inputs['resolution_id'];
            $row->document_prefix = $inputs['document_prefix'];
            $row->supplier_country = $inputs['supplier_country'];
            $row->department_id = $inputs['department_id'];
            $row->city_id = $inputs['city_id'];
            $row->resident = $inputs['resident'];
            $row->language = $inputs['language'];
            $row->document_number = $inputs['document_number'];
            $row->date_transaction = $inputs['date_transaction'];
            $row->time_transaction = $inputs['time_transaction'];
            $row->currency = $inputs['currency'];
            $row->note = $inputs['note'];
            $row->observations = $inputs['observations'];
            $row->note1 = $inputs['note1'];
            $row->note2 = $inputs['note2'];
            $row->note3 = $inputs['note3'];
            $row->note4 = $inputs['note4'];
            $row->note5 = $inputs['note5'];

            $row->document_id = $inputs['document_id'];
            $row->document_number_note = $inputs['document_number_note'];
            $row->date_send_ds = $inputs['date_send_ds'];

            $row->have_purchase_order = $inputs['have_purchase_order'];
            $row->order_number = $inputs['order_number'];
            $row->order_date = $inputs['order_date'];
            $row->have_advance = $inputs['have_advance'];
            $row->advance_number = $inputs['advance_number'];
            $row->advance_date = $inputs['advance_date'];

            $row->supplier_id = $inputs['supplier_id'];
            $row->supplier_reason = $inputs['supplier_reason'];
            $row->supplier_document_type = $inputs['supplier_document_type'];
            $row->supplier_document = $inputs['supplier_document'];
            $row->supplier_verification_digit = $inputs['supplier_verification_digit'];
            $row->supplier_legal_organization = $inputs['supplier_legal_organization'];
            $row->supplier_tribute = $inputs['supplier_tribute'];
            $row->supplier_fiscal_obligations_code = $inputs['supplier_fiscal_obligations_code'];
            $row->supplier_address = $inputs['supplier_address'];
            $row->supplier_postal_code = $inputs['supplier_postal_code'];

            $row->way_pay = $inputs['way_pay'];
            $row->payment_method = $inputs['payment_method'];
            $row->date_due_payment = $inputs['date_due_payment'];

            $row->base_currency_init = $inputs['base_currency_init'];
            $row->monetary_base_init = $inputs['monetary_base_init'];
            $row->base_currency_end = $inputs['base_currency_end'];
            $row->monetary_base_end = $inputs['monetary_base_end'];
            $row->exchange_rate_value = $inputs['exchange_rate_value'];
            $row->date_exchange_payment = $inputs['date_exchange_payment'];
            $row->base_currency_init2 = $inputs['base_currency_init2'];
            $row->monetary_base_init2 = $inputs['monetary_base_init2'];
            $row->base_currency_end2 = $inputs['base_currency_end2'];
            $row->monetary_base_end2 = $inputs['monetary_base_end2'];
            $row->exchange_rate_value2 = $inputs['exchange_rate_value2'];
            $row->date_exchange_payment2 = $inputs['date_exchange_payment2'];
            
            
            $row->value_letters = 'UN MILLON';
            
            $row->total_lines = '1';
            $row->gross_total_base_lines = '1';
            $row->gross_total_and_tribute = '1';
            $row->discounts_total_and_detail = '0';
            $row->charges_total_and_detail = '1';
            $row->pay_total = '1000000';

            

            $row->save();

            $json = json_encode([
                'error' => false,
                'data' => $row,
                'message' => '',
            ]);
            exit( $json );
        }
        catch ( \Throwable $th ) {
            $errro = json_encode([
                'error' => true,
                'data' => [],
                'message' => 'Error en el servidor: '.$th->getMessage().
                '<br />Línea: '.$th->getLine(),
            ]);
            exit( $errro );
        }
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    //public function show(Document $document )
    public function show( $id )
    {
        $message = '';
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules( $user->id, 4 );

        $document = new Document();
        $rows = $document->getOne( $id );
        if( count( $rows ) == 0 ){
            $row = $document->initRow( NULL );
            $message = 'No se encontro el registro con ID = ' . $id;
        }
        else{
            $row = $document->initRow( $rows[0] );

            $rowsDocumentLine_ = new DocumentLine();
            $rowsDocumentLine = $rowsDocumentLine_->getAllByDocumentId( $row->id );
        }

        return view('document/show',[
            'message' => $message,
            'modules' => $modules,
            'user' => $user,
            'row' => $row,
            'rowsDocumentLine' => $rowsDocumentLine,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    //public function edit(Document $document)
    public function edit( Request $request )
    {
        $message = '';
        $json = [
            'error' => false,
            'data' => [],
            'message' => '',
            'datos' => [],
        ];
        try {      
            $user = Auth::user();
            $inputs = $request->all();

            $equivalenDocument = config('app.equivalenDocument');

            $document = Document::find( $inputs['document_id'] );

            $rows_ = new DocumentLine();
            $max_line = $rows_->getMaxLineByDocumentId( $inputs['document_id'] );
            
            if( $inputs['id'] != 0 ){
                $row = DocumentLine::find( $inputs['id'] );
            }
            else{
                $row = new DocumentLine();
                if( $max_line === NULL || $max_line == '' || $max_line == 0 ){
                    $row->line = 1;
                }
                else{
                    $row->line = $max_line + 1;
                }
            }
            $row->product_code = $row->line;

            $quantityUnit = $rows_->getInfoQuantityUnitById( $inputs['quantity_unit_id'] );

            $row->gen_transmission = 'Por operación';
          //  $row->gen_transmission_code = $equivalenDocument['formsGenerationTransmission'][ $row->gen_transmission ];
            $row->gen_transmission_code = 1;
            $row->unit_measurement = $quantityUnit->code;
            $row->date_purchase = $document->date_transaction;

            $row->document_id = $inputs['document_id'];
            $row->quantity_unit_id = $inputs['quantity_unit_id'];
            $row->quantity = $inputs['quantity'];
            $row->total = $inputs['total'];
            $row->item_description = $inputs['item_description'];
            $row->price = $inputs['price'];
            $row->perc_charge = $inputs['perc_charge'];
            $row->perc_discount = $inputs['perc_discount'];
            $row->perc_tax = $inputs['perc_tax'];
            $row->perc_reterenta = $inputs['perc_reterenta'];
            //$row->code_discounts_charge_tax = $inputs['code_discounts_charge_tax'];
            $row->note = '';
            $row->item_brand = '';
            $row->item_model = '';
            // siempre como ejemplo excel
            $row->product_scheme_id = '999';

            $row->save();

            $rows = $rows_->getAllByDocumentId( $row->document_id );
            $json['data'] = $rows;
            
            exit( json_encode( $json ) );
        }
        catch (\Throwable $th) {
            $error = json_encode([
                'error' => true,
                'data' => [],
                'message' => 'Error en el servidor: '.$th->getMessage().
                '<br />Línea: '.$th->getLine(),
            ]);
            exit( $error );
        }
    }

    /**
     * Get by ID: 
     * DiscountsChargeTax
     *
     * @param  \App\Document  $id, $type
     * @return data
     */
    public function get_item( Request $request )
    {
        try {
            $message = '';
            $json = [
                'error' => false,
                'data' => [],
                'message' => '',
            ];
            $user = Auth::user();
            $inputs = $request->all();

            $id = $inputs['id'];
            $type = $inputs['type'];

            if( $id == 0 || $id == ''  ){
                $json['error'] = true;
                $json['message'] = 'No se recibió un ID válido';
                exit( json_encode( $json ) );
            }
            
            $row = [];
            if( $type == 'DocumentsLines' ){
                $rowClass = new DocumentLine();
                $row = $rowClass->getOneById( $id );
            }
            $json['data'] = $row;
            
            exit( json_encode( $json ) );
        }
        catch (\Throwable $th) {
            $json = json_encode([
                'error' => true,
                'data' => [],
                'message' => 'Error en el servidor: '.$th->getMessage().
                '<br />Línea: '.$th->getLine(),
            ]);
            exit( $json );
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request )
    {
        try {
            $message = '';
            $json = [
                'error' => false,
                'data' => [],
                'message' => '',
                'xml' => '',
            ];
            $user = Auth::user();
            $inputs = $request->all();

            $id = $inputs['document_id'];

            if( $id == 0 || $id == ''  ){
                $json['error'] = true;
                $json['message'] = 'No se recibió un ID válido';
                exit( json_encode( $json ) );
            }
            
            $doc_ = new Document();
            $docs = $doc_->getOne( $id );
            $doc = $docs[ 0 ];

            // RESOLUCION => Consecutivo Ste
            $resolutions_dians = new DianResolution();
            $resolutionDian = $resolutions_dians->getOneByCompanyId( $doc->company_id );
            if( ! $resolutionDian ){
                $json['message'] = 'No se encontró una resolución activa para la Compañía';
                $json['error'] = true;
                exit( json_encode( $json ) );
            }

            $document_number = $resolutionDian->prefix . ( $resolutionDian->current + 1 );
            $doc->document_number = $document_number;
            $doc->document_prefix = $resolutionDian->prefix;

            // Fecha y Hora actuales:
            date_default_timezone_set('America/Bogota');
            $current_date = date('Y-m-d');
            $current_time = date('H:i') . ':00-05:00';
            $doc->date_transaction = $current_date;
            $doc->time_transaction = $current_time;

            // ---------------------------------------
            $rowsDocumentLine_ = new DocumentLine();
            $documentLines = $rowsDocumentLine_->getAllByDocumentIdToXML( $doc->id );
            $doc->total_lines = count( $documentLines );

            // =======================================
            // Generar el XML ========================
            $xml = new XmlController();
            $dataXml = $xml->create( $doc, $resolutionDian, $documentLines );
            $json['xml'] = $dataXml;
            // Fin el XML ============================

            // Actualizar DS nuevamente
            $row = Document::find( $id );
            $row->value_letters = $inputs['value_letters'];
            $row->total_lines = $doc->total_lines;
            $row->gross_total_base_lines = $inputs['gross_total_base_lines'];
            $row->gross_total_minus_discounts = $inputs['gross_total_minus_discounts'];
            $row->gross_total_and_tribute = $inputs['gross_total_and_tribute'];
            $row->discounts_total_and_detail = $inputs['discounts_total_and_detail'];
            $row->charges_total_and_detail = $inputs['charges_total_and_detail'];
            $row->pay_total = $inputs['pay_total'];
            $row->perc_reteiva = $inputs['perc_reteiva'];

            //$row->document_number = $document_number;
            //$row->document_prefix = $resolutionDian->prefix;
            $row->date_transaction = $current_date;
            $row->time_transaction = $current_time;
            $row->save();
            

            /* Actualizar Consecutivo Resolucion
            $resolDian = DianResolution::find( $resolutionDian->id );
            $resolDian->current = $resolutionDian->current + 1;
            //$resolDian->save(); */

            $json['data'] = $row;
            exit( json_encode( $json ) );
        }
        catch (\Throwable $th) {
            $json = json_encode([
                'error' => true,
                'data' => [],
                'message' => 'Error en el servidor: '.$th->getMessage().
                '<br />Línea: '.$th->getLine(),
            ]);
            exit( $json );
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request )
    {
        try {
            $message = '';
            $json = [
                'error' => false,
                'data' => [],
                'message' => '',
            ];
            $user = Auth::user();
            $inputs = $request->all();

            $id = $inputs['id'];
            $type = $inputs['type'];

            if( $id == 0 || $id == ''  ){
                $json['error'] = true;
                $json['message'] = 'No se recibió un ID válido';
                exit( json_encode( $json ) );
            }
            
            $rows = [];
            if( $type == 'DocumentsLines' )
            {
                $rowDel = DocumentLine::find( $id );
                $document_id = $rowDel->document_id;
                $line_del = $rowDel->line;
                $rowDel->delete();

                $rowClass = new DocumentLine();
                $rows = $rowClass->getAllByDocumentId( $document_id );

                $line_next = 1;
                foreach ( $rows as $key => $value ){
                    if( $value->line < $line_del ){
                        $line_next = $value->line + 1;
                    }
                    else{ // >
                        $rowUpdate = DocumentLine::find( $value->id );
                        $rowUpdate->line = $line_next;
                        $rowUpdate->product_code = $line_next;
                        $rowUpdate->save();

                        $line_next = $line_next + 1;
                    }
                }
                $rows = $rowClass->getAllByDocumentId( $document_id );
            }

            $json['data'] = $rows;
            
            exit( json_encode( $json ) );
        }
        catch (\Throwable $th) {
            $json = json_encode([
                'error' => true,
                'data' => [],
                'message' => 'Error en el servidor: '.$th->getMessage().
                '<br />Línea: '.$th->getLine(),
            ]);
            exit( $json );
        }
    }

    /**
     * Buesqueda por Departamentos.
     *
     */
    public function search_deptos( Request $request )
    {
        $depto = $request->depto;
        $deptos = DB::table('departments')
            ->where('name', 'like', '%' . $depto . '%' )->get();

        $response = [
            'error' => false,
            'data' => [],
            'message' => '',
        ];    
        if( count( $deptos ) != 0 ){
            $options = [];
            foreach( $deptos as $key => $value ){
                $option = $this->setOptionSelect( $value->id, $value->name );
                array_push( $options, $option );
            }
            $response[ 'data' ] = $options;
        }
        $json = json_encode( $response );
        exit( $json );
    }

    /**
     * Buesqueda por Municipios.
     *
     */
    public function search_cities( Request $request )
    {
        $depto = $request->depto;
        $city = $request->city;
        $rows = DB::table('cities')
            ->whereRaw("name like '%".$city."%' AND department_id = ?;", [ 
                $depto, 
            ])
            ->get();

        $response = [
            'error' => false,
            'data' => [],
            'message' => '',
        ];    
        if( count( $rows ) != 0 ){
            $options = [];
            foreach( $rows as $key => $value ){
                $option = $this->setOptionSelect( $value->id, $value->name );
                array_push( $options, $option );
            }
            $response[ 'data' ] = $options;
        }
        $json = json_encode( $response );
        exit( $json );
    }

    /**
     * Busqueda por Proveedor.
     *
     */
    public function search_supplier( Request $request )
    {
        $query = $request->query_;
        $rows = DB::table('suppliers')
            ->where('name', 'like', '%' . $query . '%' )
            ->orWhere('nit', 'like', '%' . $query . '%' )
            ->get();

        $response = [
            'error' => false,
            'data' => [],
            'message' => '',
        ];    
        if( count( $rows ) != 0 ){
            $options = [];
            foreach( $rows as $key => $value ){
                $option = $this->setOptionSelect( $value->id, $value->name );
                $option->nit = $value->nit;
                $option->document_type = $value->document_type;
                array_push( $options, $option );
            }
            $response[ 'data' ] = $options;
        }
        $json = json_encode( $response );
        exit( $json );
    }

    /**
     * Buesqueda por Unidad de medida.
     *
     */
    public function search_quantity_unit( Request $request )
    {
        try {
            $query = $request->query_;
            $rows = DB::table('quantity_units')
                ->whereRaw("description like '%".$query."%' ;" )
                ->get();

            $response = [
                'error' => false,
                'data' => [],
                'message' => '',
            ];    
            if( count( $rows ) != 0 ){
                $options = [];
                foreach( $rows as $key => $value ){
                    $text = $value->code .' - '. $value->description;
                    $option = $this->setOptionSelect( $value->id, $text );
                    array_push( $options, $option );
                }
                $response[ 'data' ] = $options;
            }
            $json = json_encode( $response );
            exit( $json );
        }
        catch (\Throwable $th) {
            $error = json_encode([
                'error' => true,
                'data' => [],
                'message' => 'Error en el servidor: '.$th->getMessage().
                '<br />Línea: '.$th->getLine(),
            ]);
            exit( $error );
        }
            
    }



    /**
     * Documentos para posible nota de ajuste.
     *
     */
    public function ajuste( Request $request )
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules( $user->id, 4 );


        $document = new Document();
        $rows = $document->getAllNote( );
        $count = count( $rows );

        return view('document/ajuste',[
            'modules' => $modules,
            'user' => $user,
            'rows' => $rows,
            'count' => $count,
        ]);
            
    }


    /**
     * Documentos para posible nota de ajuste.
     *
     */
    public function ajuste_create( Request $request )
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules( $user->id, 4 );

        $id=$request->id;

        $document = new Document();
        $rows = $document->getOne( $id );

        $rowsDocumentLine_ = new DocumentLine();
        $rowsDocumentLine = $rowsDocumentLine_->getAllByDocumentId( $id );

        $countLines=count($rowsDocumentLine);
        $countLinesVector=array();


        for ($i=0; $i < $countLines ; $i++) { 
            array_push($countLinesVector,$i);
        }
        return view('document/nota_ajuste',[
            'modules' => $modules,
            'user' => $user,
            'rows' => $rows,
            'rowsDocumentLine' => $rowsDocumentLine,
            'countLines' => $countLines,
            'countLinesVector' => $countLinesVector
        ]);
            
    }


    /**
     * Documentos para posible nota de ajuste.
     *
     */
    public function ajuste_save( Request $request )
    {
        $user = Auth::user();
        $application = new Application();
        $modules = $application->getModules( $user->id, 4 );
        $inputs = $request->all();

        $equivalenDocument = config('app.equivalenDocument');

        //$valor=5250;

        $formatter = new NumeroALetras();
        //var_dump($formatter->toString($valor));

        $cantidad_lineas=intval($inputs['countLines']);
        $valor_total=0;
        $valor_bruto=0;
        $valor_descuentos=0;
        $valor_bruto_menos_descuentos=0;
        $elementos_array=array();

        for ($i=0; $i < $cantidad_lineas; $i++) { 
            $valor_total=$valor_total+$inputs['valor_total_'.$i];
            $valor_bruto=$valor_bruto+$inputs['valor_bruto_'.$i];
            $valor_descuentos=$valor_descuentos+$inputs['valor_descuentos_'.$i];
            array_push($elementos_array,$i);

        }

        $valor_bruto_menos_descuentos=$valor_bruto-$valor_descuentos;

        $valor_letras=$formatter->toString($valor_total);
        

        try {
            $rowsDocumentLine = new DocumentLine();
            $row = Document::find( $inputs['id'] );
            $rowsDocumentLine = $rowsDocumentLine->getInfoByIdDocumento( $inputs['id'] );
            $URLS_BASE = config('app.URLS_BASE');
            $urlBase = config('app.getUrlBase');

            $environments = config('app.equivalenDocument')['ProfileExecutionID'];
            $environment = $environments['prod'];
            if( $urlBase == $URLS_BASE['local'] || $urlBase == $URLS_BASE['dev'] ){
                $environment = $environments['dev'];
            }

            $date = Carbon::now();
            $row->environment = $environment;

            $row2 = new Document();
            $row2->company_id = $row->company_id;
            $row2->status = 'Creado';
            $row2->type = 'Documento de soporte';
            $row2->type_code = $row->type_code;
            $row2->reason = $row->reason;
            $row2->document_type = $row->document_type;
            $row2->document = $row->document;
            $row2->verification_digit = $row->verification_digit;
            $row2->legal_organization = $row->legal_organization;
            $row2->tribute = $row->tribute;
            $row2->fiscal_obligations_code = $row->fiscal_obligations_code;
            //
            $row2->resolution_id = $row->resolution_id;
            $row2->document_prefix = $row->document_prefix;
            $row2->supplier_country = $row->supplier_country;
            $row2->department_id = $row->department_id;
            $row2->city_id = $row->city_id;
            $row2->resident = $row->resident;
            $row2->language = $row->language;
            $row2->date_transaction = $date->toDateString();
            $row2->time_transaction = $date->toTimeString()."-05:00";
            $row2->currency = $row->currency;
            $row2->note = $inputs['nota_ajuste'];
            $row2->value_letters = $valor_letras.' PESOS';
            $row2->total_lines = $cantidad_lineas;
            $row2->perc_reteiva = $row->perc_reteiva;
            $row2->charges_total_and_detail = $row->charges_total_and_detail;

            $row2->gross_total_base_lines = $valor_bruto;
            $row2->gross_total_minus_discounts = $valor_bruto_menos_descuentos;
            $row2->gross_total_and_tribute = $valor_bruto_menos_descuentos;
            $row2->discounts_total_and_detail = $row->charges_total_and_detail;

            $row2->pay_total = $valor_total;
            $row2->observations = $row->observations;
            $row2->note1 = $row->note1;
            $row2->note2 = $row->note2;
            $row2->note3 = $row->note3;
            $row2->note4 = $row->note4;
            $row2->note5 = $row->note5;
            $row2->environment = $environment;

            $row2->document_id = $row->document_id;
            $row2->document_number_note = $row->document_number_note;
            $row2->date_send_ds = $row->date_send_ds;

            $row2->have_purchase_order = $row->dohave_purchase_ordercument_id;
            $row2->order_number = $row->order_number;
            $row2->order_date = $row->order_date;
            $row2->have_advance = $row->have_advance;
            $row2->advance_number = $row->advance_number;
            $row2->advance_date = $row->advance_date;

            $row2->supplier_id = $row->supplier_id;
            $row2->supplier_reason = $row->supplier_reason;
            $row2->supplier_document_type = $row->supplier_document_type;
            $row2->supplier_document = $row->supplier_document;
            $row2->supplier_verification_digit = $row->supplier_verification_digit;
            $row2->supplier_legal_organization = $row->supplier_legal_organization;
            $row2->supplier_tribute = $row->supplier_tribute;
            $row2->supplier_fiscal_obligations_code = $row->supplier_fiscal_obligations_code;
            $row2->supplier_address = $row->supplier_address;
            $row2->supplier_postal_code = $row->supplier_postal_code;

            $row2->way_pay = $row->way_pay;
            $row2->payment_method = $row->payment_method;
            $row2->date_due_payment = $row->date_due_payment;
            $row2->have_purchase_order = $row->have_purchase_order;

            $row2->base_currency_init = $row->base_currency_init;
            $row2->monetary_base_init = $row->monetary_base_init;
            $row2->base_currency_end = $row->base_currency_end;
            $row2->monetary_base_end = $row->monetary_base_end;
            $row2->exchange_rate_value = $row->exchange_rate_value;
            $row2->date_exchange_payment = $row->date_exchange_payment;
            $row2->base_currency_init2 = $row->base_currency_init2;
            $row2->monetary_base_init2 = $row->monetary_base_init2;
            $row2->base_currency_end2 = $row->base_currency_end2;
            $row2->monetary_base_end2 = $row->monetary_base_end2;
            $row2->exchange_rate_value2 = $row->exchange_rate_value2;
            $row2->date_exchange_payment2 = $row->date_exchange_payment2;

            $row2->id_replace_document = $row->id;

            $row2->save();

            $maxId = $row->maxId();
           
            $array = json_decode(json_encode($rowsDocumentLine), true);
            

            foreach ($elementos_array as $key) {
                $rowsLine = new DocumentLine();
                $rowsLine->document_id = $maxId[0]->max_id;
                $rowsLine->quantity_unit_id = $array['quantity_unit_id'];
                $rowsLine->line = $array['line'];
                $rowsLine->note = '';
                $rowsLine->quantity = $inputs['cantidad_'.$key];
                $rowsLine->unit_measurement = $array['unit_measurement'];
                $rowsLine->total = $inputs['valor_total_'.$key];
                $rowsLine->date_purchase = $date->toDateString();
                $rowsLine->item_description = $inputs['description_'.$key];
                $rowsLine->item_quantity = $inputs['cantidad_'.$key];
                $rowsLine->item_brand = $array['item_brand'];
                $rowsLine->item_model = $array['item_model'];
                $rowsLine->gen_transmission = 'Por operación';
                $rowsLine->gen_transmission_code = $array['gen_transmission_code'];
                $rowsLine->product_code = $array['product_code'];
                $rowsLine->product_scheme_id = $array['product_scheme_id'];
                $rowsLine->price = $inputs['valor_total_'.$key];
                $rowsLine->perc_charge = $array['perc_charge'];
                $rowsLine->perc_discount = $array['perc_discount'];
                $rowsLine->perc_tax = $array['perc_tax'];
                $rowsLine->perc_reterenta = $array['perc_reterenta'];

                $rowsLine->save();
            }


            $id = $maxId[0]->max_id;            
            $doc_ = new Document();
            $docs = $doc_->getOne( $id );
            $doc = $docs[ 0 ];

            // RESOLUCION => Consecutivo Ste
            $resolutions_dians = new DianResolution();
            $resolutionDian = $resolutions_dians->getOneByCompanyId( $doc->company_id );

            $document_number = $resolutionDian->prefix . ( $resolutionDian->current + 1 );
            $doc->document_number = $document_number;
            $doc->document_prefix = $resolutionDian->prefix;

            // Fecha y Hora actuales:
            date_default_timezone_set('America/Bogota');
            $current_date = date('Y-m-d');
            $current_time = date('H:i') . ':00-05:00';
            $doc->date_transaction = $current_date;
            $doc->time_transaction = $current_time;

            // ---------------------------------------
            $rowsDocumentLine_ = new DocumentLine();
            $documentLines = $rowsDocumentLine_->getAllByDocumentIdToXML( $doc->id );
            $doc->total_lines = count( $documentLines );

            // =======================================
            // Generar el XML ========================
            $xml = new XmlController();
            $dataXml = $xml->create_xml_note( $doc, $resolutionDian, $documentLines );
            $json['xml'] = $dataXml;
            // Fin el XML ============================





            //===========================================================================

            //Envio de XML a Cadena

            //============================================================================
            $nameXML = 'xml_'.$id.'.xml';
            $pathDS = storage_path('app') . '/ds/' . $nameXML;
            if( ! file_exists($pathDS) ){
                $json['error'] = true;
                $json['message'] = 'No se encontró el XML';
                exit( json_encode( $json ) );
            }

            // Read the XML to send to the Web Service
            $request_file = $pathDS;
            $fh = fopen( $request_file, 'r' );
            $xml_data = fread( $fh, filesize($request_file) );
            fclose($fh);

            $xml_data_base_64 = base64_encode($xml_data);

         //   $URLS_BASE = config('app.URLS_BASE');
         //   $urlBase = config('app.getUrlBase');

            $doc = Document::find( $id );

           // $company = config('app.equivalenDocument')['companies'][ $doc->document ];

           // $url = $company['prod']['url'];

            $headers = array(
                'Content-Type: text/plain',
                'efacturaAuthorizationToken: 9f9b4217-81d6-41df-9308-8ebed0235dc7',
            );

         /*   if( $urlBase == $URLS_BASE['local'] || $urlBase == $URLS_BASE['dev'] )
            {
                //$url = 'https://apivp.efacturacadena.com/staging/vp/documentos/proceso/sincrono';
                $url = $company['dev']['url'];
                //$credentials = "username:password";
                //$page = "/services/calculation";
                $headers = array(
                    //'POST '.$page.' HTTP/1.0',
                    //'Authorization: Basic ' . base64_encode($credentials),
                    //'Cache-Control: no-cache',
                    //'Pragma: no-cache',
                    //'SOAPAction: "run"',
                    //'Content-length: '.strlen($xml_data_base_64),
                    //'Accept: text/xml',
                    //'Content-type: text/xml;charset="utf-8"',
                    //'Content-Type: application/xml',
                    'Content-Type: text/plain',
                    'efacturaAuthorizationToken: ' . $company['dev']['token'],
                );
            }*/
            //$json['url-dian'] = 'https://apivp.efacturacadena.com/v1/vp/documentos/proceso/sincrono';
            $url='https://apivp.efacturacadena.com/v1/vp/documentos/proceso/sincrono';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            //curl_setopt($ch, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);

            // Apply the XML to our curl call
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data_base_64);

            $data = curl_exec($ch);
            $error='';

            if( curl_errno($ch) ){
                $error_ = "Error-1: " . curl_error($ch);
                $error1 =0;
                $json['datos'] = $error_;
            }
            else{
                $dataJson = json_decode( $data );
                if( $dataJson->{'statusCode'} == 200 )
                {
                    // RESOLUCION => Consecutivo Ste
                    $resDian = new DianResolution();
                    $resolutionDian = $resDian->getOneByCompanyId( $doc->company_id );
                    $document_number = $resolutionDian->prefix . ( $resolutionDian->current + 1 );

                    $doc->document_number = $document_number;
                    $doc->document_prefix = $resolutionDian->prefix;
                    $doc->save();

                    // Actualizo Consecutivo Resolucion
                    $resolDian = DianResolution::find( $resolutionDian->id );
                    $resolDian->current = $resolutionDian->current + 1;
                    $resolDian->save();

                    $json['data'] = 'Documento procesado correctamente';
                    $error1=0;   
                }
                else{
                    $json['error'] = true;
                    $json['data'] = $dataJson;
                    $error1=$json['data'];
                }
                $json['datos'] = $dataJson;
                curl_close($ch);
            }
            
            $document = new Document();
            $rows = $document->getAllNote( );
            $count = count( $rows );

            $array_error = json_decode(json_encode($error1), true);

           // var_dump($array_error['statusCode']);

        return view('document/ajuste_validation',[
            'modules' => $modules,
            'user' => $user,
            'rows' => $rows,
            'count' => $count,
            'error1' => $array_error['statusCode']
        ]);



    //======================================================================================
    //Fin de envio de XML a cadena

    //======================================================================================

        }
        catch ( \Throwable $th ) {
            $errro = json_encode([
                'error' => true,
                'data' => [],
                'message' => 'Error en el servidor: '.$th->getMessage().
                '<br />Línea: '.$th->getLine(),
            ]);
            exit( $errro );
        }

            
    }



}
