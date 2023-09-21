@extends('layouts.app')

@section('content')

<div class="container invoice-area">

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
                                    <th>Valor bruto</th>
                                    <th>Valor descuentos</th>
                                    <th>Valor total</th>
                                    <th>Valor en letras</th>
                                    <th>Líneas</th>
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
                                        <td>{{$row->gross_total_and_tribute}}</td>
                                        <td>{{$row->discounts_total_and_detail}}</td>
                                        <td>{{$row->pay_total}}</td>
                                        <td>{{$row->value_letters}}</td>
                                        <td>{{$row->total_lines}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        @php 
                            //echo '<pre>'.print_r($rowsDiscountChargeTax).'</pre>';

                            $yesNo = config('app.globalLists')['yesNo'];
                            // Cargo/Descuento/Impuesto
                            $types_discounts_charge_tax = config('app.equivalenDocument')['types_discounts_charge_tax'];
                            // Codigos Impuesto
                            $types_tax = config('app.equivalenDocument')['TaxScheme_Name'];

                        @endphp

                        <!-- ============================ 

                        -->
                        <hr />
                        <br />
                        <h4>Cargos / Descuentos / Impuestos a nivel de factura</h4>
                        <div class="form-row">
                            <div 
                            class="form-group col-sm-6 "
                            style="text-align: left;" 
                            >
                                <button type="button"
                                    class="btn btn-success"
                                    id="btn-show-discounts-charge-tax"
                                ><!--
                                <i class="fas fa-plus text-white"></i>
                                <i class="fas fa-minus"></i>-->
                                <i class="fas fa-arrow-down text-white"></i>
                                <i class="fas fa-arrow-up text-white"></i>
                                </button>
                            </div>
                        </div>

                        <br />
                        <div id="form-discounts-charge-tax" style="display: none;">
                            <form  method="POST" >
                                @php
                                $lineNext = count( $rowsDiscountChargeTax ) + 1;
                                @endphp
                                <input type="hidden" name="line_next_discounts_charge_tax" 
                                    id="id_discounts_charge_tax" 
                                    value="{{$lineNext}}" />
                                <input type="hidden" name="id_discounts_charge_tax" 
                                    id="id_discounts_charge_tax" 
                                    value="0" />
                                <div class="form-row">
                                    <div class="form-group col-sm-3" >
                                        <label for="line_discounts_charge_tax">
                                            Línea *
                                        </label>
                                        <input type="number" class="form-control " 
                                        id="line_discounts_charge_tax" 
                                        value="{{$lineNext}}" 
                                        name="line_discounts_charge_tax" 
                                        placeholder="" required 
                                        readonly 
                                        />
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="type_discounts_charge_tax">
                                            Tipo *
                                        </label>
                                        <select class="form-control" 
                                            id="type_discounts_charge_tax" name="type_discounts_charge_tax">
                                            <option value="" >Seleccione..</option>
                                            @foreach( $types_discounts_charge_tax as $type_ )
                                                <option value="{{$type_}}" >
                                                    {{$type_}}
                                                </option>  
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-5" 
                                        style="display: none;"
                                        id="div-code-discounts-charge-tax"
                                    >
                                        <label for="code_discounts_charge_tax">
                                            Tipo impuesto *
                                        </label>
                                        <select 
                                            class="form-control" 
                                            id="code_discounts_charge_tax" 
                                            name="code_discounts_charge_tax"
                                            >
                                            @php
                                            foreach( $types_tax as $key_ => $val_ ){
                                                echo '<option value="'.$val_.'" >
                                                    '.$key_.'
                                                </option>';
                                            }
                                            @endphp
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-5" 
                                        style="display: none;"
                                        id="div-description-discounts-charge-tax"
                                    >
                                        <label for="description_discounts_charge_tax">
                                            Descripción *
                                        </label>
                                        <input type="text" class="form-control" 
                                        id="description_discounts_charge_tax" 
                                        value="" 
                                        name="description_discounts_charge_tax" 
                                        placeholder="" required />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm-4">
                                        <label for="percentage_discounts_charge_tax">
                                            Porcentaje *
                                        </label>
                                        <input type="number" class="form-control" 
                                        id="percentage_discounts_charge_tax" 
                                        value="" 
                                        name="percentage_discounts_charge_tax" 
                                        placeholder="" required />
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="base_discounts_charge_tax">
                                            Base *
                                        </label>
                                        <input type="number" class="form-control" 
                                        id="base_discounts_charge_tax" 
                                        value="" 
                                        name="base_discounts_charge_tax" 
                                        placeholder="" required />
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="calculated_value_discounts_charge_tax">
                                            Valor *
                                        </label>
                                        <input type="number" class="form-control" 
                                        id="calculated_value_discounts_charge_tax" 
                                        value="" 
                                        name="calculated_value_discounts_charge_tax" 
                                        placeholder="" required />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm-12">
                                        <button type="button"
                                            class="btn btn-success"
                                            id="btn-save-discounts-charge-tax"
                                        >
                                            Guardar nuevo registro 
                                        </button>
                                        <button type="button"
                                            class="btn btn-warning"
                                            id="btn-clear-discounts-charge-tax"
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
                                        <th class="text-center">#</th>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Tipo</th>
                                        <th class="text-center">Código</th>
                                        <th class="text-center">Descripción</th>
                                        <th class="text-center">Porcentaje</th>
                                        <th class="text-center">Base</th>
                                        <th class="text-center">Valor</th>
                                        <th class="text-center" colspan="2">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody id="table-discounts-charge-tax">
                                    @php
                                    $line = 0;
                                    @endphp
                                    @foreach( $rowsDiscountChargeTax as $item )
                                    @php
                                    $line++;
                                    @endphp
                                    <tr id="line-discounts-charge-tax-{{$item->id}}">
                                        <td class="text-center">{{$line}}</td>
                                        <td class="text-center">{{$item->id}}</td>
                                        <td>{{$item->type}}</td>
                                        <td>{{$item->tax_code}}</td>
                                        <td>{{$item->description}}</td>
                                        <td class="text-center">{{$item->percentage}} %</td>
                                        <td>{{$item->base}}</td>
                                        <td>{{$item->calculated_value}}</td>
                                        <td class="text-center">
                                            <button type="button" 
                                                data-id="{{$item->id}}" 
                                                data-line="{{$line}}" 
                                                class="btn btn-success btn-edit-discounts-charge-tax"
                                                >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-danger btn-del-discounts-charge-tax"
                                                data-id="{{$item->id}}" 
                                                >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr> 
                                    @endforeach
                                    <!--
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td class="text-center">1</td>
                                        <td>aaa</td>
                                        <td>100</td>
                                        <td>asasaasa dcddc</td>
                                        <td class="text-center">5 %</td>
                                        <td>100000</td>
                                        <td>105000</td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-success"
                                                >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-danger"
                                                >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>

                        <!-- ============================ 
                        -->
                        <hr />
                        <br />
                        <h4>Items / Líneas</h4>


                        <br />
                        <h4>Cargos / Descuentos a nivel de Items / Líneas</h4>
                    
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

const getHTML = ( id_, type = 'id' ) => {
    if( type == 'id' )
        return document.getElementById( id_ ) 
    else
        return document.querySelectorAll(`${id_}`)
}

let inp_document_id
// Cargo/Descuento/Impuesto
let formDiscountsChargeTax, btnSaveDiscountsChargeTax, btnClearDiscountsChargeTax
let divDescriptionDiscountsChargeTax, divCodeDiscountsChargeTax
let inp_id_discounts_charge_tax, inp_line_next_discounts_charge_tax,  inp_type_discounts_charge_tax, inp_code_discounts_charge_tax, inp_description_discounts_charge_tax, inp_line_discounts_charge_tax, inp_percentage_discounts_charge_tax, inp_base_discounts_charge_tax, inp_calculated_value_discounts_charge_tax
let tableDiscountsChargeTax



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
                    if( type_row_ == 'DiscountsChargeTax' )
                    {
                        setFormDiscountsChargeTax( info )
                    }
                    

                }
            }
        }
    });
}


