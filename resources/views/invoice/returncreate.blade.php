@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Devoluciones</div>
                @if($validacion == 1)
                <div class="card-body">
                        <div class="alert alert-success" role="alert">
                            <ul>
                                  <span>Excelente!!</span>
                                  <li>La solicitud fue enviada de forma exitosa</li>
                            </ul>
                        </div>
                </div>
                @endif
                <div class="card-body">
                    <form action="{{url('devoluciones/save')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h5 class="text-center"><strong>Datos para devoluciones</strong></h5><br>
                      <div class="form-row">
                        <div class="form-group col-sm-4">
                                <label for="date_data">Nombre del cliente:</label>
                                <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" placeholder="Nombre..." required>
                            </div>
                        <div class="form-group col-sm-4">
                                <label for="date_data">Identificación del cliente:</label>
                                <input type="text" class="form-control" id="id_cliente" name="id_cliente" placeholder="Identificación..." required>
                        </div>
                        <div class="form-group col-sm-4">
                                <label for="date_data">Fecha soporte transacción:</label>
                                <input type="text" class="form-control" id="fecha_pago" name="fecha_pago" placeholder="yyyy-mm-dd" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
                        </div>

                            <div class="form-group col-sm-4">
                                <label for="supplier_name">Área:</label>
                                    <select class="form-control" id="area" name="area" required>
                                      <option selected>Seleccione el area...</option>
                                      <option value="1">Punto de venta</option>
                                      <option value="2">Domicilios</option>
                                      <option value="7">Clientes a credito/contado</option>
                                    </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="supplier_name">Cuenta bancaria a la que entro el pago del cliente:</label>
                                    <select class="form-control" id="forma_pago" name="forma_pago" required>
                                      <option>Banco de Bogotá-Corriente</option>
                                      <option>Banco de Bogotá/PAYU-Corriente</option>
                                      <option>Bancolombia-Ahorros</option>
                                      <option>Bancolombia-Corriente</option>
                                      <option>Bancolombia-Ahorros QR</option>
                                      <option>Banco de Occidente-Corriente</option>
                                      <option>Banco Davivienda-Ahorros</option>
                                      <option>Banco Agrario-Corriente</option>
                                      <option>BBVA-Corriente</option>
                                      <option>Banco Popular-Corriente</option>
                                      <option>ITAU-Corriente</option>
                                    </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="date_data">Valor de devolución:</label>
                                <input type="text" class="form-control format-number" id="valor" name="valor" placeholder="Valor..." required>
                        </div>
                            <br><br>
                            </div>
                            <div class="card-body">
                        <div class="alert alert-warning" role="warning">
                            <ul>
                                  <span>ATENCIÓN</span><br>
                                  <span>Recuerda adjuntar los siguientes documentos en los casos que aplique</span>
                                  <li>Certificación bancaria del cliente</li>
                                  <li>Constancia o recibo del pago realizado por el cliente</li>
                                  <li>Carta de la solicitud de la devolución</li>
                                  <li>Formato de devolución en excel</li>
                                  <li>Factura de venta</li>
                            </ul>
                            <h6>Nota: Valores superiores a $100.000 deben tener certificado bancario</h6>
                        </div>
                        </div>
                        <div class="form-row">
                            <div class='coces' id='campoadjunto1'><div class='form-group col-sm-6'><label for='file' id='divadjunto1'>Documento adjunto 1:</label><input type='file' class='form-control' id='file1' name='file1' placeholder='' style='width: 350px; margin-left: :1%;' required="required"></div><div class='form-group col-sm-4'></div></div>
                            <div class="form-group col-sm-4">
                            <img src="../img/agregarnuevo.png" alt="Agregar campo adicional" width="80" height="80" style="margin-top: 7%;" onclick="AgregarCamposAdjuntos();"
                                id="imagen_add">
                            </div>
                            <input type="text" name="countfieldsadd"  id="countfieldsadd" width="10px;" value="1" style="display: none;">
                        </div>
                        <div id="NuevoCampoAdjuntos"></div>

                            <div class="form-group col-sm-12">
                                <label for="ciudad">Motivo de devolución:</label>
                                <textarea class="form-control" id="motivo_devolucion" name='motivo_devolucion' rows="4" required=""></textarea>
                            </div>

                            <div class="form-group col-sm-12">
                                <label for="ciudad">Observación:</label>
                                <textarea class="form-control" id="observacion_devolucion" name='observacion_devolucion' rows="4"></textarea>
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


j=1;
function AgregarCamposAdjuntos(){
  j=j+1;
 $("#countfieldsadd").val(j);
 $("#NuevoCampoAdjuntos").before("<div class='coce' id='campoadjunto"+j+"'><div class='form-group col-sm-6'><label for='file' id='divadjunto"+j+"'>Documento adjunto "+j+":</label><input type='file' class='form-control' id='file"+j+"' name='file"+j+"' placeholder='' style='width: 350px;' required='required'></div><div class='form-group col-sm-6'><img src='../img/eliminar.png' alt='Agregar campo adicional' width='30' height='30' style='margin-top: 10%;' onclick='EliminarCampoAdjunto("+j+");' id='imagen_delete"+j+"'></div></div>");
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
