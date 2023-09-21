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
                    Resolución 
                </div>

                @php
                    $resolution_types = config('app.equivalenDocument')['resolution_types'];

                    //echo '<pre>'.print_r($row).'</pre>';
                @endphp
               

                <div class="card-body">
                    <form action="{{url('/dianresolution/store')}}" method="POST" >
                        @csrf
                        <input type="hidden" name="id" id="id" value="{{$row->id}}" />
                        <!--
                        <input type="hidden" name="role_id" value="" /> 
                        -->
                        <div class="text-center font-weight-bold">
                            Información de la Resolución
                        </div>
                        <br />
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="type">
                                    Tipo de resolución *
                                </label>
                                <select class="form-control" id="type" name="type">
                                    <option value="">Seleccione..</option>
                                @foreach( $resolution_types as $typeRes )
                                @php
                                $sel_type = ( $typeRes == $row->type )? "selected='selected'":'';
                                @endphp
                                    <option value="{{$typeRes}}" {{$sel_type}} >
                                        {{$typeRes}}
                                    </option>  
                                @endforeach
                                </select>
                            </div>
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
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="resolution">Resolución *</label>
                                <input type="text" 
                                class="form-control " 
                                id="resolution" 
                                name="resolution" 
                                value="{{$row->resolution}}"
                                placeholder="" required />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="start">Inicio *</label>
                                <input type="number" class="form-control r" id="start" 
                                value="{{$row->start}}" 
                                name="start" placeholder="" required />
                            </div>
                
                            <div class="form-group col-sm-4">
                                <label for="end">
                                    Fin *
                                </label>
                                <input type="number" class="form-control " id="end" name="end"
                                value="{{$row->end}}" 
                                placeholder="" required />
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="date_due">
                                    Fecha vencimiento *
                                </label>
                                <input type="text" class="form-control" id="date_due" name="date_due" placeholder="yyyy-mm-dd" 
                                value="{{$row->date_due}}" 
                                data-provide="datepicker" data-date-format="yyyy-mm-dd" required />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="current">Actual *</label>
                                <input type="number" class="form-control " id="current"
                                value="{{$row->current}}" 
                                name="current" placeholder="" required />
                            </div>
                
                            <div class="form-group col-sm-4">
                                <label for="prefix">
                                    Prefijo *
                                </label>
                                <input type="text" class="form-control " id="prefix" 
                                value="{{$row->prefix}}" 
                                name="prefix" placeholder="" required />
                            </div>
                        </div>
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
                        <br />
                    </form>
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


const saveData = () => {

    const id = document.getElementById('id').value
    const type = document.getElementById('type').value
    const company_id = document.getElementById('company_id').value
    const resolution = document.getElementById('resolution').value
    const start = document.getElementById('start').value
    const end = document.getElementById('end').value
    const current = document.getElementById('current').value
    const date_due = document.getElementById('date_due').value
    const prefix = document.getElementById('prefix').value

    let errors = []
    if( ! validateData( type, 'string', true, 0 ) ){
        errors.push( 'Debe seleccionar un tipo de resolución' )
    }
    if( ! validateData( company_id, 'number', true, 1 ) ){
        errors.push( 'Debe seleccionar una compañía' )
    }
    if( ! validateData( resolution, 'string', true, 0 ) ){
        errors.push( 'Debe ingresar una resolución' )
    }
    if( ! validateData( start, 'number', true, 1 ) ){
        errors.push( 'Debe ingresar el inicio del consecutivo' )
    }
    if( ! validateData( end, 'number', true, 1 ) ){
        errors.push( 'Debe ingresar el fin del consecutivo' )
    }
    if( ! validateData( date_due, 'string', true, 0 ) ){
        errors.push( 'Debe ingresar la fecha de vencimiento de la resolución' )
    }
    if( ! validateData( current, 'number', true, 1 ) ){
        errors.push( 'Debe ingresar el consecutivo actual' )
    }
    if( ! validateData( prefix, 'string', true, 0 ) ){
        errors.push( 'Debe ingresar el prefijo de la resolución' )
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
        type,
        id,
        company_id,
        resolution,
        start,
        end,
        current,
        date_due,
        prefix,
    }

    $.ajax({
        data,
        url: urlBase_ + '/api/dianresolution/store',
        type:'POST',
        dataType :'JSON',
        success: function( resp ){
            if( resp.error ){
                divMessages.innerHTML = `<p>${resp.messages}</p>`
                $('#modal-messages').modal('show')
            }
            else{
                window.location.href = urlBase_ + '/dianresolution'
            }
        }
    });
}

$(document).ready(function () {

    divMessages = document.querySelector('#div-messages')
    const btnSave = document.querySelector('#btn-save')

    btnSave.addEventListener('click', () => {
        saveData()
    })

});






</script>

@endsection
