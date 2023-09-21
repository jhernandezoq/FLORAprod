@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Anticipo # {{$id_documento}}</div>

                <div class="card-body">
                    <form action="{{url('/legalizacion/save')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h5 class="text-center"><strong>Datos para legalización</strong></h5><br>
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{$id_documento}}">

                            <div class="coces" style="border:  1px solid; border-radius: 25px; border-left: 10%; background-color: #e4ffe0; border-width: 90%; border-style: none;">
                                <br><h5 class="text-center"><strong>Centros de Costos</strong></h5><br><br>
                                    <div class="form-row" style="margin-bottom: 1%; margin-left: 2%;margin-right: 2%;">
                                        <div class="col">
                                            <select class="form-control" id="coce" name="coce1" required>
                                                <option value="">Centro de costos...</option>   
                                                @foreach($costCenters as $coce)
                                                    <option value="{{$coce->id}}">{{$coce->name}} - {{$coce->code}}</option>  
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                          <input type="text" class="form-control format-number" name="value1" id="value1" placeholder="Valor" required>
                                        </div>
                                        <div class="col">
                                            <select class="form-control" id="cuenta" name="cuenta1" required>
                                                <option value="">Cuentas...</option>   
                                                @foreach($cuentas as $cuenta)
                                                    <option value="{{$cuenta->id}}">{{$cuenta->cuenta}}</option>  
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                <div id="NuevoCampo"></div><br>
                                <img src="../../img/agregar.png" alt="Agregar centro de costo" width="70" height="70" style="margin-left: 86%;margin-bottom: 1%;" id="imagen_add_anticipo" onclick="AgregarCampos();"
                                id="imagen_add">
                            </div>
                            <input type="text" name="countfields"  id="countfields" width="10px;" value="1" style="display: none;">
                        <div class="form-row">
                            <div class='coces' id='campoadjunto1'><div class='form-group col-sm-6'><label for='file' id='divadjunto1'>Documento adjunto 1:</label><input type='file' class='form-control' id='file1' name='file1' placeholder='' style='width: 350px; margin-left: :1%;' required="required"></div><div class='form-group col-sm-4'></div></div>
                            <div class="form-group col-sm-4">
                            <img src="../../img/agregarnuevo.png" alt="Agregar campo adicional" width="80" height="80" style="margin-top: 7%;" onclick="AgregarCamposAdjuntos();"
                                id="imagen_add">
                            </div>
                            <input type="text" name="countfieldsadd"  id="countfieldsadd" width="10px;" value="1" style="display: none;">
                        </div>
                        <div id="NuevoCampoAdjuntos"></div>

                            <div class="form-group col-sm-12">
                                <label for="supplier_id">Seleccione el/la director/a para aprobación:</label><br>
                                <select class="form-control" id="id_director" name="id_director" style="width: 100%; height: 40px;">
                                    <option value="">Seleccionar director/a...</option>   
                                    @foreach($directores as $director)
                                        <option value="{{$director->id}}">{{$director->name}}</option>  
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-sm-12">
                                <label for="ciudad">Concepto:</label>
                                <textarea class="form-control" id="concepto_anticipo" name='concepto_anticipo' rows="4" required=""></textarea>
                            </div>
                            <div class="row justify-content-center">
                                <input type="submit" class="btn btn-success" name="guardar" value="Guardar"  style="margin-top: 4%;">
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
 $('#coce').select2();
 $('#cuenta').select2();
 $('#id_director').select2();
});




function GenerarExcel(){
$(document).ready(function () {
    $("#directorio").table2excel({
        filename: "directorio.xls"
    });
});

}


function TipoUsuario(){
    var TipoUsuario=$("#tipo_usuario").val();
    if (TipoUsuario == 'Proveedor') {
      $("#name_proveedor").show('slow');
      $("#name_proveedor").prop('required', true);
    }else{
      $("#name_proveedor").hide('slow');
      $("#name_proveedor").prop('required', false); 
    }
}


var i=1;
function AgregarCampos(){
  i=i+1;
  $("#countfields").val(i);
 $("#NuevoCampo").before("<div class='form-row' id='campo"+i+"' style='margin-bottom: 1%; margin-left: 2%;margin-right: 2%;'><div class='col'><select class='form-control' id='coce' name='coce"+i+"'><option value=''>Centro de costos...</option>@foreach($costCenters as $coce)<option value='{{$coce->id}}'>{{$coce->name}} - {{$coce->code}}</option>@endforeach</select></div><div class='col'> <input type='text' class='form-control format-number' name='value"+i+"' id='value"+i+"' placeholder='Valor' required></div><div class='col'><select class='form-control' id='cuenta' name='cuenta"+i+"' required><option value=''>Cuentas...</option>@foreach($cuentas as $cuenta)<option value='{{$cuenta->id}}'>{{$cuenta->cuenta}}</option>@endforeach</select></div><div><img src='../../img/eliminar.png' alt='Eliminar registro' width='30' height='30' style='margin-left: 10%;' onclick='EliminarCampo("+i+");' id='imagen_add'></div></div>");
 $("#coce").select2();
 $("#cuenta").select2();

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
 $("#NuevoCampoAdjuntos").before("<div class='coce' id='campoadjunto"+j+"'><div class='form-group col-sm-6'><label for='file' id='divadjunto"+j+"'>Documento adjunto "+j+":</label><input type='file' class='form-control' id='file"+j+"' name='file"+j+"' placeholder='' style='width: 350px;' required='required'></div><div class='form-group col-sm-6'><img src='../../img/eliminar.png' alt='Agregar campo adicional' width='30' height='30' style='margin-top: 10%;' onclick='EliminarCampoAdjunto("+j+");' id='imagen_delete"+j+"'></div></div>");
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
