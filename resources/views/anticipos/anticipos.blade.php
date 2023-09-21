@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Solicitud de anticipos') }}</div>

                <div class="card-body">
                    <form action="{{url('/anticipos/save')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="invoice_id" value="">
                        <input type="hidden" name="role_id" value="">
                        <h5 class="text-center"><strong>Datos para pago</strong></h5><br>
                        <div class="form-row">
                            <div class="form-group col-sm-3">
                                <label for="supplier_name">Empresa:</label>
                                    <select class="form-control" id="empresa" name="empresa">
                                      <option>PEREZ Y CARDONA S.A.S</option>
                                      <option>M.P GALAGRO S.A.S</option>
                                      <option>SUPER AGRO S.A.S</option>
                                    </select>
                            </div>
                           <div class="form-group col-sm-3">
                                <label for="date_data">Fecha pago anticipo:</label>
                                <input type="text" class="form-control" id="fecha_anticipo" name="fecha_anticipo" placeholder="yyyy-mm-dd" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="ciudad">Valor de anticipo:</label>
                                <input type="text" class="form-control format-number" id="valor_anticipo" name="valor_anticipo" placeholder="" required>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="supplier_name">Forma de pago:</label>
                                    <select class="form-control" id="forma_pago" name="forma_pago">
                                      <option>Efectivo</option>
                                      <option>Transferencia</option>
                                    </select>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="supplier_name">Tipo usuario para anticipo:</label>
                                    <select class="form-control" id="tipo_usuario" name="tipo_usuario" onchange="TipoUsuario();">
                                      <option>Usuario</option>
                                      <option>Proveedor</option>
                                    </select>
                            </div>
                            <div class="form-group col-sm-12" style="display: none;" id="name_proveedor">
                                <label for="supplier_id">Proveedor:</label><br>
                                <select class="form-control" id="supplier_id" name="supplier_id" style="width: 100%; height: 40px;">
                                    <option value="">Seleccionar Proveedor...</option>   
                                    @foreach($suppliers as $sup)
                                        <option value="{{$sup->id}}">{{$sup->name}} | {{$sup->nit}}</option>  
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="supplier_id">Seleccione el/la director/a para aprobación:</label><br>
                                <select class="form-control" id="id_director" name="id_director" style="width: 100%; height: 40px;">
                                    <option value="">Seleccionar director/a...</option>   
                                    @foreach($directores as $director)
                                        <option value="{{$director->id}}">{{$director->name}} - {{$director->profile}}</option>  
                                    @endforeach
                                </select>
                            </div>

                            <div id="NuevoCampo"></div>
                            <input type="text" name="countfields"  id="countfields" width="10px;" value="1" style="display: none;">


                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="file1">Documento adjunto 1:</label>
                                <input type="file" class="form-control" id="file1" name="file1" placeholder="" style="width: 350px;">
                            </div>
                            <div class="form-group col-sm-4">
                            <img src="../img/agregaradjunto.png" alt="Agregar campo adicional" width="30" height="30" style="margin-top: 7%;" onclick="AgregarCamposAdjuntos();"
                                id="imagen_add">
                            </div>
                            <input type="text" name="countfieldsadd"  id="countfieldsadd" width="10px;" value="1" style="display: none;">
                        </div>
                        <div id="NuevoCampoAdjuntos"></div>

                            <div class="form-group col-sm-12">
                                <label for="ciudad">Concepto:</label>
                                <textarea class="form-control" id="concepto_anticipo" name='concepto_anticipo' rows="4" required=""></textarea>
                            </div>
                            <div class="row justify-content-center" style="margin-left: 45%;" id="boton_guardar">
                                <input type="submit" class="btn btn-success" name="guardar" value="Guardar"  style="margin-top: 4%;" onclick="esconder_boton();">
                            </div>
                        </div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script type="text/javascript">

$(document).ready(function () {
  $(".format-number").on({
    "focus": function (event) {
        $(event.target).select();
    },
    "keyup": function (event) {
        $(event.target).val(function (index, value ) {
            return value.replace(/[^\d\,]/g,"")
                        //.replace(/([0-9])([0-9]{2})$/, '$1,$2')
                        .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
        });
    }
    });

   $('#id_director').select2();

});