const showHideDivDiscountsChargeTax = ( open = false ) => {
    const display_ = formDiscountsChargeTax.style.display
    if( display_ != '' || open )
        formDiscountsChargeTax.style.display = ''
    else
        formDiscountsChargeTax.style.display = 'none'
}

const setTypeDiscountsChargeTax = ( type_ ) => {
    if( type_ == '' ){
        divCodeDiscountsChargeTax.style.display = 'none'
        divDescriptionDiscountsChargeTax.style.display = 'none'
    }
    else if( type_ == 'Impuesto' ){
        divCodeDiscountsChargeTax.style.display = ''
        divDescriptionDiscountsChargeTax.style.display = 'none'
    }
    else{
        divCodeDiscountsChargeTax.style.display = 'none'
        divDescriptionDiscountsChargeTax.style.display = ''
    }
}

const saveDiscountsChargeTax = () => {
    const document_id = inp_document_id.value
    const id = inp_id_discounts_charge_tax.value
    const type = inp_type_discounts_charge_tax.value
    const code = inp_code_discounts_charge_tax.value
    const description = inp_description_discounts_charge_tax.value
    const line = inp_line_discounts_charge_tax.value
    const percentage = inp_percentage_discounts_charge_tax.value
    const base = inp_base_discounts_charge_tax.value
    const calculated_value = inp_calculated_value_discounts_charge_tax.value

    const data = {
        id, 
        document_id, 
        type, 
        code, 
        description, 
        line, 
        percentage, 
        base, 
        calculated_value, 
    }
    console.log({ data })
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
                setHtmlDiscountsChargeTax( resp.data )
            }
        }
    });
}

