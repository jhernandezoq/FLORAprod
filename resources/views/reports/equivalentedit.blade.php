@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Edición documento equivalente</div>
                    <br>
                    <form action="{{url('/reports/equivalentupdate')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="invoice_id" value="">
                        <input type="hidden" name="role_id" value="">
                        <h5 class="text-center"><strong>Información del proveedor</strong></h5><br>
                        <div class="form-row">
                            @foreach($information AS $info)
                            <input type="text" name="document_id" value="{{$info->id}}" style="display: none;">
                            <div class="form-group col-sm-4">
                                <label for="supplier_name">Nombre:</label>
                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" placeholder="" required value="{{$info->proveedor}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="supplier_name">Identificación:</label>
                                <input type="text" class="form-control" id="supplier_id" name="supplier_id" placeholder="CC/Nit" required value="{{$info->id_proveedor}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="ciudad">Ciudad:</label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" placeholder="" required value="{{$info->city}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="ciudad">Dirección:</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="" required value="{{$info->address}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="date_data">Estado:</label>
                                    <select name="estado" id="estado" class="form-control">
                                      <option value="{{$info->estado}}" selected="selected">{{$info->estado}}</option>
                                      <option value="Radicada">Radicada</option>
                                      <option value="Finalizada">Finalizada</option>
                                    </select> 
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="date_data">Fecha documento:</label>
                                <input type="text" class="form-control" id="date_data" name="date_data" placeholder="yyyy-mm-dd" data-provide="datepicker" data-date-format="yyyy-mm-dd" required value="{{$info->fecha_documento}}">
                            </div>
                            @endforeach
                        </div><br>
                     <div class="form-group col-sm-12">
                        <input type="submit" class="btn btn-success" name="action" value="Actualizar" style="margin-left: 45%;">
                    </div>

                    </form>
                    <br>
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

  $("#valor").on('paste', function(e){
    e.preventDefault();
    alert('Esta acción está prohibida');
  })
  
  $("#valor").on('copy', function(e){
    e.preventDefault();
    alert('Esta acción está prohibida');
  })

});


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
 $("#NuevoCampoAdjuntos").before("<div class='form-row' id='campoadjunto"+j+"'><div class='form-group col-sm-4'><label for='file' id='divadjunto"+j+"'>Documento adjunto "+j+":</label><input type='file' class='form-control' id='file"+j+"' name='file"+j+"' placeholder='' style='width: 95%;'></div><div class='form-group col-sm-4'><img src='../img/eliminar.png' alt='Agregar campo adicional' width='30' height='30' style='margin-top: 10%;' onclick='EliminarCampoAdjunto("+j+");' id='imagen_delete"+j+"'></div></div>");
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


function VerificacionPegado(){
    alert("Ingreso aqui");
}

</script>
@endsection
