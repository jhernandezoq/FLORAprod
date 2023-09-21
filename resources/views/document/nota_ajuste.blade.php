@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Creación nota de ajuste') }}</div>
                <div class="card-body">
                    <form action="{{url('/ajuste/save')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <input type="hidden" name="role_id" value="">
                        @foreach($rows as $row)
                        <input type="hidden" name="id" value="{{$row->id}}">
                        <h5 class="text-center"><strong>Datos principales del documento</strong></h5><br>
                        <div class="form-row">
                            <div class="form-group col-sm-3">
                                <label for="supplier_name">Compañia:</label>
                                <input type="text" class="form-control" id="company" name="company" value="{{$row->company}}" disabled>
                            </div>
                           <div class="form-group col-sm-3">
                                <label for="date_data">Proveedor:</label>
                                <input type="text" class="form-control" id="supplier" name="supplier" value="{{$row->supplier_reason}}" disabled>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="ciudad">Num. Documento:</label>
                                <input type="text" class="form-control format-number" id="num_documento" name="num_documento" value="{{$row->document_number}}" disabled>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="supplier_name">Fecha de pago:</label>
                                <input type="text" class="form-control" id="fecha_pago" name="fecha_pago" value="{{$row->date_due_payment}}" disabled>
                            </div>

                            
                            <div class="coces" style="padding: 20px; background-color:#e4ffe0;border-radius: 20px; margin-top:10%;margin: 0 auto;">
                                <h5 class="text-center"><strong>Items del documento</strong></h5>
                                <input type="hidden" value="{{$countLines}}" name="countLines">
                                @php
                                $cantidad_items=0;
                                @endphp
                                @foreach($rowsDocumentLine as $lines)

                                    <div class="form-row" style="margin-bottom: 1%;">
                                        <div class="col">
                                          <label for="cantidad">Cantidad:</label>
                                          <input type="text" class="form-control" name="cantidad_{{$cantidad_items}}" id="cantidad_{{$cantidad_items}}" value="{{ $lines->quantity }}">
                                        </div>
                                        <div class="col">
                                          <label for="description">Descripción:</label>
                                          <input type="text" class="form-control" name="description_{{$cantidad_items}}" id="description_{{$cantidad_items}}" value="{{ $lines->item_description }}">
                                        </div>
                                        <div class="col">
                                          <label for="valor_bruto">Valor bruto:</label>
                                          <input type="text" class="form-control" name="valor_bruto_{{$cantidad_items}}" id="valor_bruto_{{$cantidad_items}}" value="{{ $lines->price }}">
                                        </div>
                                        <div class="col">
                                          <label for="valor_descuentos">Valor descuentos:</label>
                                          <input type="text" class="form-control" name="valor_descuentos_{{$cantidad_items}}" id="valor_descuentos_{{$cantidad_items}}" value="{{ $lines->perc_discount }}">
                                        </div>
                                        <div class="col">
                                          <label for="valor_total">Valor total:</label>
                                          <input type="text" class="form-control" name="valor_total_{{$cantidad_items}}" id="valor_total_{{$cantidad_items}}" value="{{ $lines->total }}">
                                        </div>
                                    </div>
                                    @php
                                        $cantidad_items=$cantidad_items+1;
                                        @endphp
                                    @endforeach
                            </div><br>
                            <div class="form-group col-sm-12" style="margin-top:2%;">
                                <label for="nota_ajuste">Nota:</label>
                                <textarea class="form-control" id="nota_ajuste" name='nota_ajuste' rows="4" required=""></textarea>
                            </div>
                            <div class="row justify-content-center" style="margin-left: 45%;" id="boton_guardar">
                                <input type="submit" class="btn btn-success" name="guardar" value="Guardar"  style="margin-top: 4%;">
                            </div>
                        </div>
                        <br>
                        @endforeach
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

   $("#valor_total").change(function(){
     alert("The text has been changed.");
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
