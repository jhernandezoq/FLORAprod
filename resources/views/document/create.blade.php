@extends('layouts.app')

@section('content')
<div class="container">


    <!-- Modal -->
    <div class="modal fade" role="dialog" id="modal-messages">
        <div class="modal-dialog">
        <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header-ok">
                    <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
                    <h4 class="modal-title" style="text-align: center;">
                        Atención
                    </h4>
                </div>
                <div class="modal-body" id="div-messages" >
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Mensaje alerta -->
    @php
    if( $message != '' ){
    @endphp
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @php
    }
    @endphp


    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Documento de soporte
                </div>

                @php 
                    date_default_timezone_set('America/Bogota');
                    $current_date = date('Y-m-d');
                    $current_time = date('H:i') . ':00-05:00';

                    if( $row->id != 0 ){
                        $current_date = $row->date_transaction;
                        $current_time = $row->time_transaction;
                    }

                    //$yesNo = config('app.globalLists')['yesNo'];
                    $yesNo = array(
                                "Seleccione.." => '',
                                "Si" => 1,
                                "No" => 0
                            );
                    // Configuraciones compañias
                   // $companies_conf = config('app.equivalenDocument')['companies'];
                   $companies_conf = array(
                    '890912426' => [
			'name' => 'PEREZ Y CARDONA SAS',
			'nit' => '890912426',
			'schemeID_digito_verificacion' => 7,
			'AdditionalAccountID_tipoOrganizacionJuridica' => 1,
			'schemeName_tipoDocumento' => 31,
			// 16.2.5.1
			'TaxLevelCodes' => [
				'Autorretenedor' => 'O-15',
				'Impuesto sobre las ventas' => 'O-48',
			],
			'typeTribute' => '01',
			'dev' => [
				'url' => 'https://apivp.efacturacadena.com/staging/vp/documentos/proceso/sincrono',
				'token' => '64a650b0-e1dc-43cc-b90d-4f6caae599de',
			],
			'prod' => [
				'url' => 'https://apivp.efacturacadena.com/v1/vp/documentos/proceso/sincrono',
				'token' => '9f9b4217-81d6-41df-9308-8ebed0235dc7',
			],
		],
		'811046781' => [
			'name' => 'MP GALAGRO',
			'nit' => '811046781',
			'schemeID_digito_verificacion' => 4,
			'AdditionalAccountID_tipoOrganizacionJuridica' => 1,
			'schemeName_tipoDocumento' => 31,
			'TaxLevelCodes' => [
				'Autorretenedor' => 'O-15',
				'Impuesto sobre las ventas' => 'O-48',
			],
			'typeTribute' => '01',
			'dev' => [
				'url' => 'https://apivp.efacturacadena.com/staging/vp/documentos/proceso/sincrono',
				'token' => '64a650b0-e1dc-43cc-b90d-4f6caae599de',
			],
			'prod' => [
				'url' => 'https://apivp.efacturacadena.com/v1/vp/documentos/proceso/sincrono',
				'token' => '9f9b4217-81d6-41df-9308-8ebed0235dc7',
			],
		],
		'3519589' => [
			'name' => 'ANTONIO JOSE PEREZ',
			'nit' => '3519589',
			'schemeID_digito_verificacion' => 6,
			'AdditionalAccountID_tipoOrganizacionJuridica' => 1,
			'schemeName_tipoDocumento' => 31,
			'TaxLevelCodes' => [
				'No aplica' => 'R-99-PN',
			],
			'typeTribute' => 'ZZ',
			'dev' => [
				'url' => 'https://apivp.efacturacadena.com/staging/vp/documentos/proceso/sincrono',
				'token' => '1f234509-1686-4e2c-93a0-ddasewe7de',
			],
			'prod' => [
				'url' => 'https://apivp.efacturacadena.com/v1/vp/documentos/proceso/sincrono',
				'token' => '9f9b4217-81d6-41df-9308-8ebed0235dc7',
			],
		],
		'890938441' => [
			'name' => 'SUPERAGRO',
			'nit' => '890938441',
			'schemeID_digito_verificacion' => 0,
			'AdditionalAccountID_tipoOrganizacionJuridica' => 1,
			'schemeName_tipoDocumento' => 31,
			'TaxLevelCodes' => [
				'Impuesto sobre las ventas' => 'O-48',
			],
			'typeTribute' => '01',
			'dev' => [
				'url' => 'https://apivp.efacturacadena.com/staging/vp/documentos/proceso/sincrono',
				'token' => '9f9b4217-81d6-41df-9308-8ebed0235dc7',
			],
			'prod' => [
				'url' => 'https://apivp.efacturacadena.com/v1/vp/documentos/proceso/sincrono',
				'token' => '9f9b4217-81d6-41df-9308-8ebed0235dc7',
			],
		],

                   );
                    // Codigos de tipos de documentos equivalente
                    //$document_type_ds = config('app.equivalenDocument')['DocumentTypeCode'];
                    $document_type_ds =array(
                        'Documento de soporte' => '05'
                    );
                    // Codigos de tipos de documentos identidad
                    //$document_types_company = config('app.equivalenDocument')['CompanyID_schemeName'];
                    $document_types_company=array(
                                    'NIT' => 31,
                                    'Cédula de extranjería' => 22,
                                    'Tarjeta de extranjería' => 21,
                                    'Pasaporte' => 41,
                                    'Documento de identificación extranjero' => 42,
                                    'PEP' => 47,
                                    'NIT de otro país' => 50,
                    );
                    // Paises
                   // $countries = config('app.globalLists')['countries'];
                   $countries =array(
                    'Colombia' => [
                    'nombre_iso' => 'Colombia',
                    'codigo_alfa_2' => 'CO'
                    ]
                   );
                    // Monedas
                   // $currencies = config('app.globalLists')['currencies'];
                   $currencies =array(
                    'No aplica',
		            'COP',
                   );
                    // Lenguajes
                    //$languages = config('app.globalLists')['languages'];
                    $languages = array(
                        'Español' => 'es',
                    );
                    // Residentes
                    //$residents = config('app.equivalenDocument')['CustomizationID'];
                    $residents = array(
                        'Residente' => 10,
                    );
                    // Ambientes
                    //$environments = config('app.equivalenDocument')['ProfileExecutionID'];
                    $environments = array(
                        'prod' => 1,
		                'dev' => 2,
                    );
                    // Tipos de organizacion juridica
                   // $legals_organizations = config('app.equivalenDocument')['AdditionalAccountID'];
                   $legals_organizations = array(
                    'Persona Jurídica y asimiladas' => 1,
		            'Persona Natural y asimiladas' => 2,
                   );
                    // Obligaciones fiscales
                   // $fiscal_obligations_code_taxlevelcode = config('app.equivalenDocument')['TaxLevelCode'];
                   $fiscal_obligations_code_taxlevelcode=array(
                    'Gran contribuyente' => 'O-13',
                    'Autorretenedor' => 'O-15',
                    'Agente de retención IVA' => 'O-23',
                    'Régimen simple de tributación' => 'O-47',
                    'Impuesto sobre las ventas' => 'O-48',
                    'No responsable de IVA' => 'O-49',
                    'No aplica' => 'R-99-PN',
                   );
                    // Tipos de atributos
                  //  $types_tribute = config('app.equivalenDocument')['PartyTaxScheme_Name'];
                  $types_tribute = array(
                    'No aplica' => 'ZZ',
                    'IVA' => '01',
                  );
                    // Formas de pago
                   // $payment_means = config('app.equivalenDocument')['PaymentMeans_ID'];
                   $payment_means = array(
                    'Contado' => 1,
	            	'Crédito' => 2,
                   );
                    // Medios de pago
                   // $payment_methods = config('app.equivalenDocument')['payment_methods'];
                     $payment_methods = array(
                        'Efectivo' => '10',
                        'Tarjeta Crédito' => '48',
                        'Tarjeta Débito' => '49',
                        'Transferencia Débito Bancaria' => '47',
                     );
                    // Notas de ajuste
                    $objNote = new stdClass();
                    $objNote->id = 100;
                    $objNote->document_number_note = "Nota test";
                    $adjustment_notes = [ $objNote ];

                    //echo '<pre>'.print_r($document_type_ds).'</pre>';
                @endphp
               

                <div class="card-body">

                    <div>
                        @php
                        if( $row->id != 0 ) {
                        @endphp
                        <a 
                            href="{{url('document/show')}}/{{$row->id}}" 
                            class="btn btn-info">
                            Líneas / Items del documento
                        </a>
                        @php
                        }
                        @endphp
                    </div>

                    <form action="{{url('/document/store')}}" method="POST" >
                        @csrf
                        <input type="hidden" 
                            name="id" id="id" 
                            value="{{$row->id}}" />

    
                        <div class="text-center font-weight-bold">
                            Información de la Compañía
                        </div>
                        <br />
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="company_id">Empresa *</label>
                                <select class="form-control" 
                                    id="company_id" name="company_id">
                                    <option value="" >Seleccione..</option>
                                    @foreach( $companies as $company )
                                    @php
                                    $sel_company_id = ( $company->id == $row->company_id )? "selected='selected'":'';
                                    @endphp
                                        <option value="{{$company->id}}" {{$sel_company_id}} >
                                            {{$company->name}}
                                        </option>  
                                    @endforeach
                                </select>
                                <input type="hidden" name="reason" 
                                    id="reason" 
                                    value="{{$row->reason}}" />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="document_type_str">
                                    Tipo de documento *
                                </label>
                                <!--
                                <input type="text" 
                                class="form-control " 
                                id="document_type_str" 
                                name="document_type_str" 
                                value=""
                                readonly 
                                placeholder="" required /> -->
                                <select 
                                    class="form-control" 
                                    id="document_type" 
                                    name="document_type"
                                    >
                                    @php
                                    foreach( $document_types_company as $key_ => $val_ ){
                                        /*
                                        $sel_document_type = ( $val_ == 1 )? "selected='selected'":'';
                                        echo '<option value="'.$val_.'" '.$sel_document_type.' >
                                                '.$key_.'
                                            </option>'; */
                                        if( $val_ == 31 ){
                                        echo '<option value="'.$val_.'" >
                                            '.$key_.'
                                        </option>';
                                        }
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="document">
                                    Número documento *
                                </label>
                                <input type="text" class="form-control" id="document" 
                                value="{{$row->document}}" 
                                readonly 
                                name="document" placeholder="" required />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="verification_digit">
                                    Digito verificación *
                                </label>
                                <input type="text" class="form-control " id="verification_digit" name="verification_digit"
                                value="{{$row->verification_digit}}" 
                                readonly 
                                placeholder="" required />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="legal_organization">
                                    Tipo organización jurídica *
                                </label>
                                <select class="form-control" 
                                    id="legal_organization" 
                                    name="legal_organization" 
                                    >
                                    @php
                                    foreach( $legals_organizations as $key_ => $val_ ){
                                        //$sel_legal_organization = ( $val_ == 1 )? "selected='selected'":'';
                                        /*
                                        echo '<option value="'.$val_.'" '.$sel_legal_organization.' >
                                                '.$key_.'
                                            </option>'; */
                                        if( $val_ == 1 ){
                                            echo '<option value="'.$val_.'" >
                                                '.$key_.'
                                            </option>';
                                        }
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="tribute">
                                    Tipo Tributo *
                                </label>
                                <select class="form-control" 
                                    id="tribute" name="tribute">
                                    @php
                                    foreach( $types_tribute as $key_ => $val_ ){
                                        $sel_tribute = ( $val_ == $row->tribute )? "selected='selected'":'';
                                        echo '<option value="'.$val_.'" '.$sel_tribute.' >
                                            '.$key_.'
                                        </option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="fiscal_obligations_code">
                                    Tipo responsabilidad fiscal *
                                </label>
                                <select class="form-control" 
                                    id="fiscal_obligations_code" name="fiscal_obligations_code[]"
                                    multiple="multiple" 
                                    disabled 
                                    >
                                    <option value="" >Seleccione..</option>
                                    @php
                                    foreach( $fiscal_obligations_code_taxlevelcode as $key_ => $val_ ){

                                        $position = strpos($row->fiscal_obligations_code, $val_);
                                        $sel_fiscal_obligations_code = ( $position === false )? '' : "selected='selected'";
  
                                        echo '<option value="'.$val_.'" '.$sel_fiscal_obligations_code.' >
                                            '.$key_.'
                                        </option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                        </div>


                        <hr />
                        <br />

                        <div class="text-center font-weight-bold">
                            Datos generales
                        </div>
                        <br />
                        <div class="form-row">
                            <!--
                            <div class="form-group col-sm-4">
                                <label for="type_code">
                                    Tipo de documento *
                                </label>                                
                                <select class="form-control" 
                                    id="type_code" name="type_code">
                                    <option value="" >Seleccione..</option>
                                    @php
                                    foreach( $document_type_ds as $key_ => $val_ ){
                                        $sel_type_code = ( $val_ == $row->type_code )? "selected='selected'":'';
                                        echo '<option value="'.$val_.'" '.$sel_type_code.' >
                                            '.$key_.'
                                        </option>';
                                    }
                                    @endphp
                                </select>
                                <input type="hidden" name="type" 
                                    id="type" 
                                    value="{{$row->type}}" /> 
                            </div> -->
                            <div class="form-group col-sm-4">
                                <label for="resolution_id">
                                    Resolución *
                                </label>
                                <select class="form-control" 
                                    id="resolution_id" name="resolution_id">
                                    <option value="" >Seleccione..</option>
                                    
                                    @foreach( $resolutions_dian as $resolu )
                                    @php 
                                    /*
                                    $sel_resolu = ( $resolu->id == $row->resolution_id )? "selected='selected'":'';
                                    echo '<option value="'.$resolu->id.'" '.$sel_resolu.' >
                                            '.$resolu->resolution.'
                                        </option> ';
                                    */
                                    @endphp
                                    @endforeach
                                    
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="document_prefix">
                                    Prefijo *
                                </label>
                                <input type="text" 
                                class="form-control " 
                                id="document_prefix" 
                                name="document_prefix" 
                                value="{{$row->document_prefix}}"
                                placeholder="" required 
                                readonly 
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="document_number">
                                    Consecutivo No. Documento
                                </label>
                                <input type="text" 
                                class="form-control " 
                                id="document_number" 
                                name="document_number" 
                                value="{{$row->document_number}}"
                                placeholder="" required 
                                readonly
                                />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="supplier_country">
                                    País *
                                </label>                                
                                <select class="form-control" 
                                    id="supplier_country" name="supplier_country">
                                    <!-- 
                                    <option value="" >Seleccione..</option> -->
                                @php
                                foreach( $countries as $key_ => $val_ ){
                                    $sel_supplier_country = ( $key_ == $row->supplier_country )? "selected='selected'":'';
                                    echo '<option value="'.$key_.'" '.$sel_supplier_country.' >
                                        '.$key_.'
                                    </option>';
                                }
                                @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="department_id">
                                    Departamento *
                                </label><br />
                                <select class="form-control" 
                                    id="department_id" name="department_id">
                                    <option value="" >Seleccione..</option>
                                @php
                                if( $row->department_id != '' ){
                                    echo '<option value="'.$row->department_id.'" selected="selected" >
                                        '.$row->department.'
                                    </option>';
                                }
                                @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="city_id">
                                    Ciudad *
                                </label><br />
                                <select class="form-control" 
                                    id="city_id" name="city_id">
                                @php
                                if( $row->city_id != '' )
                                    echo '<option value="'.$row->city_id.'" selected="selected" >
                                        '.$row->city.'
                                    </option>';
                                @endphp
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="resident">
                                    Tipo de operación:
                                </label>                                
                                <select class="form-control" 
                                    id="resident" name="resident">
                                    <!-- 
                                    <option value="" >Seleccione..</option> -->
                                @php
                                foreach( $residents as $key_ => $val_ ){
                                    $sel_resident = ( $val_ == $row->resident )? "selected='selected'":'';
                                    echo '<option value="'.$val_.'" '.$sel_resident.' >
                                        '.$key_.'
                                    </option>';
                                }
                                @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="language">
                                    Lenguaje *
                                </label>
                                <select class="form-control" 
                                    id="language" name="language">
                                    <!-- 
                                    <option value="" >Seleccione..</option> -->
                                    @php
                                    foreach( $languages as $key_ => $val_ ){
                                        $sel_language = ( $val_ == $row->language )? "selected='selected'":'';
                                        echo '<option value="'.$val_.'" '.$sel_language.' >
                                            '.$key_.'
                                        </option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="currency">
                                    Divisa del documento *
                                </label>
                                <select class="form-control" 
                                    id="currency" name="currency">
                                    <!--
                                    <option value="">Seleccione..</option>-->
                                    @php
                                    foreach( $currencies as $key_ => $val_ ){
                                        $sel_currency = ( $val_ == $row->currency )? "selected='selected'" : '';
                                        if( $val_ != 'No aplica' ){
                                            echo '<option value="'.$val_.'" '.$sel_currency.' >
                                                '.$val_.'
                                            </option>';
                                        }
                                    }
                                    @endphp
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="date_transaction">
                                    Fecha transacción *
                                </label>
                                <input type="text" class="form-control" id="date_transaction" name="date_transaction" placeholder="yyyy-mm-dd" 
                                value="{{$current_date}}" 
                                required 
                                readonly 
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="time_transaction">
                                    Hora transacción *
                                </label>
                                <input type="text" 
                                class="form-control " 
                                id="time_transaction" 
                                name="time_transaction" 
                                value="{{$current_time}}"
                                placeholder="" required 
                                readonly 
                                />
                            </div>
                        </div>
                        <div class="form-row">                            
                            <div class="form-group col-sm-12">
                                <label for="note">
                                    Nota 
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="note" 
                                    rows="1"
                                    >{{$row->note}}</textarea>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="observations">
                                    Observaciones
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="observations" 
                                    rows="2"
                                    >{{$row->observations}}</textarea>
                            </div>
                            
                            <div class="form-group col-sm-12 oculto">
                                <label for="note1">
                                    Nota 1
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="note1" 
                                    rows="1"
                                    >{{$row->note1}}</textarea>
                            </div>
                            <div class="form-group col-sm-12 oculto">
                                <label for="note2">
                                    Nota 2
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="note2" 
                                    rows="1"
                                    >{{$row->note2}}</textarea>
                            </div>
                            <div class="form-group col-sm-12 oculto">
                                <label for="note3">
                                    Nota 3
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="note3" 
                                    rows="1"
                                    >{{$row->note3}}</textarea>
                            </div>
                            <div class="form-group col-sm-12 oculto">
                                <label for="note4">
                                    Nota 4
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="note4" 
                                    rows="1"
                                    >{{$row->note4}}</textarea>
                            </div>
                            <div class="form-group col-sm-12 oculto">
                                <label for="note5">
                                    Nota 5
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="note5" 
                                    rows="1"
                                    >{{$row->note5}}</textarea>
                            </div>
                           <!-- -->
                        </div>
                        <div class="form-row oculto" >
                            <div class="form-group col-sm-4">
                                <label for="have_purchase_order">
                                    Tiene Orden *
                                </label>                                
                                <select class="form-control" 
                                    id="have_purchase_order" name="have_purchase_order">
                                    @php
                                    $have_purchase_order_ = false;
                                    foreach( $yesNo as $key_ => $val_ ){
                                        $sel_have_purchase_order = ( $val_ == $row->have_purchase_order && ! $have_purchase_order_ )? "selected='selected'":'';
                                        if( $val_ == $row->have_purchase_order ) $have_purchase_order_ = true;
                                        if( $key_ == 'No' ){
                                            echo '<option value="'.$val_.'" '.$sel_have_purchase_order.' >
                                            '.$key_.'
                                            </option>';
                                        }
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4 div_have_purchase_order">
                                <label for="order_number">
                                    No. Orden *
                                </label>
                                <input type="text" 
                                class="form-control " 
                                id="order_number" 
                                name="order_number" 
                                value="{{$row->order_number}}"
                                placeholder="" required />
                            </div>
                            <div class="form-group col-sm-4 div_have_purchase_order">
                                <label for="order_date">
                                    Fecha Orden *
                                </label>
                                <input type="text" class="form-control" id="order_date" name="order_date" placeholder="yyyy-mm-dd" 
                                value="{{$row->order_date}}" 
                                data-provide="datepicker" data-date-format="yyyy-mm-dd" required />
                            </div>
                        </div>
                        <div class="form-row oculto" >
                            <div class="form-group col-sm-4">
                                <label for="have_advance">
                                    Tiene Anticipo *
                                </label>                                
                                <select class="form-control" 
                                    id="have_advance" name="have_advance">
                                    @php
                                    $have_advance_ = false;
                                    foreach( $yesNo as $key_ => $val_ ){
                                        $sel_have_advance = ( $val_ == $row->have_advance && ! $have_advance_ )? "selected='selected'":'';
                                        if( $val_ == $row->have_advance ) $have_advance_ = true;
                                        if( $key_ == 'No' ){
                                            echo '<option value="'.$val_.'" '.$sel_have_advance.' >
                                                '.$key_.'
                                            </option>';
                                        }
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4 div_have_advance">
                                <label for="advance_number">
                                    No. Anticipo *
                                </label>
                                <input type="text" 
                                class="form-control " 
                                id="advance_number" 
                                name="advance_number" 
                                value="{{$row->advance_number}}"
                                placeholder="" required />
                            </div>
                            <div class="form-group col-sm-4 div_have_advance">
                                <label for="advance_date">
                                    Fecha Anticipo *
                                </label>
                                <input type="text" class="form-control" id="advance_date" name="advance_date" placeholder="yyyy-mm-dd" 
                                value="{{$row->advance_date}}" 
                                data-provide="datepicker" data-date-format="yyyy-mm-dd" required />
                            </div>
                        </div>



                        <hr class="oculto" />
                        <br />
                        <div class="text-center font-weight-bold oculto">
                            Nota de ajuste relacionada
                        </div>
                        <br />
                        <div class="form-row oculto">
                            <div class="form-group col-sm-4">
                                <label for="document_id">Nota de ajuste</label>
                                <select class="form-control" 
                                    id="document_id" name="document_id">
                                    <option value="" >Seleccione..</option>
                                    @php
                                    foreach( $adjustment_notes as $key_ => $val_ ){
                                        $sel_tribute = ( $val_->id == $row->document_id )? "selected='selected'":'';
                                        echo '<option value="'.$val_->id.'" '.$sel_tribute.' >
                                            '.$val_->document_number_note.'
                                        </option>';
                                    }
                                    @endphp
                                </select>
                                <input type="hidden" name="document_number_note" 
                                    id="document_number_note" 
                                    value="{{$row->document_number_note}}" 
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="date_send_ds">
                                    Fecha de generación
                                </label>
                                <input type="text" class="form-control" 
                                id="date_send_ds" 
                                name="date_send_ds" 
                                placeholder="yyyy-mm-dd" 
                                value="{{$row->date_send_ds}}" 
                                data-provide="datepicker" data-date-format="yyyy-mm-dd" 
                                required />
                            </div>
                        </div>


                        
                        <hr />
                        <br />

                        <div class="text-center font-weight-bold">
                            Información del Proveedor
                        </div>
                        <br />
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="supplier_id">Empresa *</label>
                                <select class="form-control" 
                                    id="supplier_id" name="supplier_id">
                                    <option value="" >Seleccione..</option>
                                @php
                                if( $row->supplier_id != '' ){
                                    echo '<option value="'.$row->supplier_id.'" selected="selected" >
                                        '.$row->supplier_reason.'
                                    </option>';
                                }
                                @endphp
                                </select>
                                <input type="hidden" name="supplier_reason" 
                                    id="supplier_reason" 
                                    value="{{$row->supplier_reason}}" /> 
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="supplier_document_type">
                                    Tipo de documento *
                                </label>
                                <select 
                                    class="form-control" 
                                    id="supplier_document_type" 
                                    name="supplier_document_type"
                                    >
                                    <option value="" >Seleccione..</option>
                                    @php
                                    foreach( $document_types_company as $key_ => $val_ ){
                                        $sel_document_type = ( $val_ == $row->supplier_document_type )? "selected='selected'":'';
                                        echo '<option value="'.$val_.'" '.$sel_document_type.' >
                                            '.$key_.'
                                            </option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="supplier_document">
                                    Número documento *
                                </label>
                                <input type="text" class="form-control r" id="supplier_document" 
                                value="{{$row->supplier_document}}" 
                                 
                                name="supplier_document" placeholder="" required />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="supplier_verification_digit">
                                    Digito verificación *
                                </label>
                                <input type="number" class="form-control " id="supplier_verification_digit" 
                                name="supplier_verification_digit"
                                value="{{$row->supplier_verification_digit}}" 
                                min="0" max="9"
                                placeholder="" required 
                                
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="supplier_legal_organization">
                                    Tipo organización jurídica *
                                </label>
                                <input type="text" 
                                class="form-control " 
                                id="supplier_legal_organization_str" 
                                name="supplier_legal_organization_str" 
                                value="{{$row->supplier_legal_organization}}"
                                readonly 
                                placeholder="" required />
                                <select style="display: none;"
                                    class="form-control" 
                                    id="supplier_legal_organization" 
                                    name="supplier_legal_organization"
                                    >
                                    @php
                                    foreach( $legals_organizations as $key_ => $val_ ){
                                        $sel_supplier_legal_organization = ( $val_ == 1 )? "selected='selected'":'';
                                        echo '<option value="'.$val_.'" '.$sel_supplier_legal_organization.' >
                                                '.$key_.'
                                            </option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="supplier_tribute">
                                    Tipo Tributo *
                                </label>
                                <select class="form-control" 
                                    id="supplier_tribute" name="supplier_tribute">
                                    <option value="" >Seleccione..</option>
                                    @php
                                    foreach( $types_tribute as $key_ => $val_ ){
                                        $sel_supplier_tribute = ( $val_ == $row->supplier_tribute )? "selected='selected'":'';
                                        echo '<option value="'.$val_.'" '.$sel_supplier_tribute.' >
                                            '.$key_.'
                                        </option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="supplier_fiscal_obligations_code">
                                    Tipo responsabilidad fiscal *
                                </label>
                                <select class="form-control" 
                                    id="supplier_fiscal_obligations_code" name="supplier_fiscal_obligations_code[]"
                                    multiple="multiple" 
                                    >
                                    <option value="" >Seleccione..</option>
                                    @php
                                    $fiscal_obligations_code = $row->supplier_fiscal_obligations_code;
                                    if( $fiscal_obligations_code == '' || $fiscal_obligations_code === NULL ){
                                        // Por default: 'No responsable de IVA'
                                        $fiscal_obligations_code = 'O-49';
                                    }

                                    foreach( $fiscal_obligations_code_taxlevelcode as $key_ => $val_ ){
                                        $position = strpos($fiscal_obligations_code, $val_);
                                        $sel_supplier_fiscal_obligations_code = ( $position === false )? '' : "selected='selected'";
                                        echo '<option value="'.$val_.'" '.$sel_supplier_fiscal_obligations_code.' >
                                            '.$key_.'
                                        </option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="supplier_address">
                                    Dirección *
                                </label>
                                <input type="text" class="form-control " id="supplier_address" 
                                value="{{$row->supplier_address}}" 
                                name="supplier_address" placeholder="" required />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="supplier_postal_code">
                                    Código postal
                                </label>
                                <input type="text" class="form-control " id="supplier_postal_code" 
                                value="{{$row->supplier_postal_code}}" 
                                name="supplier_postal_code" placeholder="" required />
                            </div>
                        </div>


                        <hr />
                        <br />

                        <div class="text-center font-weight-bold">
                            Información pago
                        </div>
                        <br />
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="way_pay">
                                    Forma de pago *
                                </label>
                                <select class="form-control" 
                                    id="way_pay" name="way_pay">
                                    <option value="" >Seleccione..</option>
                                    @php
                                    foreach( $payment_means as $key_ => $val_ ){
                                        $sel_way_pay = ( $val_ == $row->way_pay )? "selected='selected'":'';
                                        echo '<option value="'.$val_.'" '.$sel_way_pay.' >
                                            '.$key_.'
                                            </option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="payment_method">
                                    Medio de pago *
                                </label>
                                <select 
                                    class="form-control" 
                                    id="payment_method" 
                                    name="payment_method"
                                    >
                                    <option value="" >Seleccione..</option>
                                    @php
                                    foreach( $payment_methods as $key_ => $val_ ){
                                        $sel_payment_method = ( $val_ == $row->payment_method )? "selected='selected'":'';
                                        echo '<option value="'.$val_.'" '.$sel_payment_method.' >
                                            '.$key_.'
                                            </option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="date_due_payment">
                                    Fecha de pago 
                                </label>
                                <input type="text" class="form-control" id="date_due_payment" name="date_due_payment" placeholder="yyyy-mm-dd" 
                                value="{{$row->date_due_payment}}" 
                                data-provide="datepicker" data-date-format="yyyy-mm-dd" 
                                required />
                            </div>
                        </div>


                        <hr />
                        <br />

                        <div class="text-center font-weight-bold oculto">
                            Tasa de cambio
                        </div>
                        <br />
                        <div class="form-row oculto">
                            <div class="form-group col-sm-4">
                                <label for="base_currency_init">
                                    Divisa base del documento *
                                </label>
                                <select class="form-control" 
                                    id="base_currency_init" name="base_currency_init">
                                    <!--
                                    <option value="">Seleccione..</option>-->
                                    @php
                                    foreach( $currencies as $key_ => $val_ ){
                                        $sel_base_currency_init = ( $val_ == $row->base_currency_init )? "selected='selected'" : '';
                                        if( $val_ == 'COP' || $val_ == 'No aplica' ){
                                            echo '<option value="'.$val_.'" '.$sel_base_currency_init.' >
                                                '.$val_.'
                                            </option>';
                                        }
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="monetary_base_init">
                                    Base monetaria de la divisa extranjera
                                </label>
                                @php
                                $monetary_base_init = 1;
                                if( $row->monetary_base_init != '' && $row->monetary_base_init != NULL ){
                                    $monetary_base_init = $row->monetary_base_init;
                                }
                                @endphp
                                <input type="text" class="form-control" id="monetary_base_init" name="monetary_base_init" placeholder="" 
                                value="{{$monetary_base_init}}" 
                                required 
                                readonly  
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="base_currency_end">
                                    Divisa a convertir
                                </label>
                                <select class="form-control" 
                                    id="base_currency_end" name="base_currency_end">
                                    <!--
                                    <option value="" >Seleccione..</option> -->
                                    @php
                                    foreach( $currencies as $key_ => $val_ ){
                                        $sel_base_currency_end = ( $val_ == $row->base_currency_end )? "selected='selected'" : '';
                                        if( $val_ != 'COP' ){
                                            echo '<option value="'.$val_.'" '.$sel_base_currency_end.' >
                                                '.$val_.'
                                            </option>';
                                        }
                                    }
                                    @endphp
                                </select>
                            </div>
                        </div>
                        <div class="form-row oculto">
                            <div class="form-group col-sm-4">
                                <label for="monetary_base_end">
                                    Base monetaria para conversión
                                </label>
                                @php
                                $monetary_base_end = 1;
                                if( $row->monetary_base_end != '' && $row->monetary_base_end != NULL ){
                                    $monetary_base_end = $row->monetary_base_end;
                                }
                                @endphp
                                <input type="text" class="form-control" id="monetary_base_end" name="monetary_base_end" placeholder="" 
                                value="{{$monetary_base_end}}" 
                                required 
                                readonly  
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="exchange_rate_value">
                                    Valor tasa de cambio entre divisas
                                </label>
                                <input type="text" class="form-control" id="exchange_rate_value" name="exchange_rate_value" placeholder="" 
                                value="{{$row->exchange_rate_value}}" 
                                required 
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="date_exchange_payment">
                                    Fecha tasa de cambio
                                </label>
                                <input type="text" class="form-control" id="date_exchange_payment" name="date_exchange_payment" placeholder="" 
                                value="{{$row->date_exchange_payment}}" 
                                required 
                                data-provide="datepicker" data-date-format="yyyy-mm-dd" 
                                />
                            </div>
                        </div>


                        <hr class="oculto" />
                        <br />

                        <div class="text-center font-weight-bold oculto">
                            Tasa de cambio alternativa
                        </div>
                        <br />
                        <div class="form-row oculto">
                            <div class="form-group col-sm-4">
                                <label for="base_currency_init2">
                                    Divisa base del documento *
                                </label>
                                <select class="form-control" 
                                    id="base_currency_init2" name="base_currency_init2">
                                    <!--
                                    <option value="" >No aplica</option>-->
                                    @php
                                    foreach( $currencies as $key_ => $val_ ){
                                        $sel_base_currency_init2 = ( $val_ == $row->base_currency_init2 )? "selected='selected'" : '';
                                        if( $val_ != 'COP' ){
                                            echo '<option value="'.$val_.'" '.$sel_base_currency_init2.' >
                                                '.$val_.'
                                            </option>';
                                        }
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="monetary_base_init2">
                                    Base monetaria de la divisa extranjera
                                </label>
                                @php
                                $monetary_base_init2 = 1;
                                if( $row->monetary_base_init2 != '' && $row->monetary_base_init2 != NULL ){
                                    $monetary_base_init2 = $row->monetary_base_init2;
                                }
                                @endphp
                                <input type="text" class="form-control" id="monetary_base_init2" name="monetary_base_init2" placeholder="" 
                                value="{{$monetary_base_init2}}" 
                                required 
                                readonly  
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="base_currency_end2">
                                    Divisa a convertir
                                </label>
                                <select class="form-control" 
                                    id="base_currency_end2" name="base_currency_end2">
                                    <!--
                                    <option value="" >Seleccione..</option>-->
                                    @php
                                    foreach( $currencies as $key_ => $val_ ){
                                        $sel_base_currency_end2 = ( $val_ == $row->base_currency_end2 )? "selected='selected'" : '';
                                        if( $val_ != 'COP' ){
                                            echo '<option value="'.$val_.'" '.$sel_base_currency_end2.' >
                                                '.$val_.'
                                            </option>';
                                        }
                                    }
                                    @endphp
                                </select>
                            </div>
                        </div>
                        <div class="form-row oculto">
                            <div class="form-group col-sm-4">
                                <label for="monetary_base_end2">
                                    Base monetaria para conversión
                                </label>
                                @php
                                $monetary_base_end2 = 1;
                                if( $row->monetary_base_end2 != '' && $row->monetary_base_end2 != NULL ){
                                    $monetary_base_end2 = $row->monetary_base_end2;
                                }
                                @endphp
                                <input type="text" class="form-control" id="monetary_base_end2" name="monetary_base_end2" placeholder="" 
                                value="{{$monetary_base_end2}}" 
                                required 
                                readonly  
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="exchange_rate_value2">
                                    Valor tasa de cambio entre divisas
                                </label>
                                <input type="text" class="form-control" id="exchange_rate_value2" name="exchange_rate_value2" placeholder="" 
                                value="{{$row->exchange_rate_value2}}" 
                                required 
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="date_exchange_payment2">
                                    Fecha tasa de cambio
                                </label>
                                <input type="text" class="form-control" id="date_exchange_payment2" name="date_exchange_payment2" placeholder="" 
                                value="{{$row->date_exchange_payment2}}" 
                                required 
                                data-provide="datepicker" data-date-format="yyyy-mm-dd" 
                                />
                            </div>
                        </div>


                        <hr class="oculto" />
                        <br />

                        <!--
                        <div class="text-center font-weight-bold">
                            Valores totales
                        </div>
                        <br />
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="total_lines">
                                    Cantidad de líneas *
                                </label>
                                <input type="number" class="form-control" id="total_lines" name="total_lines" placeholder="" 
                                value="{{$row->total_lines}}" 
                                required 
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="gross_total_base_lines">
                                    Total valor bruto / Base imponible *
                                </label>
                                <input type="number" class="form-control" id="gross_total_base_lines" name="gross_total_base_lines" placeholder="" 
                                value="{{$row->gross_total_base_lines}}" 
                                required 
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="gross_total_and_tribute">
                                    Total valor bruto más tributos *
                                </label>
                                <input type="number" class="form-control" id="gross_total_and_tribute" name="gross_total_and_tribute" placeholder="" 
                                value="{{$row->gross_total_and_tribute}}" 
                                required 
                                />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="discounts_total_and_detail">
                                    Total descuentos *
                                </label>
                                <input type="number" class="form-control" id="discounts_total_and_detail" name="discounts_total_and_detail" placeholder="" 
                                value="{{$row->discounts_total_and_detail}}" 
                                required 
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="charges_total_and_detail">
                                    Total cargos *
                                </label>
                                <input type="number" class="form-control" id="charges_total_and_detail" name="charges_total_and_detail" placeholder="" 
                                value="{{$row->charges_total_and_detail}}" 
                                required 
                                />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="pay_total">
                                    Total a pagar *
                                </label>
                                <input type="number" class="form-control" id="pay_total" name="pay_total" placeholder="" 
                                value="{{$row->pay_total}}" 
                                required 
                                />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <label for="value_letters">
                                    Valor en letras:
                                </label>
                                <input type="text" 
                                class="form-control " 
                                id="value_letters" 
                                name="value_letters" 
                                value="{{$row->value_letters}}"
                                placeholder="" required />
                            </div>
                        </div>
                    -->



                        <br />
                        @php
                        if( $row->id == 0 ){
                        @endphp
                        <div class="form-row">
                            <div 
                            class="form-group col-sm-12 "
                            style="text-align: center;" 
                            >
                                <button type="button"
                                    class="btn btn-success"
                                    id="btn-save"
                                >
                                    Guardar
                                </button>
                            </div>
                        </div>
                        @php
                        }
                        else{
                            if( $row->document_number == NULL || $row->document_number == '' ){
                        @endphp
                                <div class="form-row">
                                    <div 
                                    class="form-group col-sm-6 "
                                    style="text-align: center;" 
                                    >
                                        <button type="button"
                                            class="btn btn-success"
                                            id="btn-save"
                                        >
                                            Actualizar
                                        </button>
                                    </div>
                                    <div 
                                    class="form-group col-sm-6 "
                                    style="text-align: center;" 
                                    >
                                        <a 
                                            href="{{url('document/show')}}/{{$row->id}}" 
                                            class="btn btn-info">
                                            Líneas / Items del documento
                                        </a>
                                    </div>
                                </div>
                        @php
                            }
                        }
                        @endphp
                        <br />
                        <br />
                    </form>
                    <br />
                    <br />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script type="text/javascript">

let urlBase_ = "{{ config('app.getUrlBase') }}"
let divMessages
let suppliersList

// Objeto Global con todos los inputs
let inp = {}

// ==========================
// Carga de "maestras" necesarias en JS 
@php
echo "const document_type_ds_ = " . json_encode($document_type_ds) . ";\n";
echo "const document_types_company_ = " . json_encode($document_types_company) . ";\n";
echo "const resolutions_dian_ = " . json_encode($resolutions_dian) . ";\n";
echo "const companies_ = " . json_encode($companies) . ";\n";
echo "const companies_conf_ = " . json_encode($companies_conf) . ";\n";
echo "const legals_organizations_ = " . json_encode($legals_organizations) . ";\n";
echo "const fiscal_obligations_code_taxlevelcode_ = " . json_encode($fiscal_obligations_code_taxlevelcode) . ";\n";
@endphp



const getHTML = ( id_, type = 'id' ) => {
    if( type == 'id' )
        return document.getElementById( id_ ) 
    else
        return document.querySelectorAll(`${id_}`)
}


/**
 * Funcion que valida INPUTS
 * @param {Object} inp_ Object html
 * @param {String} type_ Type input (string/number)
 * @param {Boolean} required_ Boolean
 * @param {number} min Optional to number, default = 0
 * @returns {Boolean}
 **/
const validateData = ( inp_, type_, required_, min=0 ) => {
    let val_ = inp_
    if( type_ == 'string' )
    {
        val_ = val_ + ''
        if( required_ ){
            if( val_ === null || val_ == '' || val_.trim() == '' )
                return false
        }
        return true
    }
    else{ // number
        if( isNaN( val_ ) )
            return false
        if( required_ ){
            if( val_ === null || val_ == '' || val_.trim() == '' || val_ < min )
            return false
        }
        return true
    }
}


// Nuestra compañia
const setDocumentTypeMyCompany = ( id_ ) => {
    let i, strDocType = ''
    for( i in document_types_company_ ){
        let docType = document_types_company_[ i ]
        if( docType == id_ ) strDocType = i
    }
    if( strDocType != '' ){
        inp.document_type.value = id_
        //inp.document_type_str.value = strDocType
    }
    else{
        inp.document_type.value = ''
        //inp.document_type_str.value = ''
    }
}

// Tipo Doc del Proveedor
const setDocumentTypeCompanySupplier = ( id_ ) => {

    let have_id_ = ( id_ != '' && id_ !== null ) ? true : false
    let existInTypes_ = false
    if( have_id_ ){ // El ID a buscar existe entre los Validos ?
        let k
        for( k in document_types_company_ ){
            if( document_types_company_[ k ] == id_ ) existInTypes_ = true
        }
    }

    let i, optsHtml = '<option value="" >Seleccione..</option>'
    for( i in document_types_company_ ){
        let docType = document_types_company_[ i ]
        if( have_id_ && existInTypes_ )
        {
            if( docType == id_ )
                optsHtml += `<option value="${docType}" >${i}</option>`
        }
        else{
            optsHtml += `<option value="${docType}" >${i}</option>`
        }
    }
    inp.supplier_document_type.innerHTML = optsHtml
    if( have_id_ && existInTypes_ )
        inp.supplier_document_type.value = id_
    else
        inp.supplier_document_type.value = ''
}


// Descripcion Nota de Ajuste
const setAdjustmentNote = ( document_id_ ) => {
    let text_ = ''
    if( inp.document_id.value != '' ){
        text_ = inp.document_id.options[ inp.document_id.selectedIndex ].text;
    }
    console.log({text_})
    inp.document_number_note.value = text_
}


const setDataResolution = ( company_id_ ) => {
    if( company_id_ != -1 )
    {
        let optsDefault = '<option value="" >Seleccione..</option>'
        let optsHtml = '';
        let row_ = null, i
        for( i in resolutions_dian_ ){
            let resolD_ = resolutions_dian_[ i ]
            if( resolD_.company_id == company_id_ 
                && resolD_.active == 1
            ){
                row_ = resolD_
                optsHtml += `<option value="${resolD_.id}" >
                    ${resolD_.resolution}
                </option>`
            }
        }

        
        if( row_ !== null ){
            inp.resolution_id.innerHTML = optsHtml
            console.log({ resolutionn: row_ })
            inp.resolution_id.value = row_.id
            inp.document_prefix.value = row_.prefix
            //inp.document_number.value = + row_.current + 1
            inp.document_number.value = ''
        }
        else{
            inp.resolution_id.innerHTML = optsDefault + optsHtml
            inp.resolution_id.value = ''
            inp.document_prefix.value = ''
            inp.document_number.value = ''
        }
    }
    else{
        inp.resolution_id.value = ''
        inp.document_prefix.value = ''
        inp.document_number.value = ''
    }
}


const setDataFiscalObligationsMyCompany = ( nit_ ) => {
    let i, comp_ = null
    for( i in companies_conf_ ){
        let infoComp_ = companies_conf_[ i ]
        if( i == nit_ ) comp_ = infoComp_
    }

    let arrTaxLevelCodes_ = ( comp_ !== null ) ? comp_['TaxLevelCodes'] : []
    let optsHtml_ = '<option value="" >Seleccione..</option>'

    let j
    for( j in fiscal_obligations_code_taxlevelcode_ ){ // Lista completa
        let fisObli_ = fiscal_obligations_code_taxlevelcode_[ j ]
        let k, selectOn = false
        for( k in arrTaxLevelCodes_ ){ // Las de mi Compania
            let taxLevelCodeMy_ = arrTaxLevelCodes_[ k ]
            if( fisObli_ == taxLevelCodeMy_ ) selectOn = true
        }

        let seleted_ = ( selectOn ) ? "selected='selected'" : ''
        optsHtml_ += `<option value="${fisObli_}" ${seleted_} >${j}</option>`
    }
    
    $('#fiscal_obligations_code').html(optsHtml_).trigger( 'change' )

    // Set Tribute
    if( comp_ !== null ){
        inp.tribute.value = comp_['typeTribute']
    }
}

const setDataCompany = ( company_id_ ) => {
    let comp = null, i
    for( i in companies_ ){
        if( companies_[ i ].id == company_id_ ) comp = companies_[ i ]
    }
    if( comp !== null ){
        inp.company_id.value = comp.id
        inp.reason.value = comp.name
        inp.document.value = comp.nit
        inp.verification_digit.value = comp.verification_digit
        
        // Tipo Doc Empresa-nuestra
        setDocumentTypeMyCompany( 31 )

        setDataResolution( comp.id )

        setDataFiscalObligationsMyCompany( comp.nit )
    }
    else{
        inp.company_id.value = ''
        inp.reason.value = ''
        inp.document.value = ''
        inp.verification_digit.value = ''
        
        // Tipo Doc Empresa-nuestra
        setDocumentTypeMyCompany( -1 )

        setDataResolution( -1 )

        setDataFiscalObligationsMyCompany( -1 )
    }
}


const setDataSupplier = ( supplier_id_ ) => {

    let row_ = null, i
    for( i in suppliersList ){
        if( suppliersList[ i ].id == supplier_id_ )
            row_ = suppliersList[ i ]
    }
    console.log({supplier_id_})
    if( row_ !== null ){
        // Nombre/Razon social
        inp.supplier_reason.value = row_.text
        // Digito verificacion
        const dig_ = row_.nit.substr( (row_.nit.length-1), 1 )
        // Nit
        const nit_ = row_.nit.substr( 0, (row_.nit.length-1) )

        inp.supplier_document.value = nit_
        inp.supplier_verification_digit.value = dig_

        // Set tipo Documento Proveedor
        setDocumentTypeCompanySupplier( row_.document_type )

        // Tipo Persona: Natural - Juridica
        let j, type_legal_org = ''
        for( j in legals_organizations_ ){ // Natural - Juridica
            let legOrg = legals_organizations_[ j ]
            if( row_.document_type == 31 && legOrg == 1 ){
                type_legal_org = j
            }
            else if( row_.document_type != 31 && legOrg == 2 ){
                type_legal_org = j
            }
        }
        if( row_.document_type == 31 ){
            inp.supplier_legal_organization_str.value = type_legal_org
        }
        else if( row_.document_type != '' && row_.document_type !== null ){
            inp.supplier_legal_organization_str.value = type_legal_org
        }
        else{
            inp.supplier_legal_organization_str.value = ''
        }
    }
    else{
        inp.supplier_reason.value = ''
        inp.supplier_document_type.value = ''
        inp.supplier_document.value = ''
        //inp.supplier_legal_organization.value = ''
        inp.supplier_legal_organization_str.value = ''
    }
}

const validateHaveOrderAdvance = ( have_, type_have_ ) => {
    const dysplay_ = ( have_ == 1 ) ? '' : 'none'
    const inputs_ = getHTML(`.${type_have_}`, 'class')
    for( let inp_ of inputs_ ){
        inp_.style.display = dysplay_
    }
}



const saveData = () => {

    const id = inp.id.value
    // Información de la Compañía
    const company_id = inp.company_id.value
    const reason = inp.reason.value
    const document_type = inp.document_type.value
    const document = inp.document.value
    const verification_digit = inp.verification_digit.value
    const legal_organization = inp.legal_organization.value
    const tribute = inp.tribute.value
    const fiscal_obligations_code = inp.fiscal_obligations_code.val().join(';')
    // Datos generales
    const resolution_id = inp.resolution_id.value
    const document_prefix = inp.document_prefix.value
    const supplier_country = inp.supplier_country.value
    const department_id = inp.department_id.value
    const city_id = inp.city_id.value
    const resident = inp.resident.value
    const language = inp.language.value
    const document_number = inp.document_number.value
    const date_transaction = inp.date_transaction.value
    const time_transaction = inp.time_transaction.value
    const currency = inp.currency.value
    const note = inp.note.value
    const observations = inp.observations.value
    const note1 = inp.note1.value
    const note2 = inp.note2.value
    const note3 = inp.note3.value
    const note4 = inp.note4.value
    const note5 = inp.note5.value

    const document_id = inp.document_id.value
    const document_number_note = inp.document_number_note.value
    const date_send_ds = inp.date_send_ds.value

    const have_purchase_order = inp.have_purchase_order.value
    const order_number = inp.order_number.value
    const order_date = inp.order_date.value
    const have_advance = inp.have_advance.value
    const advance_number = inp.advance_number.value
    const advance_date = inp.advance_date.value
    // Información del Proveedor
    const supplier_id = inp.supplier_id.value
    const supplier_reason = inp.supplier_reason.value
    const supplier_document_type = inp.supplier_document_type.value
    const supplier_document = inp.supplier_document.value
    const supplier_verification_digit = inp.supplier_verification_digit.value
    const supplier_legal_organization = inp.supplier_legal_organization.value
    const supplier_tribute = inp.supplier_tribute.value
    const supplier_fiscal_obligations_code = inp.supplier_fiscal_obligations_code.val().join(';')
    const supplier_address = inp.supplier_address.value
    const supplier_postal_code = inp.supplier_postal_code.value
    // Información pago
    const way_pay = inp.way_pay.value
    const payment_method = inp.payment_method.value
    const date_due_payment = inp.date_due_payment.value
    // Tasa de cambio
    const base_currency_init = inp.base_currency_init.value
    const monetary_base_init = inp.monetary_base_init.value
    const base_currency_end = inp.base_currency_end.value
    const monetary_base_end = inp.monetary_base_end.value
    const exchange_rate_value = inp.exchange_rate_value.value
    const date_exchange_payment = inp.date_exchange_payment.value
    // Tasa de cambio alternativa
    const base_currency_init2 = inp.base_currency_init2.value
    const monetary_base_init2 = inp.monetary_base_init2.value
    const base_currency_end2 = inp.base_currency_end2.value
    const monetary_base_end2 = inp.monetary_base_end2.value
    const exchange_rate_value2 = inp.exchange_rate_value2.value
    const date_exchange_payment2 = inp.date_exchange_payment2.value
    // Valores totales
    /*
    const total_lines = inp.total_lines.value
    const gross_total_base_lines = inp.gross_total_base_lines.value
    const gross_total_and_tribute = inp.gross_total_and_tribute.value
    const discounts_total_and_detail = inp.discounts_total_and_detail.value
    const charges_total_and_detail = inp.charges_total_and_detail.value
    const pay_total = inp.pay_total.value
    const value_letters = inp.value_letters.value 
    */

    let errors = []
    if( ! validateData( company_id, 'string', true, 0 ) ){
        errors.push( 'Debe seleccionar una compañía' )
    }
    if( ! validateData( tribute, 'string', true, 0 ) ){
        errors.push( 'Debe seleccionar un tipo de tributo para la compañía' )
    }
    if( ! validateData( fiscal_obligations_code, 'string', true, 0 ) ){
        errors.push( 'Debe seleccionar mínimo una opción en Obligaciones fiscales de la compañía' )
    }

    if( ! validateData( resolution_id, 'number', true, 1 ) ){
        errors.push( 'Debe seleccionar una resolución' )
    }
    if( ! validateData( supplier_country, 'string', true, 0 ) ){
        errors.push( 'Debe seleccionar un país' )
    }
    if( ! validateData( department_id, 'number', true, 1 ) ){
        errors.push( 'Debe seleccionar un departamento' )
    }
    if( ! validateData( city_id, 'number', true, 1 ) ){
        errors.push( 'Debe seleccionar una ciudad' )
    }
    if( ! validateData( resident, 'number', true, 1 ) ){
        errors.push( 'Debe seleccionar un tipo de operación' )
    }
    if( ! validateData( language, 'string', true, 0 ) ){
        errors.push( 'Debe seleccionar un lenguaje' )
    }

    if( ! validateData( supplier_id, 'string', true, 0 ) ){
        errors.push( 'Debe seleccionar un proveedor' )
    }
    if( ! validateData( supplier_document_type, 'number', true, 1 ) ){
        errors.push( 'Debe seleccionar un tipo de documento para el proveedor' )
    }
    if( ! validateData( supplier_tribute, 'string', true, 0 ) ){
        errors.push( 'Debe seleccionar un tipo de tributo para el proveedor' )
    }
    if( ! validateData( supplier_fiscal_obligations_code, 'string', true, 0 ) ){
        errors.push( 'Debe seleccionar mínimo una opción en Obligaciones fiscales del proveedor' )
    }
    if( ! validateData( supplier_address, 'string', true, 0 ) ){
        errors.push( 'Debe ingresar la dirección' )
    }

    if( ! validateData( way_pay, 'number', true, 1 ) ){
        errors.push( 'Debe seleccionar una forma de pago' )
    }
    if( ! validateData( payment_method, 'number', true, 1 ) ){
        errors.push( 'Debe seleccionar un método de pago' )
    }

    console.log({errors})
    if( errors.length != 0 ){
        let i, html_ = ''
        for( i in errors ){
            html_ += `<p>* ${errors[i]}</p>`
        }
        divMessages.innerHTML = html_
        $('#modal-messages').modal('show')
        return
    }
    
    const data = {
        id, 
        company_id, 
        reason, 
        document_type, 
        document, 
        verification_digit, 
        legal_organization, 
        tribute, 
        fiscal_obligations_code, 
        //
        resolution_id, 
        document_prefix, 
        supplier_country, 
        department_id, 
        city_id, 
        resident, 
        language, 
        document_number, 
        date_transaction, 
        time_transaction, 
        currency, 
        note, 
        observations, 
        note1, 
        note2, 
        note3, 
        note4, 
        note5, 

        document_id, 
        document_number_note, 
        date_send_ds, 
        have_purchase_order, 
        order_number, 
        order_date, 
        have_advance, 
        advance_number, 
        advance_date, 
        // 
        supplier_id, 
        supplier_reason, 
        supplier_document_type, 
        supplier_document, 
        supplier_verification_digit, 
        supplier_legal_organization, 
        supplier_tribute, 
        supplier_fiscal_obligations_code, 
        supplier_address, 
        supplier_postal_code, 
        // 
        way_pay,
        payment_method,
        date_due_payment,
        // 
        base_currency_init, 
        monetary_base_init, 
        base_currency_end, 
        monetary_base_end, 
        exchange_rate_value, 
        date_exchange_payment, 
        base_currency_init2, 
        monetary_base_init2, 
        base_currency_end2, 
        monetary_base_end2, 
        exchange_rate_value2, 
        date_exchange_payment2, 
        /*
        total_lines, 
        gross_total_base_lines, 
        gross_total_and_tribute, 
        discounts_total_and_detail, 
        charges_total_and_detail, 
        pay_total, 
        value_letters, */
    }
    console.log({ data })
    
    $.ajax({
        data,
        url: urlBase_ + '/api/document/store',
        type:'POST',
        dataType :'JSON',
        success: function( resp ){ console.log('Server resp;', resp )
            if( resp.error ){
                divMessages.innerHTML = `<p>${resp.message}</p>`
                $('#modal-messages').modal('show')
            }
            else{
                //window.location.href = urlBase_ + '/document'
                window.location.href = urlBase_ + '/document/' + resp.data.id
            }
        }
    });
}



// Esperar que la página cargue totalmente
$(document).ready(function () {

    divMessages = getHTML('div-messages')

    inp.id = getHTML('id')
    //inp.type_code = getHTML('type_code');inp.type = getHTML('type')
    inp.resolution_id = getHTML('resolution_id')
    //inp.resolution = getHTML('resolution')
    inp.department_id = getHTML('department_id')
    inp.city_id = getHTML('city_id')

    inp.company_id = getHTML('company_id')
    inp.reason = getHTML('reason')
    inp.document_type = getHTML('document_type')  
    //inp.document_type_str = getHTML('document_type_str') 
    inp.document = getHTML('document')
    inp.verification_digit = getHTML('verification_digit')
    inp.legal_organization = getHTML('legal_organization')
    inp.tribute = getHTML('tribute') 
    inp.document_prefix = getHTML('document_prefix') 

    inp.fiscal_obligations_code = $('#fiscal_obligations_code')
    inp.language = getHTML('language')

    inp.resident = getHTML('resident')
    inp.environment = getHTML('environment')
    inp.document_number = getHTML('document_number')
    inp.date_transaction = getHTML('date_transaction')
    inp.time_transaction = getHTML('time_transaction')
    inp.note = getHTML('note')
    inp.observations = getHTML('observations')
    inp.note1 = getHTML('note1')
    inp.note2 = getHTML('note2')
    inp.note3 = getHTML('note3')
    inp.note4 = getHTML('note4')
    inp.note5 = getHTML('note5')
    inp.currency = getHTML('currency')

    inp.document_id = getHTML('document_id')
    inp.document_number_note = getHTML('document_number_note')
    inp.date_send_ds = getHTML('date_send_ds')

    inp.have_purchase_order = getHTML('have_purchase_order')
    inp.order_number = getHTML('order_number')
    inp.order_date = getHTML('order_date')
    inp.have_advance = getHTML('have_advance')
    inp.advance_number = getHTML('advance_number')
    inp.advance_date = getHTML('advance_date')
    
    inp.supplier_id = getHTML('supplier_id')
    inp.supplier_reason = getHTML('supplier_reason')
    inp.supplier_document_type = getHTML('supplier_document_type')
    inp.supplier_document = getHTML('supplier_document')
    inp.supplier_verification_digit = getHTML('supplier_verification_digit')
    inp.supplier_legal_organization = getHTML('supplier_legal_organization') 
    inp.supplier_legal_organization_str = getHTML('supplier_legal_organization_str')
    inp.supplier_tribute = getHTML('supplier_tribute')
    inp.supplier_fiscal_obligations_code = $('#supplier_fiscal_obligations_code')
    inp.supplier_country = getHTML('supplier_country')
    inp.supplier_postal_code = getHTML('supplier_postal_code')
    inp.supplier_address = getHTML('supplier_address')

    inp.way_pay = getHTML('way_pay')
    inp.payment_method = getHTML('payment_method')
    inp.date_due_payment = getHTML('date_due_payment')
    
    inp.base_currency_init = getHTML('base_currency_init')
    inp.monetary_base_init = getHTML('monetary_base_init')
    inp.base_currency_end = getHTML('base_currency_end')
    inp.monetary_base_end = getHTML('monetary_base_end')
    inp.exchange_rate_value = getHTML('exchange_rate_value')
    inp.date_exchange_payment = getHTML('date_exchange_payment')
    inp.base_currency_init2 = getHTML('base_currency_init2')
    inp.monetary_base_init2 = getHTML('monetary_base_init2')
    inp.base_currency_end2 = getHTML('base_currency_end2')
    inp.monetary_base_end2 = getHTML('monetary_base_end2')
    inp.exchange_rate_value2 = getHTML('exchange_rate_value2')
    inp.date_exchange_payment2 = getHTML('date_exchange_payment2')
    
    /*
    inp.value_letters = getHTML('value_letters')
    inp.total_lines = getHTML('total_lines')
    inp.gross_total_base_lines = getHTML('gross_total_base_lines')
    inp.gross_total_and_tribute = getHTML('gross_total_and_tribute')
    inp.discounts_total_and_detail = getHTML('discounts_total_and_detail')
    inp.charges_total_and_detail = getHTML('charges_total_and_detail')
    inp.pay_total = getHTML('pay_total')
    */


    inp.company_id.addEventListener('change', ( ev ) => {
        setDataCompany( ev.target.value )
    })

    // Multiple select (Responsabilidades fiscales Compañia)
    $('#fiscal_obligations_code').select2()

    // Multiple select
    $('#supplier_fiscal_obligations_code').select2()


    // Departamentos -------------------------------------
    $('#department_id').select2({
        language: 'es',
        ajax: {
            url: urlBase_ + '/api/document/search-depto',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    depto: params.term, // search term
                    //page: params.page
                };
            },
            processResults: function (data, params) {
                // limpio Select de Ciudades..
                $('#city_id').val( null ).trigger( 'change' )
                //params.page = params.page || 1;
                return {
                    results: data.data,
                    /*
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    } */
                }
            },
            //cache: true
        },  
        placeholder: 'buscar...',
        minimumInputLength: 2,
        //templateResult: formatRepo,
        //templateSelection: formatRepoSelection
    });

    // Ciudades -------------------------------------
    $('#city_id').select2({
        language: 'es',
        ajax: {
            url: urlBase_ + '/api/document/search-city',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    city: params.term,
                    depto: inp.department_id.value,
                };
            },
            processResults: function (data, params) {
                return {
                    results: data.data,
                }
            },
        },  
        placeholder: 'buscar...',
        minimumInputLength: 2,
    });

    // Nota ajuste relacionada
    inp.document_id.addEventListener('change', ( ev ) => {
        setAdjustmentNote( ev.target.value )
    })

    // Proveedores -------------------------------------
    $('#supplier_id').select2({
        language: 'es',
        ajax: {
            url: urlBase_ + '/api/document/search-supplier',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    query_: params.term,
                };
            },
            processResults: function (resp, params) {
                if( resp.error ){
                    divMessages.innerHTML = `<p>${resp.message}</p>`
                    $('#modal-messages').modal('show')
                    return
                }
                suppliersList = resp.data
                return {
                    results: resp.data,
                }
            },
        },  
        placeholder: 'buscar...',
        minimumInputLength: 2,
    });
    // Forma de detectar una "seleccion" con select2:
    // Al seleccionar un Proveedor
    $('#supplier_id').on("select2:select", function (e) {
        setDataSupplier( e.params.data.id )
    });


    // Tiene Ordenes
    inp.have_purchase_order.addEventListener('change', ( ev ) => {
        validateHaveOrderAdvance( ev.target.value, 'div_have_purchase_order' )
    })
    // Tiene Anticipos
    inp.have_advance.addEventListener('change', ( ev ) => {
        validateHaveOrderAdvance( ev.target.value, 'div_have_advance' )
    })




    const btnSave = getHTML('btn-save');
    if( btnSave !== null ){
        btnSave.addEventListener('click', () => {
            saveData()
        })
    }


    // Ejecuciones -------------------------------------
    @php
    if( $row->id != 0 ){
        echo 'let company_id_ = "' . $row->company_id . '"' . "\n";
        echo 'let haveOrder_ = "' . $row->have_purchase_order . '"' . "\n";
        echo 'let haveAdvance_ = "' . $row->have_advance . '"' . "\n";
    @endphp


    //setDataCompany( company_id_ ) // Obligaria a las Oblig Fiscal Parametrizadas x Empresa !!!
    setDataResolution( company_id_ )
    validateHaveOrderAdvance( haveOrder_, 'div_have_purchase_order' )
    validateHaveOrderAdvance( haveAdvance_, 'div_have_advance' )

    @php
    }
    @endphp
    

});






</script>

@endsection
