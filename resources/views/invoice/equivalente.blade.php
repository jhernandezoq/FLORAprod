@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Generar documento equivalente</div>
                    <br>
                    @if(($error) == 1)
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                  <span>Lo sentimos:</span>
                                  <li>No existe una resolución vigente configurada en el sistema para la empresa seleccionada</li>
                            </ul>
                        </div>
                    @endif
                    @if(($error) == 2)
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                  <span>Lo sentimos:</span>
                                  <li>La resolución actual no cuenta con consecutivos disponibles</li>
                            </ul>
                        </div>
                    @endif
                    <form action="{{url('/invoice/logequivalent')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="invoice_id" value="">
                        <input type="hidden" name="role_id" value="">
                        <h5 class="text-center"><strong>Información del proveedor</strong></h5><br>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="supplier_name">Nombre:</label>
                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" placeholder="" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="supplier_name">Identificación:</label>
                                <input type="text" class="form-control" id="supplier_id" name="supplier_id" placeholder="CC/Nit" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="ciudad">Ciudad:</label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" placeholder="" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="ciudad">Dirección:</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="ciudad">Teléfono:</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="date_data">Fecha documento:</label>
                                <input type="text" class="form-control" id="date_data" name="date_data" placeholder="yyyy-mm-dd" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
                            </div>
                        </div>
                        <h5 class="text-center"><strong>Información del adquiriente</strong></h5>
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <select class="form-control" id="compañia1" name="compañia1" required>
                                    <option value="">Selecciona la compañia...</option>
                                    @foreach($companies as $company)
                                      <option value="{{$company->code}}">{{$company->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div><br>

                            <div class="coces">
                                <h5 class="text-center"><strong>Valores del documento equivalente</strong></h5>
                                    <div class="form-row" style="margin-bottom: 1%;">
                                        <div class="col">
                                          <input type="text" class="form-control format-number" name="valor1" id="valor1" placeholder="Ingresa el valor" required>
                                        </div>
                                        <div class="col">
                                            <select class="form-control" name="currency1" id="currency1">
                                              <option value="COP" selected="selected">COP</option>
                                              <option value="USD">USD</option>
                                              <option value="EUR">EUR</option>
                                            </select>
                                        </div><br><br>
                                        <div class="form-group col-sm-12">
                                            <textarea class="form-control" id="concept1" name="concept1" rows="2" required placeholder="Ingresa el concepto"></textarea>
                                        </div>
                                    </div>
                                <div id="NuevoCampo"></div>
                                <img src="../img/agregar.png" alt="Agregar campo adicional" width="40" height="40" style="margin-left: 86%;" onclick="AgregarCampos();"
                                id="imagen_add">
                            </div><br>
                            <input type="text" name="countfields"  id="countfields" width="10px;" value="1" style="display: none;">

                        <div class="form-group">
                            <label for="description">Observación:</label>
                            <textarea class="form-control" id="concept" name="description" rows="3" aria-describedby="descriptionHelp" placeholder="Documento en proceso..."></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="file1">Documento adjunto 1:</label>
                                <input type="file" class="form-control" id="file1" name="file1" placeholder="" style="width: 95%;">
                            </div>
                            <div class="form-group col-sm-4">
                            <img src="../img/agregaradjunto.png" alt="Agregar campo adicional" width="30" height="30" style="margin-top: 7%;" onclick="AgregarCamposAdjuntos();"
                                id="imagen_add">
                            </div>
                            <input type="text" name="countfieldsadd"  id="countfieldsadd" width="10px;" value="1" style="display: none;">
                        </div>
                        <div id="NuevoCampoAdjuntos"></div>

                     <div class="form-group col-sm-12">
                        <input type="submit" class="btn btn-success" name="action" value="Generar">
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
