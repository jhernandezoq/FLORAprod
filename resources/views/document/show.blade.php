@extends('layouts.app')

@section('content')

<div class="container invoice-area">

    <!-- Modal Mensajes Error -->
    <div class="modal fade" role="dialog" id="modal-messages">
        <div class="modal-dialog">
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

    <!-- Modal Update Ok -> Cadena -->
    <div class="modal fade" role="dialog" id="modal-cadena">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header-ok">
                    <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
                    <h4 class="modal-title" style="text-align: center;">
                        Atención
                    </h4>
                </div>
                <div class="modal-body"  >
                    <p class="alert alert-info" >
                        Información actualizada
                    </p>
                    <p id="div-cadena"></p>
                    <p id="div-response-cadena"></p>
                </div>
                <div class="modal-footer">
                    <button 
                        type="button" 
                        class="btn btn-success"
                        id="btn-send"
                        >
                        <i class="fas fa-check"></i> 
                        Enviar a CADENA
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Borrar -->
    <div class="modal fade" role="dialog" id="modal-confirm-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header-ok">
                    <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
                    <h4 class="modal-title" style="text-align: center;">
                        Atención
                    </h4>
                </div>
                <div class="modal-body" id="div-messages" >
                    Está seguro de borrar el registro ?
                </div>
                <div class="modal-footer">
                    <button type="button" 
                        class="btn btn-success" 
                        id="btn-del-ok-documents-lines"
                        data-id="0"
                        >
                        Borrar el registro
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje alerta -->
    @php
    /*if( $row->document_number == NULL || $row->document_number == '' ){
        $row->document_number = '';
    }*/

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
                    Documento No. {{$row->document_number}}
                    <input type="hidden" name="document_id" 
                        id="document_id" 
                        value="{{$row->id}}" />
                </div>
                <div class="card-body">
                    <div>
                        <a href="{{url('document/')}}/{{$row->id}}" 
                            class="btn btn-info">
                                Volver
                        </a>
                    </div>
                    
                        <div class="div-table-responsive">
                            <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                    <th>ID</th>
                                    <th>Compañía</th>
                                    <th>Proveedor</th>
                                    <th>NIT Proveedor</th>
                                    <th>Resolución</th>
                                    <th>Estado</th>
                                    <th>Documento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$row->id}}</td>
                                        <td>{{$row->company}}</td>
                                        <td>{{$row->supplier_reason}}</td>
                                        <td>{{$row->supplier_document}}</td>
                                        <td>{{$row->resolution}}</td>
                                        <td>{{$row->status}}</td>
                                        <td>{{$row->document_number}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        @php 
                            //echo '<pre>'.print_r($rowsDiscountChargeTax).'</pre>';

                            $yesNo = array(
                                    'Seleccione..' => '',
                                    'Si' => 1,
                                    'No' => 0
                            );

                            $quantity_units = array(
                                '94 - Unidad' => 70
                            );
                            // Cargo/Descuento/Impuesto
                            $types_discounts_charge_tax = array(
                                'Impuesto',
                                'Cargo',
                                'Descuento'
                            );
                            // Codigos Impuesto
                            $types_tax = array(
                                'IVA' => '01',
                                'ReteIVA' => '05',
                                'ReteRenta' => '06'
                            );
                            // Porcentajes IVA
                            $percentageIvas = array(
                                0, 5, 19
                            );
                            // Porcentajes ReteRenta
                            $percentageReteRentas = array(
                                0, 
                                0.1, 
                                0.5, 
                                1,
                                1.5,
                                2,
                                2.5,
                                3,
                                3.5,
                                4,
                                6,
                                7,
                                10,
                                11,
                                15,
                                20,
                                33
                            );

                        @endphp

                        <!-- ============================ 

                        -->
                        <hr />
                        <br />
                        <h4>Líneas / Items</h4>
                        <div class="form-row">
                            <div 
                            class="form-group col-sm-6 "
                            style="text-align: left;" 
                            >
                                <button type="button"
                                    class="btn btn-success"
                                    id="btn-show-documents-lines"
                                >
                                <i class="fas fa-arrow-down text-white"></i>
                                <i class="fas fa-arrow-up text-white"></i>
                                </button>
                            </div>
                        </div>

                        <br />
                        <div id="form-documents-lines" style="display: none;">
                            <form  method="POST" >
                                <input type="hidden" name="id_documents_lines" 
                                    id="id_documents_lines" 
                                    value="0" />
                                <div class="form-row">
                                    <div class="form-group col-sm-4">
                                        <label for="item_description_documents_lines">
                                            Descripción *
                                        </label>
                                        <input type="text" class="form-control " 
                                        id="item_description_documents_lines" 
                                        value="" 
                                        name="item_description_documents_lines" 
                                        placeholder="" required 
                                        />
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="quantity_unit_id_documents_lines">
                                            Unidad medida *
                                        </label>
                                        <br />
                                        <select class="form-control" 
                                            id="quantity_unit_id_documents_lines" name="quantity_unit_id_documents_lines"
                                            style="width: 100%;">
                                            <!--
                                            <option value="" >Seleccione..</option> -->
                                            @php
                                            foreach( $quantity_units as $key_ => $val_ ){
                                                echo '<option value="'.$val_.'" >
                                                    '.$key_.'
                                                </option>';
                                            }
                                            @endphp
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-4" >
                                        <label for="quantity_documents_lines">
                                            Cantidad *
                                        </label>
                                        <input type="number" class="form-control" 
                                        id="quantity_documents_lines" 
                                        value="" 
                                        name="quantity_documents_lines" 
                                        placeholder="" required />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm-4" >
                                        <label for="price_documents_lines">
                                            Precio *
                                        </label>
                                        <input type="number" class="form-control " 
                                        id="price_documents_lines" 
                                        value="" 
                                        name="price_documents_lines" 
                                        placeholder="" required 
                                        />
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="total_documents_lines">
                                            Total *
                                        </label>
                                        <input type="number" class="form-control " 
                                        id="total_documents_lines" 
                                        value="" 
                                        name="total_documents_lines" 
                                        placeholder="" required 
                                        readonly 
                                        />
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="perc_discount_documents_lines">
                                            % Descuento
                                        </label>
                                        <input type="number" class="form-control" 
                                        id="perc_discount_documents_lines" 
                                        value="0" 
                                        name="perc_discount_documents_lines" 
                                        placeholder="" required />
                                    </div>
                                    <div class="form-group col-sm-4 oculto">
                                        <label for="perc_charge_documents_lines">
                                            % Recargo
                                        </label>
                                        <input type="number" class="form-control" 
                                        id="perc_charge_documents_lines" 
                                        value="0" 
                                        name="perc_charge_documents_lines" 
                                        placeholder="" required />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm-4 oculto">
                                        <label for="code_discounts_charge_tax_documents_lines">
                                            Impuesto 
                                        </label>
                                        <select 
                                            class="form-control" 
                                            id="code_discounts_charge_tax_documents_lines" 
                                            name="code_discounts_charge_tax_documents_lines"
                                            >
                                            <option value="">Seleccione..</option>
                                            @php
                                            foreach( $types_tax as $key_ => $val_ ){
                                                echo '<option value="'.$val_.'" >
                                                    '.$key_.'
                                                </option>';
                                            }
                                            @endphp
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="perc_tax_documents_lines">
                                            % IVA 
                                        </label>
                                        <br />
                                        <select class="form-control" 
                                            id="perc_tax_documents_lines" 
                                            name="perc_tax_documents_lines"
                                            style="width: 100%;">
                                            @php
                                            foreach( $percentageIvas as $key_ => $val_ ){
                                                echo '<option value="'.$val_.'" >
                                                    '.$val_.'
                                                </option>';
                                            }
                                            @endphp
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="perc_reterenta_documents_lines">
                                            % ReteRenta 
                                        </label>
                                        <br /> 
                                        <select class="form-control" 
                                            id="perc_reterenta_documents_lines" 
                                            name="perc_reterenta_documents_lines"
                                            style="width: 100%;">
                                            @php
                                            foreach( $percentageReteRentas as $key_ => $val_ ){
                                                echo '<option value="'.$val_.'" >
                                                    '.$val_.'
                                                </option>';
                                            }
                                            @endphp
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm-12">
                                        <button type="button"
                                            class="btn btn-success"
                                            id="btn-save-documents-lines"
                                        >
                                            Guardar nuevo registro 
                                        </button>
                                        <button type="button"
                                            class="btn btn-outline-secondary"
                                            id="btn-clear-documents-lines"
                                            style="display: none;"
                                        >
                                            Limpiar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="div-table-responsive">
                            <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Código</th>
                                        <!--
                                        <th class="text-center">ID</th> -->
                                        <th class="text-center">Descripción</th>
                                        <th class="text-center">Unidad</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Precio</th>
                                        <th class="text-center">Total bruto</th>
                                        <!-- 
                                        <th class="text-center">Recargo</th> -->
                                        <th class="text-center">Descuento</th>
                                        <th class="text-center">Subtotal</th>
                                        <th class="text-center">Retención</th>
                                        <th class="text-center">Impuesto</th>
                                        <th class="text-center" colspan="2">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody id="table-documents-lines">
                                    @php
                                    $line = 0;
                                    // Sumatorias
                                    $total_gross = 0;
                                    $sum_subtotal_charges_discount = 0;
                                    $sum_tt_charge = 0;
                                    $sum_tt_discount = 0;
                                    $sum_base_impo_ivas = 0;
                                    $sum_tt_ivas = 0;
                                    $sum_tt_retes = 0;
                                    $sum_tt_ivas_html = '';
                                    $sum_tt_retes_html = '';
                                    @endphp
                                    @foreach( $rowsDocumentLine as $item )
                                    @php
                                    $line++;

                                    // Totales brutos ---------------------------------
                                    $total_gross = $total_gross + $item->total;

                                    // Recargo        ---------------------------------
                                    $sum_charge = 0;

                                    // Descuento --------------------------------------
                                    $sum_discount = 0;
                                    if( $item->perc_discount != NULL 
                                        && $item->perc_discount != 0 
                                        && $item->perc_discount != '' ){
                                        $sum_discount = ($item->total * $item->perc_discount) / 100;

                                        $sum_tt_discount = $sum_tt_discount + $sum_discount;
                                    }

                                    $total_charge_discount = ($item->total + $sum_charge) - $sum_discount;

                                    $sum_subtotal_charges_discount = $sum_subtotal_charges_discount + $total_charge_discount;


                                    // Tipo de Impuesto: ------------------------------
                                    $info_tax = '0 %';
                                    if( $item->perc_tax != 0 ){
                                        $info_tax = 'IVA - '.$item->perc_tax .' %';
                                        // Calculo despues de aplicar Cargos/Descuento al Bruto
                                        $value_tax = ($total_charge_discount * $item->perc_tax) / 100;

                                        $sum_tt_ivas = $sum_tt_ivas + $value_tax;

                                        $sum_base_impo_ivas = $sum_base_impo_ivas + $total_charge_discount;

                                        $sum_tt_ivas_html = $sum_tt_ivas_html . '<tr>
                                            <td width="50%">
                                            '. $info_tax . '</td><td>
                                            '. number_format($value_tax, 0,',','.')
                                            .' </td></tr>';
                                    }
                                    $info_reterenta = '0 %';
                                    if( $item->perc_reterenta != 0 ){
                                        $info_reterenta = 'ReteRenta - '.$item->perc_reterenta .' %';
                                        // Calculo despues de aplicar Cargos/Descuento al Bruto
                                        $value_reterenta = ($total_charge_discount * $item->perc_reterenta) / 100;

                                        $sum_tt_retes = $sum_tt_retes + $value_reterenta;

                                        $sum_tt_retes_html = $sum_tt_retes_html . '<tr>
                                            <td width="50%">
                                            '. $info_reterenta . '</td><td>
                                            '. number_format($value_reterenta, 0,',','.')
                                            .' </td></tr>';
                                    }

                                    @endphp
                                    <tr id="line-documents-lines-{{$item->id}}">
                                        <td class="text-center">{{$line}}</td>
                                        <!--
                                        <td class="text-center">{{$item->id}}</td> -->
                                        <td>{{$item->item_description}}</td>
                                        <td>{{$item->quantity_unit}}</td>
                                        <td>{{$item->quantity}}</td>
                                        <td class="text-center">
                                            {{ number_format($item->price, 0,',','.') }}
                                        </td>
                                        <td>
                                            {{ number_format($item->total, 0,',','.') }}
                                        </td>
                                        <!--
                                        <td>{{$item->perc_charge}} %</td> -->
                                        <td>{{$item->perc_discount}} %</td>
                                        <td class="text-center">
                                            {{ number_format($total_charge_discount, 0,',','.') }}
                                        </td>
                                        <td>{{$info_reterenta}}</td>
                                        <td>{{$info_tax}}</td>
                                        <td class="text-center">
                                            <button type="button" 
                                                data-id="{{$item->id}}" 
                                                data-line="{{$line}}" 
                                                class="btn btn-success btn-edit-documents-lines"
                                                >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-danger btn-del-documents-lines"
                                                data-id="{{$item->id}}" 
                                                >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr> 
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- ============================ 
                        -->
                        <hr />
                        <br />

                        <div class="div-table-responsive">
                            <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                <tbody>
                                    <tr>
                                        <td width="40%">Valor total bruto </td>
                                        <td id="td_total_gross">
                                            {{ number_format($total_gross, 0,',','.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Valor total descuentos</td>
                                        <td id="td_sum_tt_discount">
                                            {{ number_format($sum_tt_discount, 0,',','.') }}
                                        </td>
                                    </tr>
                                    <!--  -->
                                    <tr class="oculto" >
                                        <td>Valor total recargos</td>
                                        <td id="td_sum_tt_charge">
                                            {{ number_format($sum_tt_charge, 0,',','.') }}
                                        </td>
                                    </tr> 
                                    <tr>
                                        <td>Subtotal</td>
                                        <td id="td_sum_subtotal_charges_discount">
                                            {{ number_format($sum_subtotal_charges_discount, 0,',','.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Impuestos</td>
                                        <td>
                                        @php   
                                        //if( $sum_tt_ivas_html != '' ){
                                            echo '<div class="div-table-responsive">
                                                <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                                    <tbody id="td_impuestos">
                                                        '.$sum_tt_ivas_html.
                                                        '
                                                    </tbody>
                                                </table>
                                            </div>';
                                        //}
                                        @endphp    
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> % ReteIVA </td>
                                        <td>
                                            @php
                                            if( $row->perc_reteiva === NULL || $row->perc_reteiva == '' )
                                                $row->perc_reteiva = 0;

                                            // Calculo ReteIVA
                                            $tt_reteiva = 0;
                                            $tt_reteiva_show = 0;
                                            $readonly = '';
                                            $readonly = 'readonly';
                                            if( $sum_tt_ivas == 0 ){
                                                $readonly = 'readonly';
                                                $row->perc_reteiva = 0;
                                            }
                                            elseif( $row->perc_reteiva != 0 ){
                                                $tt_ivas = $sum_tt_ivas - ( $sum_tt_ivas * ($row->perc_reteiva/100) );
                                                $tt_reteiva_show = round( $sum_tt_ivas - $tt_ivas );
                                                $tt_reteiva = $sum_tt_ivas - $tt_ivas;
                                            }
                                            @endphp
                                            <div class="div-table-responsive">
                                                <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                                    <tbody id="td_impuestos">
                                                        <tr>
                                                            <td width="50%" >
                                                                <input type="text" class="form-control" 
                                                                    id="perc_reteiva" 
                                                                    value="{{$row->perc_reteiva}}" 
                                                                    name="perc_reteiva"
                                                                    placeholder="" required 
                                                                    {{ $readonly }}
                                                                />
                                                            </td>
                                                            <td id="td_reteiva" >
                                                                {{ number_format($tt_reteiva_show, 0,',','.') }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Retenciones</td>
                                        <td>
                                        @php   
                                        //if( $sum_tt_retes_html != '' ){
                                            echo '<div class="div-table-responsive">
                                                <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                                    <tbody id="td_retenciones">
                                                        '.$sum_tt_retes_html.
                                                        '
                                                    </tbody>
                                                </table>
                                            </div>';
                                        //}
                                        @endphp    
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total a pagar</td>
                                        <td id="td_pay_total">
                                        @php
                                        // A la Suma de IVAs le resto el ReteIVA
                                        $sum_tt_ivas_end = $sum_tt_ivas - $tt_reteiva;

                                        $sum_tt_gross_and_ivas_end = $sum_subtotal_charges_discount + $sum_tt_ivas_end;
                                        /*
                                        echo '<hr /><pre>'.print_r( [ 'sum_tt_gross_and_ivas_end'=>$sum_tt_gross_and_ivas_end, 
                                            'sum_tt_charge'=>$sum_tt_charge, 
                                            'sum_tt_discount'=>$sum_tt_discount, ] ) . '</pre>'; */
                                        //$pay_total = ( $sum_subtotal_charges_discount + $sum_tt_ivas_end ) - $sum_tt_retes;
                                        //$pay_total = ( $sum_subtotal_charges_discount + $sum_tt_ivas_end );
                                        $pay_total = ( $sum_tt_gross_and_ivas_end + $sum_tt_charge ) - $sum_tt_discount;

                                        $pay_total_show = round( $pay_total );

                                        echo number_format($pay_total_show, 0,',','.');
                                        @endphp
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <div class="form-row">
                                                <div class="form-group col-sm-12">
                                                    <label for="value_letters">
                                                        Valor en letras *
                                                    </label>
                                                    <input type="text" class="form-control" 
                                                        id="value_letters" 
                                                        value="{{$row->value_letters}}" 
                                                        name="value_letters"
                                                        placeholder="" required 
                                                    />
                                                    <!-- campos Ocultos... aca pa bajo -->
                                                    @php
                                                    $type_text = 'hidden'; // hidden / text
                                                    @endphp
                                                    <input type="{{$type_text}}" 
                                                        name="total_lines" 
                                                        id="total_lines" 
                                                        value="{{$line}}" 
                                                    />
                                                    <!-- Total bruto 
                                                    $total_gross
                                                    -->
                                                    <input type="{{$type_text}}" 
                                                        name="gross_total_base_lines" 
                                                        id="gross_total_base_lines" 
                                                        value="{{$sum_subtotal_charges_discount}}" 
                                                        style="background: #6acf4b;"
                                                    />
                                                    <!-- Subtotal - descuentos -->
                                                    <input type="{{$type_text}}" 
                                                        name="subtotal" 
                                                        id="subtotal" 
                                                        value="{{$sum_subtotal_charges_discount}}" 
                                                    />
                                                    <!-- (Total - Descuentos) 
                                                    tenia: $sum_subtotal_charges_discount
                                                    --> 
                                                    <input type="{{$type_text}}" 
                                                        name="gross_total_minus_discounts" 
                                                        id="gross_total_minus_discounts" 
                                                        value="{{$sum_base_impo_ivas}}" 
                                                        style="background: #6acf4b;"
                                                    />
                                                    <!-- (Total - Descuentos) + Ivas -->
                                                    <input type="{{$type_text}}" 
                                                        name="gross_total_and_tribute" 
                                                        id="gross_total_and_tribute" 
                                                        value="{{$sum_subtotal_charges_discount + $sum_tt_ivas}}" 
                                                        style="background: #6acf4b;"
                                                    />
                                                    
                                                    <!-- Tt descuentos y Detalles? -->
                                                    <input type="{{$type_text}}" 
                                                        name="discounts_total_and_detail" 
                                                        id="discounts_total_and_detail" 
                                                        value="{{$sum_tt_discount}}" 
                                                        style="background: #6acf4b;"
                                                    />
                                                    <!-- Tt cargos y Detalles? -->
                                                    <input type="{{$type_text}}" 
                                                        name="charges_total_and_detail" 
                                                        id="charges_total_and_detail" 
                                                        value="{{$sum_tt_charge}}" 
                                                        style="background: #6acf4b;"
                                                    />
                                                    <!-- Pago total -->
                                                    <input type="{{$type_text}}" 
                                                        name="pay_total" 
                                                        id="pay_total" 
                                                        value="{{$pay_total}}" 
                                                        style="background: #6acf4b;"
                                                    />

                                                    <!-- ======================== 
                                                    Campos Auxiliares para RECALCULAR ReteIVA
                                                    -->
                                                    <input type="{{$type_text}}" 
                                                        name="sum_tt_ivas" 
                                                        id="sum_tt_ivas" 
                                                        value="{{$sum_tt_ivas}}" 
                                                    />
                                                    <input type="{{$type_text}}" 
                                                        name="sum_tt_retes" 
                                                        id="sum_tt_retes" 
                                                        value="{{$sum_tt_retes}}" 
                                                    />

                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <div class="form-row">
                                                <div class="form-group col-sm-12 text-center">
                                                @php
                                                    $displayBtns = 'style="display:none;"';
                                                    if(
                                                        $pay_total !== NULL 
                                                        && $pay_total != '' 
                                                        && $pay_total != 0 
                                                    ){
                                                        $displayBtns = '';
                                                    }
                                                    echo '<button '.$displayBtns.'
                                                            type="button" 
                                                            class="btn btn-success"
                                                            id="btn-update"
                                                            >
                                                            <i class="fas fa-sync"></i> 
                                                            Actualizar
                                                        </button>';
                                                @endphp
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- ============================ 
                        -->
                        <hr />
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
let divMessages, divCadena

const formatNumber2 = (floatValue = 0, decimals = 0, multiplier = 1) => {
    let floatMultiplied = floatValue * multiplier;
    let stringFloat = floatMultiplied + "";
    let arraySplitFloat = stringFloat.split(".");
    let decimalsValue = "0";
    if (arraySplitFloat.length > 1) {
        decimalsValue = arraySplitFloat[1].slice(0, decimals);
    }
    let integerValue = arraySplitFloat[0];
    let arrayFullStringValue = [integerValue, decimalsValue];
    let FullStringValue = arrayFullStringValue.join(".")
    let floatFullValue = parseFloat(FullStringValue) + "";
    let formatFloatFullValue = new Intl.NumberFormat('es-ES', { minimumFractionDigits: decimals }).format(floatFullValue);
    return formatFloatFullValue;
}
function toNum(str) {
    str = str + ''
    return str.replace(/\,|\￥/g, "");
}
function formatNumber( num, decimals = 0 ) {
    let source = toNum(num).split(".");
    //source[0] = source[0].replace(new RegExp('(\\d)(?=(\\d{3})+$)','ig'),"$1,");
    source[0] = source[0].replace(new RegExp('(\\d)(?=(\\d{3})+$)','ig'),"$1.");
    return source.join(".");
}

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

let inp = {} // Info Documento
let docLines = {} // Info DocumentoLinea
let btnDelOkDocumentsLines
let btnShowDocumentsLines

let formDocumentsLines, btnSaveDocumentsLines, btnClearDocumentsLines
let divDescriptionDocumentsLines, divCodeDocumentsLines

let tableDocumentsLines

let td_total_gross, td_sum_tt_discount, td_sum_tt_charge, td_sum_subtotal_charges_discount, td_impuestos, td_retenciones, td_pay_total

let btnUpdate, btnSend


// Carga de "maestras" necesarias en JS 
@php
echo "const types_tax_ = " . json_encode($types_tax) . ";\n";
@endphp

// =====================================================
// ---------- Funcion Numeros => Letras ----------------
const numeroALetras = ( function() {

// Código basado en https://gist.github.com/alfchee/e563340276f89b22042a
    function Unidades(num){

        switch(num)
        {
            case 1: return 'UN';
            case 2: return 'DOS';
            case 3: return 'TRES';
            case 4: return 'CUATRO';
            case 5: return 'CINCO';
            case 6: return 'SEIS';
            case 7: return 'SIETE';
            case 8: return 'OCHO';
            case 9: return 'NUEVE';
        }

        return '';
    }//Unidades()

    function Decenas(num){

        let decena = Math.floor(num/10);
        let unidad = num - (decena * 10);

        switch(decena)
        {
            case 1:
                switch(unidad)
                {
                    case 0: return 'DIEZ';
                    case 1: return 'ONCE';
                    case 2: return 'DOCE';
                    case 3: return 'TRECE';
                    case 4: return 'CATORCE';
                    case 5: return 'QUINCE';
                    default: return 'DIECI' + Unidades(unidad);
                }
            case 2:
                switch(unidad)
                {
                    case 0: return 'VEINTE';
                    default: return 'VEINTI' + Unidades(unidad);
                }
            case 3: return DecenasY('TREINTA', unidad);
            case 4: return DecenasY('CUARENTA', unidad);
            case 5: return DecenasY('CINCUENTA', unidad);
            case 6: return DecenasY('SESENTA', unidad);
            case 7: return DecenasY('SETENTA', unidad);
            case 8: return DecenasY('OCHENTA', unidad);
            case 9: return DecenasY('NOVENTA', unidad);
            case 0: return Unidades(unidad);
        }
    }//Decenas()

    function DecenasY(strSin, numUnidades) {
        if (numUnidades > 0)
            return strSin + ' Y ' + Unidades(numUnidades)

        return strSin;
    }//DecenasY()

    function Centenas(num) {
        let centenas = Math.floor(num / 100);
        let decenas = num - (centenas * 100);

        switch(centenas)
        {
            case 1:
                if (decenas > 0)
                    return 'CIENTO ' + Decenas(decenas);
                return 'CIEN';
            case 2: return 'DOSCIENTOS ' + Decenas(decenas);
            case 3: return 'TRESCIENTOS ' + Decenas(decenas);
            case 4: return 'CUATROCIENTOS ' + Decenas(decenas);
            case 5: return 'QUINIENTOS ' + Decenas(decenas);
            case 6: return 'SEISCIENTOS ' + Decenas(decenas);
            case 7: return 'SETECIENTOS ' + Decenas(decenas);
            case 8: return 'OCHOCIENTOS ' + Decenas(decenas);
            case 9: return 'NOVECIENTOS ' + Decenas(decenas);
        }

        return Decenas(decenas);
    }//Centenas()

    function Seccion(num, divisor, strSingular, strPlural) {
        let cientos = Math.floor(num / divisor)
        let resto = num - (cientos * divisor)

        let letras = '';

        if (cientos > 0)
            if (cientos > 1)
                letras = Centenas(cientos) + ' ' + strPlural;
            else
                letras = strSingular;

        if (resto > 0)
            letras += '';

        return letras;
    }//Seccion()

    function Miles(num) {
        let divisor = 1000;
        let cientos = Math.floor(num / divisor)
        let resto = num - (cientos * divisor)

        let strMiles = Seccion(num, divisor, 'UN MIL', 'MIL');
        let strCentenas = Centenas(resto);

        if(strMiles == '')
            return strCentenas;

        return strMiles + ' ' + strCentenas;
    }//Miles()

    function Millones(num) {
        let divisor = 1000000;
        let cientos = Math.floor(num / divisor)
        let resto = num - (cientos * divisor)

        //let strMillones = Seccion(num, divisor, 'UN MILLON DE', 'MILLONES DE');
        let strMillones = Seccion(num, divisor, 'UN MILLON ', 'MILLONES ');
        let strMiles = Miles(resto);

        if(strMillones == '')
            return strMiles;

        return strMillones + ' ' + strMiles;
    }//Millones()

    return function NumeroALetras(num, currency) {
        currency = currency || {};
        let data = {
            numero: num,
            enteros: Math.floor(num),
            centavos: (((Math.round(num * 100)) - (Math.floor(num) * 100))),
            letrasCentavos: '',
            letrasMonedaPlural: currency.plural || 'PESOS',//'PESOS CHILENOS', 'Dólares', 'Bolívares', 'etcs'
            letrasMonedaSingular: currency.singular || 'PESO', //'PESO CHILENO', 'Dólar', 'Bolivar', 'etc'
            letrasMonedaCentavoPlural: currency.centPlural || 'PESOS',//'CHIQUI PESOS CHILENOS',
            letrasMonedaCentavoSingular: currency.centSingular || 'PESO',//'CHIQUI PESO CHILENO'
        };

        if (data.centavos > 0) {
            data.letrasCentavos = 'CON ' + (function () {
                    if (data.centavos == 1)
                        return Millones(data.centavos) + ' ' + data.letrasMonedaCentavoSingular;
                    else
                        return Millones(data.centavos) + ' ' + data.letrasMonedaCentavoPlural;
                })();
        };

        if(data.enteros == 0)
            return 'CERO ' + data.letrasMonedaPlural + ' ' + data.letrasCentavos;
        if (data.enteros == 1)
            return Millones(data.enteros) + ' ' + data.letrasMonedaSingular + ' ' + data.letrasCentavos;
        else
            return Millones(data.enteros) + ' ' + data.letrasMonedaPlural + ' ' + data.letrasCentavos;
    };

})();
// Fin Funcion Numeros => Letras -----------------------
// =====================================================


const getArrayLabelByValue = ( base_, value_ ) => {
	if( value_ === null || value_ == '' ) return '';
	let key_, label_ = '';
	for( key_ in base_ ){
        let val_ = base_[ key_ ]
   		if( value_ == val_ ) label_ = key_;
	}
	return label_;
}


const getDataItem = ( id_, type_row_ ) => {
    const data = {
        id: id_, 
        type: type_row_,
    }
    $.ajax({
        data,
        url: urlBase_ + '/api/document/get-item',
        type:'POST',
        dataType :'JSON',
        success: function( resp ){
            if( resp.error ){
                divMessages.innerHTML = `<p>${resp.message}</p>`
                $('#modal-messages').modal('show')
            }
            else{
                // Llena Form
                if( resp.data.lenght != 0 ){
                    const info = resp.data
                    if( type_row_ == 'DocumentsLines' )
                    {
                        setFormDocumentsLines( info )
                    }
                }
            }
        }
    });
}


const showHideDivDocumentsLines = ( open = false ) => {
    const display_ = formDocumentsLines.style.display
    if( display_ != '' || open )
        formDocumentsLines.style.display = ''
    else
        formDocumentsLines.style.display = 'none'
}


const setEventsClik = ( btn_, id_, action_, type_row_ ) => {
    if( type_row_ == 'DocumentsLines' )
    {
        if( action_ == 'edit' )
        {
            btn_.addEventListener('click', () => {
                getDataItem( id_, type_row_ )
            })
        }
        else if( action_ == 'delete' )
        {
            btn_.addEventListener('click', () => {
                confirmDeleteDocumentsLines( id_, type_row_ )
            })
        }
    }
}


const setQuantityUnit = ( data ) => {
    let i, html_ = '<option value="">Seleccione..</option>'
    let value_ = ''
    if( data.id != 0 ){
        value_ = data.quantity_unit_id
        html_ += `<option value="${data.quantity_unit_id}">
            ${data.quantity_unit}
        </option>`
    }
    $('#quantity_unit_id_documents_lines').html( html_ ).val( value_ ).trigger( 'change' )
}

const setFormDocumentsLines = ( data ) => { console.log({data})
    if( data.id != 0 ){
        docLines.id.value = data.id;
        docLines.item_description.value = data.item_description 
        docLines.quantity.value = data.quantity 
        docLines.price.value = data.price 
        docLines.perc_charge.value = data.perc_charge 
        docLines.perc_discount.value = data.perc_discount 
        docLines.perc_tax.value = data.perc_tax 
        docLines.perc_reterenta.value = data.perc_reterenta 
        //docLines.code_discounts_charge_tax.value = data.code_discounts_charge_tax 
        docLines.total.value = data.total 

        //setQuantityUnit( data )
        docLines.quantity_unit_id.value = data.quantity_unit_id

        showHideDivDocumentsLines( true )
        btnSaveDocumentsLines.innerHTML = 'Guardar actualización'
        btnClearDocumentsLines.style.display = ''
    }
    else{
        docLines.id.value = 0
        docLines.item_description.value = ''
        docLines.quantity.value = ''
        docLines.price.value = ''
        docLines.perc_charge.value = 0
        docLines.perc_discount.value = 0
        docLines.perc_tax.value = 0
        docLines.perc_reterenta.value = 0
        //docLines.code_discounts_charge_tax.value = ''
        docLines.total.value = ''
        //setQuantityUnit({ id: 0 })
        docLines.quantity_unit_id.value = ''

        
        btnSaveDocumentsLines.innerHTML = 'Guardar nuevo registro'
        btnClearDocumentsLines.style.display = 'none'
    }
}


const setHtmlDocumentsLines = ( data ) => {
    // Sumatorias
    let total_gross = 0;
    let sum_subtotal_charges_discount = 0;
    let sum_tt_charge = 0;
    let sum_tt_discount = 0;
    let sum_tt_ivas = 0;
    let sum_tt_retes = 0;
    let sum_tt_ivas_html = '';
    let sum_tt_retes_html = '';

    let i, html_ = '', lines_ = 0
    for( i in data )
    {
        let item_ = data[i], line_ = +i + 1, lines_ = line_
        console.log('i = ', i)

        // Totales brutos
        total_gross = +total_gross + ( + item_.total );
        
        // Recargo
        let sum_charge = 0;

        // Descuento
        let sum_discount = 0;
        if( item_.perc_discount !== null
            && item_.perc_discount != 0 
            && item_.perc_discount != '' ){
            sum_discount = (item_.total * item_.perc_discount) / 100;

            sum_tt_discount = +sum_tt_discount + sum_discount;
        }

        let total_charge_discount = (+ item_.total + sum_charge) - sum_discount;

        sum_subtotal_charges_discount = +sum_subtotal_charges_discount + total_charge_discount;
            
        let info_tax = '0 %';
        if( item_.perc_tax != 0 ){
            info_tax = 'IVA - ' + item_.perc_tax + ' %'

            // Calculo despues de aplicar Cargos/Descuento al Bruto
            let value_tax = (total_charge_discount * item_.perc_tax) / 100;

            sum_tt_ivas = +sum_tt_ivas + value_tax;

            sum_tt_ivas_html = sum_tt_ivas_html + `<tr>
                <td width="50%">
                ${info_tax}</td><td>
                ${formatNumber(value_tax, 0)}</td></tr>`;
        }
        let info_reterenta = '0 %';
        if( item_.perc_reterenta != 0 ){
            info_reterenta = 'ReteRenta - ' + item_.perc_reterenta + ' %'

            // Calculo despues de aplicar Cargos/Descuento al Bruto
            let value_reterenta = (total_charge_discount * item_.perc_reterenta) / 100;

            sum_tt_retes = +sum_tt_retes + value_reterenta;

            sum_tt_retes_html = sum_tt_retes_html + `<tr>
                <td width="50%">
                ${info_reterenta}</td><td>
                ${formatNumber( Math.round(value_reterenta), 0)}</td></tr>`;
        }

        html_ += `<tr id="line-documents-lines-${item_.id}">
            <td class="text-center">${line_}</td>
            <!--
            <td class="text-center">${item_.id}</td>-->
            <td>${item_.item_description}</d>
            <td>${item_.quantity_unit}</td>
            <td>${item_.quantity}</td>
            <td class="text-center">
                ${formatNumber(item_.price, 0)}
            </td>
            <td>
                ${formatNumber(item_.total, 0)}
            </td>
            <!--
            <td>${item_.perc_charge} %</td>-->
            <td>${item_.perc_discount} %</td>
            
            <td class="text-center">
                ${formatNumber(total_charge_discount)}
            </td>
            <td>${info_reterenta}</td>
            <td>${info_tax}</td>
            <td class="text-center">
                <button type="button" 
                    data-id="${item_.id}" 
                    data-line="${line_}" 
                    class="btn btn-success btn-edit-documents-lines"
                    >
                    <i class="fas fa-edit"></i>
                </button>
            </td>
            <td class="text-center">
                <button type="button"
                    class="btn btn-danger btn-del-documents-lines"
                    data-id="${item_.id}" 
                    >
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr> `
    }
    tableDocumentsLines.innerHTML = html_
    setEventDocumentsLines()

    td_total_gross.innerHTML = formatNumber(total_gross, 0)
    td_sum_tt_discount.innerHTML = formatNumber(sum_tt_discount, 0)
    td_sum_tt_charge.innerHTML = formatNumber(sum_tt_charge, 0)
    td_sum_subtotal_charges_discount.innerHTML = formatNumber(sum_subtotal_charges_discount, 0)
    td_impuestos.innerHTML = sum_tt_ivas_html
    td_retenciones.innerHTML = sum_tt_retes_html

    // Actualizo Aux para calculos [  ]
    inp.subtotal.value = sum_subtotal_charges_discount
    inp.sum_tt_ivas.value = sum_tt_ivas
    inp.sum_tt_retes.value = sum_tt_retes

    // Calculo ReteIVA
    let tt_reteiva = 0
    //console.log({ sum_tt_ivas })
    if( sum_tt_ivas == 0 ){
        ////inp.perc_reteiva.readOnly = true;
        inp.perc_reteiva.value = 0;
    }
    else{
        ////inp.perc_reteiva.readOnly = false;

        let perc_reteiva_ = inp.perc_reteiva.value
        perc_reteiva_ = ( perc_reteiva_==null || perc_reteiva_=='')? 0 : perc_reteiva_
        
        let tt_ivas = +sum_tt_ivas - ( sum_tt_ivas * (perc_reteiva_ / 100 ) )
        tt_reteiva = +sum_tt_ivas - tt_ivas
    }
    td_reteiva.innerHTML = formatNumber( Math.round( tt_reteiva ) )

    // A la Suma de IVAs le resto el ReteIVA
    sum_tt_ivas = +sum_tt_ivas - tt_reteiva

    const sum_tt_gross_and_ivas_end = +sum_subtotal_charges_discount + sum_tt_ivas

    //const pay_total = ( +sum_subtotal_charges_discount + sum_tt_ivas ) - sum_tt_retes
    //const pay_total = ( +sum_subtotal_charges_discount + sum_tt_ivas )
    const pay_total = ( sum_tt_gross_and_ivas_end + sum_tt_charge ) - sum_tt_discount

    td_pay_total.innerHTML = formatNumber(pay_total, 0)

    // Campos Ocultos que actualizan Totales en el Doc.
    inp.total_lines.value = lines_
    inp.gross_total_base_lines.value = sum_subtotal_charges_discount //total_gross
    inp.gross_total_minus_discounts.value = sum_subtotal_charges_discount
    inp.gross_total_and_tribute.value = +sum_subtotal_charges_discount + ( +sum_tt_ivas )
    inp.discounts_total_and_detail.value = sum_tt_discount
    inp.charges_total_and_detail.value = sum_tt_charge
    inp.pay_total.value = pay_total

    // Numeros a Letras:
    let numberToLetters = numeroALetras( pay_total, {
        plural: '',//'Pesos colombianos',
        singular: '',//'Pesos colombianos',
        centPlural: 'CENTAVOS',
        centSingular: 'CENTAVO'
    });
    inp.value_letters.value = numberToLetters

    // Btns Actualizar/Enviar a cadena 
    let displayBtns_ = 'none'
    if( pay_total !== null 
        && pay_total != '' 
        && pay_total != 0
    ){
        displayBtns_ = '';
    }
    btnUpdate.style.display = displayBtns_
    btnSend.style.display = displayBtns_
}


const deleteDocumentsLines = ( id_, type_row_ ) => {
    $('#modal-confirm-delete').modal('hide')
    const data = {
        id: id_, 
        type: type_row_,
    }
    $.ajax({
        data,
        url: urlBase_ + '/api/document/del-item',
        type:'POST',
        dataType :'JSON',
        success: function( resp ){
            if( resp.error ){
                divMessages.innerHTML = `<p>${resp.message}</p>`
                $('#modal-messages').modal('show')
            }
            else{
                setHtmlDocumentsLines( resp.data )

                setFormDocumentsLines({ id: 0 })
                if( formDocumentsLines.style.display == '' )
                    showHideDivDocumentsLines( false )
            }
        }
    });
}

const confirmDeleteDocumentsLines = ( id_, type_row_ ) => {
    btnDelOkDocumentsLines.setAttribute( 'data-id', id_ )
    $('#modal-confirm-delete').modal('show')
}

const setEventDocumentsLines = () => {
    const listBtns_ = getHTML('.btn-edit-documents-lines', 'class')
    for( let btn_ of listBtns_ ){
        const id_ = btn_.getAttribute('data-id')
        setEventsClik( btn_, id_, 'edit', 'DocumentsLines' )
	}
    const listBtns2_ = getHTML('.btn-del-documents-lines', 'class')
    for( let btn_ of listBtns2_ ){
        const id_ = btn_.getAttribute('data-id')
		setEventsClik( btn_, id_, 'delete', 'DocumentsLines' )
	}
}


const calculateTotal = () => {
    const price_ = docLines.price.value
    const quantity_ = docLines.quantity.value
    if( price_ !== null && price_ != '' )
    {
        if( quantity_ !== null && quantity_ != '' )
        {
            docLines.total.value = price_ * quantity_
        }
    }
}

const calculatePayTotal = () => { 
    const perc_reteiva_ = inp.perc_reteiva.value
    const subtotal_ = inp.subtotal.value
    const sum_tt_ivas_ = inp.sum_tt_ivas.value
    const sum_tt_retes_ = inp.sum_tt_retes.value


    let tt_reteiva_ = 0
    if( perc_reteiva_ !== null && perc_reteiva_ != '' && perc_reteiva_ >= 0 ){
        tt_reteiva_ = sum_tt_ivas_ * ( perc_reteiva_  / 100 )

        td_reteiva.innerHTML = formatNumber( Math.round( tt_reteiva_ ), 0)
        //console.log({ perc_reteiva_, subtotal_, sum_tt_ivas_, tt_reteiva_ })
    }
    else{
        td_reteiva.innerHTML = 0
    }

    let pay_total_ = ( +subtotal_ + (+sum_tt_ivas_) ) - (+sum_tt_retes_)
    //console.log({ pay_total_ })
    pay_total_ = Math.round( +pay_total_ - (+tt_reteiva_) )

    td_pay_total.innerHTML = formatNumber( pay_total_, 0 )

    // Numeros a Letras:
    let numberToLetters = numeroALetras( pay_total_, {
        plural: '',
        singular: '',
        centPlural: 'CENTAVOS',
        centSingular: 'CENTAVO'
    });
    inp.value_letters.value = numberToLetters
} 


const saveDocumentsLines = () => {
    const document_id = inp.document_id.value

    const id = docLines.id.value
    const quantity_unit_id = docLines.quantity_unit_id.value
    const quantity = docLines.quantity.value
    const total = docLines.total.value
    const item_description = docLines.item_description.value
    //const product_code = docLines.product_code.value
    const price = docLines.price.value
    const perc_charge = docLines.perc_charge.value
    const perc_discount = docLines.perc_discount.value
    const perc_tax = docLines.perc_tax.value
    const perc_reterenta = docLines.perc_reterenta.value
    //const code_discounts_charge_tax = docLines.code_discounts_charge_tax.value

    let errors = []
    if( ! validateData( item_description, 'string', true, 0 ) ){
        errors.push( 'Debe ingresar una descripción' )
    }
    if( ! validateData( quantity_unit_id, 'number', true, 1 ) ){
        errors.push( 'Debe seleccionar una unidad de medida' )
    }
    if( ! validateData( quantity, 'number', true, 1 ) ){
        errors.push( 'Debe ingresar una cantidad válida' )
    }
    if( ! validateData( price, 'number', true, 1 ) ){
        errors.push( 'Debe ingresar un precio válido' )
    }
    if( ! validateData( perc_charge, 'number', true, 0 ) ){
        errors.push( 'Debe ingresar un recargo de cero o más %' )
    }
    if( ! validateData( perc_discount, 'number', true, 0 ) ){
        errors.push( 'Debe ingresar un descuento de cero o más %' )
    }
    if( ! validateData( perc_tax, 'number', true, 0 ) ){
        errors.push( 'Debe ingresar un IVA de cero o más %' )
    }
    if( ! validateData( perc_reterenta, 'number', true, 0 ) ){
        errors.push( 'Debe ingresar un ReteRenta de cero o más %' )
    }

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
        document_id, 
        quantity_unit_id, 
        quantity, 
        total, 
        item_description, 
        //product_code, 
        price, 
        perc_charge, 
        perc_discount, 
        perc_tax, 
        perc_reterenta, 
        //code_discounts_charge_tax,
    }
    $.ajax({
        data,
        url: urlBase_ + '/api/document/edit',
        type:'POST',
        dataType :'JSON',
        success: function( resp ){
            if( resp.error ){
                divMessages.innerHTML = `<p>${resp.message}</p>`
                $('#modal-messages').modal('show')
            }
            else{
                setHtmlDocumentsLines( resp.data )

                setFormDocumentsLines({ id: 0 })
                showHideDivDocumentsLines( false )

                updateDocument( false )
            }
        }
    });
}



const hideBtnsAfterSendOk = () => {
    btnShowDocumentsLines.style.display = 'none'
    
    const listBtns_ = getHTML('.btn-edit-documents-lines', 'class')
    for( let btn_ of listBtns_ ){
        btn_.style.display = 'none'
	}
    const listBtns2_ = getHTML('.btn-del-documents-lines', 'class')
    for( let btn_ of listBtns2_ ){
        btn_.style.display = 'none'
	}

    btnUpdate.style.display = 'none'
}



const updateDocument = ( showModal_ ) => {
    
    const document_id = inp.document_id.value
    const value_letters = inp.value_letters.value
    const total_lines = inp.total_lines.value
    const gross_total_base_lines = inp.gross_total_base_lines.value
    const gross_total_minus_discounts = inp.gross_total_minus_discounts.value
    const gross_total_and_tribute = inp.gross_total_and_tribute.value
    const discounts_total_and_detail = inp.discounts_total_and_detail.value
    const charges_total_and_detail = inp.charges_total_and_detail.value
    const pay_total = inp.pay_total.value

    const perc_reteiva = inp.perc_reteiva.value

    let errors = []
    if( ! validateData( value_letters, 'string', true, 0 ) ){
        errors.push( 'Debe ingresar el valor en letras' )
    }
    if( ! validateData( perc_reteiva, 'number', true, 0 ) ){
        errors.push( 'Debe ingresar un ReteIva de cero o más %' )
    }
    if( errors.length != 0 ){
        let i, html_ = ''
        for( i in errors ){
            html_ += `<p>* ${errors[i]}</p>`
        }
        divMessages.innerHTML = html_
        $('#modal-messages').modal('show')
        return
    }

    divResponseCadena.innerHTML = ''

    const data = {
        document_id, 
        value_letters, 
        total_lines, 
        gross_total_base_lines, 
        gross_total_minus_discounts, 
        gross_total_and_tribute, 
        discounts_total_and_detail, 
        charges_total_and_detail, 
        pay_total, 
        perc_reteiva,
    }
    $.ajax({
        data,
        url: urlBase_ + '/api/document/update',
        type:'POST',
        dataType :'JSON',
        success: function( resp ){
            if( resp.error ){
                divMessages.innerHTML = `<p>${resp.message}</p>`
                $('#modal-messages').modal('show')
            }
            else{
                if( showModal_ ){
                    /*
                    let pathComplete = resp.xml
                    let initUrlBase = urlBase_.replace('public', '')
                    let split_ = 'storage'
                    let vSplit = pathComplete.split( split_ )
                    let pathXml = split_ + vSplit[ 1 ]
                    let path = initUrlBase + pathXml
                    
                    divCadena.innerHTML = `<a href="${path}" target="_blank">
                        Ver XML
                    </a>` 
                    */
                    $('#modal-cadena').modal('show')
                }
            }
        }
    });
}


const sendDataCadena = () => {
    divResponseCadena.innerHTML = 'Procesando...'
    const document_id = inp.document_id.value
    const data = {
        document_id,
    }
    $.ajax({
        data,
        url: urlBase_ + '/api/document/send-data',
        type:'POST',
        dataType :'JSON',
        success: function( resp ){
            if( resp.error ){
                let errorMessage = '', errorReason = ''
                if( resp.data.hasOwnProperty('errorMessage') )
                    errorMessage = resp.data.errorMessage

                if( resp.data.hasOwnProperty('errorReason') ){
                    let errorReasonObj = resp.data.errorReason
                    errorReason = errorReasonObj.join('<br /> ')
                }
                let message = errorMessage +'<br />'+ errorReason
                if( message == '<br />' ) message = resp.message

                divResponseCadena.innerHTML = `<p class="alert alert-danger" >
                    Ha ocurrido un error. <br />
                    ${message}
                </p>`
            }
            else{
                divResponseCadena.innerHTML = `<p class="alert alert-success" >
                    ${resp.data}
                </p>`

                $('#modal-cadena').on('hidden.bs.modal', function (e) {
                    window.location.reload()
                })
            }
        }
    })
}



$(document).ready(function () {

    divMessages = getHTML('div-messages')
    divCadena = getHTML('div-cadena')
    divResponseCadena = getHTML('div-response-cadena')

    inp.id = getHTML('id') 
    inp.document_id = getHTML('document_id')
    // btn borra item/linea 
    btnDelOkDocumentsLines = getHTML('btn-del-ok-documents-lines')

    btnShowDocumentsLines = getHTML('btn-show-documents-lines')
    btnSaveDocumentsLines = getHTML('btn-save-documents-lines')
    btnClearDocumentsLines = getHTML('btn-clear-documents-lines')
    formDocumentsLines = getHTML('form-documents-lines')
    divDescriptionDocumentsLines = getHTML('div-description-documents-lines')
    divCodeDocumentsLines = getHTML('div-code-documents-lines')
    
    docLines.id = getHTML('id_documents_lines')
    //docLines.product_code = getHTML('product_code_documents_lines')
    docLines.item_description = getHTML('item_description_documents_lines')
    docLines.quantity_unit_id = getHTML('quantity_unit_id_documents_lines')
    docLines.unit_measurement = getHTML('unit_measurement_documents_lines')
    docLines.quantity = getHTML('quantity_documents_lines')
    docLines.price = getHTML('price_documents_lines')
    docLines.perc_charge = getHTML('perc_charge_documents_lines')
    docLines.perc_discount = getHTML('perc_discount_documents_lines')
    docLines.perc_tax = getHTML('perc_tax_documents_lines')
    docLines.perc_reterenta = getHTML('perc_reterenta_documents_lines')
    //docLines.code_discounts_charge_tax = getHTML('code_discounts_charge_tax_documents_lines')
    docLines.total = getHTML('total_documents_lines')
    
    tableDocumentsLines = getHTML('table-documents-lines')

    td_total_gross = getHTML('td_total_gross')
    td_sum_tt_discount = getHTML('td_sum_tt_discount')
    td_sum_tt_charge = getHTML('td_sum_tt_charge')
    td_sum_subtotal_charges_discount = getHTML('td_sum_subtotal_charges_discount')
    td_impuestos = getHTML('td_impuestos')
    td_reteiva = getHTML('td_reteiva')
    td_retenciones = getHTML('td_retenciones')
    td_pay_total = getHTML('td_pay_total')

    btnUpdate = getHTML('btn-update') 
    btnSend = getHTML('btn-send')
    inp.value_letters = getHTML('value_letters')
    inp.total_lines = getHTML('total_lines')
    inp.gross_total_base_lines = getHTML('gross_total_base_lines')
    inp.gross_total_minus_discounts = getHTML('gross_total_minus_discounts')
    inp.subtotal = getHTML('subtotal')
    inp.gross_total_and_tribute = getHTML('gross_total_and_tribute')
    inp.discounts_total_and_detail = getHTML('discounts_total_and_detail')
    inp.charges_total_and_detail = getHTML('charges_total_and_detail')
    inp.pay_total = getHTML('pay_total')
    inp.perc_reteiva = getHTML('perc_reteiva')

    inp.sum_tt_ivas = getHTML('sum_tt_ivas')
    inp.sum_tt_retes = getHTML('sum_tt_retes')


    btnShowDocumentsLines.addEventListener('click', ( ev ) => {
        showHideDivDocumentsLines( ev.target.value )
    })
    btnSaveDocumentsLines.addEventListener('click', ( ev ) => {
        saveDocumentsLines()
    })
    btnClearDocumentsLines.addEventListener('click', ( ev ) => {
        setFormDocumentsLines({ id: 0 })
    })


    // ------- Busqueda por Ajax de: Unidades de Medida ----------
    /*
    $('#quantity_unit_id_documents_lines').select2({
        language: 'es',
        ajax: {
            url: urlBase_ + '/api/document/search-quantity-unit',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { query_: params.term }
            },
            processResults: function (data, params) {
                if( data.error ){
                    divMessages.innerHTML = `<p>${data.message}</p>`
                    $('#modal-messages').modal('show')
                    return
                }
                return {
                    results: data.data,
                }
            },
        },  
        placeholder: 'buscar...',
        minimumInputLength: 2,
    }); */


    docLines.quantity.addEventListener('keyup',  function ( ev ) {
        calculateTotal()
    })
    docLines.price.addEventListener('keyup',  function ( ev ) {
        calculateTotal()
    })

    inp.perc_reteiva.addEventListener('keyup',  function ( ev ) {
        calculatePayTotal()
    })

    btnDelOkDocumentsLines.addEventListener('click',  function ( ev ) {
        const id_ = btnDelOkDocumentsLines.getAttribute( 'data-id' )
        deleteDocumentsLines( id_, 'DocumentsLines' )
    })

    btnUpdate.addEventListener('click',  function ( ev ) {
        updateDocument( true )
    })

    btnSend.addEventListener('click',  function ( ev ) {
        sendDataCadena()
    })


    setEventDocumentsLines()


    // Ejecuciones -------------------------------------
    @php
    if( $pay_total != 0 ){
        echo 'let pay_total_ = ' . $pay_total . ";\n";
    @endphp
        let numberToLetters = numeroALetras( pay_total_, {
            plural: '',//'Pesos colombianos',
            singular: '',//'Pesos colombianos',
            centPlural: 'CENTAVOS',
            centSingular: 'CENTAVO'
        });
        inp.value_letters.value = numberToLetters
    @php
        if( $row->document_number != NULL && $row->document_number != '' ){
            echo "\nhideBtnsAfterSendOk();";
        }
    }
    else{
        echo 'inp.value_letters.value = "";';
    }
    @endphp
})


</script>
@endsection