const setEventsClik = ( btn_, id_, action_, type_row_ ) => {
    if( type_row_ == 'DiscountsChargeTax' )
    {
        if( action_ == 'edit' )
        {
            btn_.addEventListener('click', () => {
                getDataItem( id_, type_row_ )
            })
        }
    }
}


const setFormDiscountsChargeTax = ( data ) => { console.log({data})
    if( data.id != 0 ){
        inp_id_discounts_charge_tax.value = data.id
        inp_type_discounts_charge_tax.value = data.type
        inp_code_discounts_charge_tax.value = data.code
        if( data.description === null ) data.description = ''
        inp_description_discounts_charge_tax.value = data.description
        inp_percentage_discounts_charge_tax.value = data.percentage
        inp_base_discounts_charge_tax.value = data.base
        inp_calculated_value_discounts_charge_tax.value = data.calculated_value

        setTypeDiscountsChargeTax( data.type )
        showHideDivDiscountsChargeTax( true )
        btnSaveDiscountsChargeTax.innerHTML = 'Guardar actualización'
        btnClearDiscountsChargeTax.style.display = ''

        inp_line_discounts_charge_tax.value = data.line
    }
    else{
        inp_id_discounts_charge_tax.value = 0
        inp_type_discounts_charge_tax.value = ''
        inp_code_discounts_charge_tax.value = ''
        inp_description_discounts_charge_tax.value = ''
        inp_percentage_discounts_charge_tax.value = ''
        inp_base_discounts_charge_tax.value = ''
        inp_calculated_value_discounts_charge_tax.value = ''
        
        setTypeDiscountsChargeTax( '' )
        btnSaveDiscountsChargeTax.innerHTML = 'Guardar nuevo registro'
        btnClearDiscountsChargeTax.style.display = 'none'

        inp_line_discounts_charge_tax.value = 0
    }
}