function GenerarExcel(){
$(document).ready(function () {
    $("#directorio").table2excel({
        filename: "directorio.xls"
    });
});

}

function esconder_boton(){
    $('#boton_guardar').hide('slow');
}


function TipoUsuario(){
    var TipoUsuario=$("#tipo_usuario").val();
    if (TipoUsuario == 'Proveedor') {
      $("#name_proveedor").show('slow');
      $("#name_proveedor").prop('required', true);
      $('#supplier_id').select2();
    }else{
      $("#name_proveedor").hide('slow');
      $("#name_proveedor").prop('required', false); 
    }
}





var i=1;
function AgregarCampos(){
  i=i+1;
 $("#countfields").val(i);
 $("#NuevoCampo").before("<div class='form-row' id='campo"+i+"' style='margin-bottom: 1%;''><div class='col'><input type='text' class='form-control format-number' name='valor"+i+"' id='valor"+i+"' placeholder='Ingresa el valor' required></div><div class='col'><select class='form-control' name='currency"+i+"' id='currency"+i+"'><option value='COP' selected='selected'>COP</option><option value='USD'>USD</option><option value='EUR'>EUR</option></select></div><br><br><div class='form-group col-sm-12'><textarea class='form-control' id='concept"+i+"' name='concept"+i+"' rows='2' required placeholder='Ingresa el concepto'></textarea></div><div><img src='../img/eliminar.png' alt='Eliminar registro' width='30' height='30' style='margin-left: 10%;' onclick='EliminarCampo("+i+");' id='imagen_add"+i+"'></div></div></div>");
  $(".format-number").on({
    "focus": function (event) {
        $(event.target).select();
    },
    "keyup": function (event) {
        $(event.target).val(function (index, value ) {
            return value.replace(/[^\d\,]/g,"")
                        //.replace(/([0-9])([0-9]{2})$/, '$1,$2')
                        .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
        });
    }
    });
}




j=1;
function AgregarCamposAdjuntos(){
  j=j+1;
 $("#countfieldsadd").val(j);
 $("#NuevoCampoAdjuntos").before("<div class='form-row' id='campoadjunto"+j+"'><div class='form-group col-sm-4'><label for='file' id='divadjunto"+j+"'>Documento adjunto "+j+":</label><input type='file' class='form-control' id='file"+j+"' name='file"+j+"' placeholder='' style='width: 350px;'></div><div class='form-group col-sm-4'><img src='../img/eliminar.png' alt='Agregar campo adicional' width='30' height='30' style='margin-top: 10%;' onclick='EliminarCampoAdjunto("+j+");' id='imagen_delete"+j+"'></div></div>");
  $(".format-number").on({
    "focus": function (event) {
        $(event.target).select();
    },
    "keyup": function (event) {
        $(event.target).val(function (index, value ) {
            return value.replace(/[^\d\,]/g,"")
                        //.replace(/([0-9])([0-9]{2})$/, '$1,$2')
                        .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
        });
    }
    });
}


function EliminarCampo(id){
    i=i-1;
    $("#countfields").val(i);
    $("#concept"+id).hide('slow', function(){ 
        $("#concept"+id).remove(); });
    $("#currency"+id).hide('slow', function(){ 
        $("#currency"+id).remove(); });
    $("#valor"+id).hide('slow', function(){ 
        $("#valor"+id).remove(); });
    $("#compañia"+id).hide('slow', function(){ 
        $("#compañia"+id).remove(); });
    $("#imagen_add"+id).hide('slow', function(){ 
        $("#imagen_add"+id).remove(); });
    $("#campo"+id).hide('slow', function(){ 
        $("#campo"+id).remove(); });
}


function EliminarCampoAdjunto(id){
    j=j-1;
    $("#countfieldsadd").val(j);
    $("#file"+id).hide('slow', function(){ 
    $("#file"+id).remove(); 
    });
    $("#imagen_delete"+id).hide('slow', function(){ 
    $("#imagen_delete"+id).remove(); 
    });
    $("#divadjunto"+id).hide('slow', function(){ 
    $("#divadjunto"+id).remove(); 
    });
    $("#campoadjunto"+id).hide('slow', function(){ 
    $("#campoadjunto"+id).remove(); });
}

</script>

@endsection