const setHtmlDiscountsChargeTax = ( data ) => {
    let i, html_ = ''
    for( i in data )
    {
        let item_ = data[i]
        if( item_.description === null ) item_.description = ''
        html_ += `<tr id="line-discounts-charge-tax-${item_.id}">
            <td class="text-center">${i+1}</td>
            <td class="text-center">${item_.id}</td>
            <td>${item_.type}</td>
            <td>${item_.tax_code}</td>
            <td>${item_.description}</td>
            <td class="text-center">${item_.percentage} %</td>
            <td>${item_.base}</td>
            <td>${item_.calculated_value}</td>
            <td class="text-center">
                <button type="button" 
                    data-id="${item_.id}" 
                    data-line="{{$line}" 
                    class="btn btn-success btn-edit-discounts-charge-tax"
                    >
                    <i class="fas fa-edit"></i>
                </button>
            </td>
            <td class="text-center">
                <button type="button"
                    class="btn btn-danger btn-del-discounts-charge-tax"
                    data-id="${item_.id}" 
                    >
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr> `
    }
    tableDiscountsChargeTax.innerHTML = html_
    setEventDiscountsChargeTax()
}


const deleteDiscountsChargeTax = ( id_, type_row_ ) => {

}

const setEventDiscountsChargeTax = () => {
    const listBtns_ = getHTML('.btn-edit-discounts-charge-tax', 'class')
    for( let btn_ of listBtns_ ){
        const id_ = btn_.getAttribute('data-id')
        setEventsClik( btn_, id_, 'edit', 'DiscountsChargeTax' )
	}
    const listBtns2_ = getHTML('.btn-del-discounts-charge-tax', 'class')
    for( let btn_ of listBtns2_ ){
        const id_ = btn_.getAttribute('data-id')
		deleteDiscountsChargeTax( id_, 'DiscountsChargeTax' )
	}
}



$(document).ready(function () {

    divMessages = getHTML('div-messages')

    inp_id = getHTML('id') 
    inp_document_id = getHTML('document_id')

    let btnShowDiscountsChargeTax = getHTML('btn-show-discounts-charge-tax')
    btnSaveDiscountsChargeTax = getHTML('btn-save-discounts-charge-tax')
    btnClearDiscountsChargeTax = getHTML('btn-clear-discounts-charge-tax')
    formDiscountsChargeTax = getHTML('form-discounts-charge-tax')
    divDescriptionDiscountsChargeTax = getHTML('div-description-discounts-charge-tax')
    divCodeDiscountsChargeTax = getHTML('div-code-discounts-charge-tax')

    inp_id_discounts_charge_tax = getHTML('id_discounts_charge_tax')
    inp_line_next_discounts_charge_tax = getHTML('line_next_discounts_charge_tax')
    inp_line_discounts_charge_tax = getHTML('line_discounts_charge_tax')
    inp_type_discounts_charge_tax = getHTML('type_discounts_charge_tax')
    inp_code_discounts_charge_tax = getHTML('code_discounts_charge_tax')
    inp_description_discounts_charge_tax = getHTML('description_discounts_charge_tax')
    inp_percentage_discounts_charge_tax = getHTML('percentage_discounts_charge_tax')
    inp_base_discounts_charge_tax = getHTML('base_discounts_charge_tax')
    inp_calculated_value_discounts_charge_tax = getHTML('calculated_value_discounts_charge_tax')
    
    tableDiscountsChargeTax = getHTML('table-discounts-charge-tax')


    btnShowDiscountsChargeTax.addEventListener('click', ( ev ) => {
        showHideDivDiscountsChargeTax( ev.target.value )
    })
    btnSaveDiscountsChargeTax.addEventListener('click', ( ev ) => {
        saveDiscountsChargeTax()
    })
    btnClearDiscountsChargeTax.addEventListener('click', ( ev ) => {
        setFormDiscountsChargeTax({ id: 0 })
    })

    inp_type_discounts_charge_tax.addEventListener('change', ( ev ) => {
        setTypeDiscountsChargeTax( ev.target.value )
    })




    setEventDiscountsChargeTax()

})


</script>
@endsection
