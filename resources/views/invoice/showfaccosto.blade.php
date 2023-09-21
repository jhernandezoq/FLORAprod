@extends('layouts.app')

@section('content')


  <!-- Modal -->
  <div class="modal fade" role="dialog" id="modal_rechazo">
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content" style="height:250px;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Motivos para el rechazo</h4>
        </div>
            <div style="margin-left:2%; margin-right:2%;">
                <input type="hidden"  id="invoice_id" name="invoice_id" value="{{$invoice->id}}">
                <input type="hidden" id="id_user" name="id_user" value="{{$user}}">
                <label for="motivo_rechazo_id">Seleccione el motivo del rechazo:</label>
                <select class="form-control" id="motivo_rechazo_id" name="motivo_rechazo_id" style="margin-bottom=2%;" onchange="ValidacionRechazo();">
                    <option value="1">Error al radicar la factura</option>
                    <option value="3">Datos errados en valores de la factura(Actualiza cambio en cadena)</option>
                    <option value="4">Los productos o servicios no fueron recibidos(Actualiza cambio en cadena)</option>
                </select>       
            </div>
        <div class="modal-footer">
          <input type="button" class="btn btn-success" name="action" value="Enviar" onclick="EnvioRechazo();">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Factura {{$invoice->supplier->name}} - {{$invoice->number}}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col d-flex flex-row justify-content-center">
                            <div class="d-flex flex-column text-center">
                                <a href="{{asset('facturas')}}/{{$invoice->file}}" target="_blank"><svg width="100px" height="100px" viewBox="0 0 16 16" class="bi bi-file-earmark-text-fill" fill="#52b788" xmlns="http://www.w3.org/2000/svg">
                                  <path fill-rule="evenodd" d="M2 2a2 2 0 0 1 2-2h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm7 2l.5-2.5 3 3L10 5a1 1 0 0 1-1-1zM4.5 8a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zM4 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5z"/>
                                </svg></a>
                                <br>
                                <h1>{{ucfirst($invoice->concept)}}</h1>
                                <h3>TOTAL: ${{number_format($invoice->total,2)}} {{$invoice->currency}}</h3>
                                <input type="text" name="valorFactura" id="valorFactura" style="display: none;" value="{{$invoice->total}}">
                                <h6>SUBTOTAL: ${{number_format($invoice->subtotal,2)}} {{$invoice->currency}}</h6>
                            </div>
                        </div>
                    </div>
                    <br>
                    <form action="{{url('logfaccosto')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                        <div class="form-group">
                            <label for="description">Observación:</label>
                            <textarea class="form-control" id="concept" name="description" rows="3" aria-describedby="descriptionHelp" placeholder="Factura en proceso..."></textarea>
                        </div>
                        <br>

                        <input type="hidden" name="role_user" value="{{$role}}">

                        @if($role == 2)
                        <div class="form-group">
                            <label for="approver_id">Tipo de novedad:</label>
                            <select class="form-control" id="novedad" name="novedad">
                                <option value="">Selecciona el tipo de novedad</option>   
                                @foreach($invoice_news as $invoice_new)
                                    <option value="{{$invoice_new->description}}">{{$invoice_new->description}}</option> 
                                @endforeach

                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Orden de compra:</label>
                            <input class="form-control" id="orden_compra" name="orden_compra">
                        </div>
                        @endif
                        <div class="form-group">
                            <label for="file">Archivo Soporte:</label>
                            <input type="file" class="form-control" id="file" name="file" placeholder="">
                        </div>
                        <div class="loader" id="loader" style='display:none; margin-left:40%;'></div>
                        <div class="form-group">
                            <label for="approver_id">Solicitar Aprobación de:</label>
                            <select class="form-control" id="approver_id" name="approver_id" required onchange="ValidacionProceso();">
                                <option value="">Seleccionar Aprobador...</option>   
                                @foreach($approvers as $ap)
                                    <option value="{{$ap->user->id}}">{{$ap->user->name}}</option> 
                                @endforeach
                            </select>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col d-flex flex-row justify-content-center">
                                 <div class="d-flex flex-column text-center" style="margin-right:1%;">
                                    <input type="button" class="btn btn-danger" onclick="ModalRechazo();" value="Rechazar">
                                </div>
                                @if($role == 1)
                                    <div class="d-flex text-center" style="margin-right:1%;">
                                                <input type="submit" class="btn btn-warning" name="action" value="Validar" id="BotonValidar">
                                    </div>
                                    <div class="d-flex text-center" style="margin-right:1%;">
                                            <input type="submit" class="btn btn-success" name="action" value="Aprobar" id="BotonAprobar">
                                    </div>

                                    <div class="d-flex text-center" style="margin-right:1%;">
                                            <input type="button" class="btn btn-warning" name="action" value="Validar" onclick="Validacion('Validar');" style="display:none;" id="BotonCadenaValidar">
                                    </div>
                                    <div class="d-flex text-center" style="margin-right:1%;">
                                            <input type="button" class="btn btn-success" name="action" value="Aprobar" onclick="Validacion('Aprobar');" style="display:none;" id="BotonCadenaAprobar">
                                    </div>

                                    <div class="d-flex text-center">
                                        <input type="submit" class="btn btn-info" name="action" value="Sin ingreso">
                                    </div>
                                @else
                                    <div class="d-flex text-center" style="margin-right:1%;">
                                                <input type="submit" class="btn btn-success" name="action" value="Aprobar">
                                    </div>
                                @endif

                                @if($role == 3)
                                <div class="d-flex text-center">
                                        <input type="button" class="btn btn-info" name="action" value="Prueba" onclick="Validacion('Aprobar');">
                                </div>
                                @endif

                                
                            </div>
                        </div>
                    </form>
                    <br>
                    <div class="row">
                        <div class="col">
                            <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Responsable</th>
                                        <th>Estado</th>
                                        <th>Observación</th>
                                        <th>Soporte</th>
                                        <th>Autorizado por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->log as $log)
                                    <tr>
                                        <td>{{$log->created_at}}</td>
                                        <td>{{$log->user->name}}</td>
                                        <td>{{$log->state->name}}</td>
                                        <td>{{$log->description}}</td>
                                        @if($log->file <> NULL)
                                            <td><a href="{{asset('facturas')}}/{{$log->file}}">soporte</a></td>
                                        @else
                                        <td></td>
                                        @endif
                                        @if($log->autorizacion <> NULL)
                                        <td>{{$log->autorizacion}}</td>
                                        @else
                                        <td></td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function () {
  $('#coce1').select2();
  $('#autorization_user').select2();
  //$('#ubication1').select2();
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


});


var i=1;
function AgregarCampos(){
  i=i+1;
  $("#countfields").val(i);
 $("#NuevoCampo").before("<div class='form-row' id='campo"+i+"' style='margin-bottom:1%;'><div class='col'><select class='form-control' id='coce"+i+"' name='coce"+i+"'><option value=''>Centro de costos...</option>@foreach($costCenters as $coce)<option value='{{$coce->id}}'>{{$coce->name}} - {{$coce->code}}</option>@endforeach</select></div><div class='col'><input type='text' class='form-control' name='percent"+i+"' id='percent"+i+"' placeholder='Porcentaje' disabled='disabled'><input type='text' class='form-control' name='percenta"+i+"' id='percenta"+i+"' placeholder='Porcentaje' style='display: none;'></div><div class='col'> <input type='text' class='form-control format-number' name='value"+i+"' id='value"+i+"' placeholder='Valor' required onchange='CalculoPorcentaje("+i+");'></div><div><img src='../img/eliminar.png' alt='Eliminar registro' width='30' height='30' style='margin-left: 10%;' onclick='EliminarCampo("+i+");' id='imagen_add'></div></div>");
 $("#coce"+i).select2();

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


var k=1;
function AgregarCamposCC(){
  k=k+1;
  $("#countfieldsCC").val(k);
 $("#NuevoCampoCC").before("<div class='form-row' id='campoCC"+k+"' style='margin-bottom:1%;'><div class='col'><select class='form-control' id='ubication"+k+"' name='ubication"+k+"'><option value=''>Centro de costos...</option>@foreach($costCenters as $coce)<option value='{{$coce->id}}'>{{$coce->name}} - {{$coce->code}}</option>@endforeach</select></div><div><img src='../img/eliminar.png' alt='Eliminar registro' width='25' height='25' style='margin-left: 3%;' onclick='EliminarCampoCC("+k+");' id='imagen_add'></div></div>");

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
    $("#percenta"+id).remove();
    $("#campo"+id).hide('slow', function(){ 
        $("#campo"+id).remove(); });
}

function EliminarCampoCC(id){
    k=k-1;
    $("#countfieldsCC").val(k);
    $("#campoCC"+id).hide('slow', function(){ 
        $("#campoCC"+id).remove(); });
}


function ModalRechazo(){
    $('#modal_rechazo').modal('show');
}


function EnvioRechazo(){

    var motivoRechazo=$("#motivo_rechazo_id").val();
    var token=$("input[name=_token]").val();
    var motivo_rechazo = $("#motivo_rechazo_id").val();
    var id_factura = $("#invoice_id").val();
    var id_rol = $("#rol_id").val();
    var id_user = $("#id_user").val();
    
    if((motivoRechazo == '3') || (motivoRechazo == '4')){

        swal({
            icon: 'warning',
            title: "¿Esta seguro/a de rechazar la factura?",
            text: 'Recuerde que esta acción actualiza la información en la DIAN',
            buttons: ["Aceptar", "Cancelar"],
            dangerMode: true,
        })
            .then(isClose => {
                if (isClose) {
                    return false;
                } else {
                    $.ajax({
                            data:{ token, motivo_rechazo, id_factura, id_user},
                            url: 'https://flora.tierragro.com/api/rechazofactura',
                            type:'POST',
                            dataType :'JSON',
                            success:function(data){
                                datas = JSON.parse(data);
                                window.location.href = 'https://flora.tierragro.com/invoice/faccosto';
                            }
                        });
            }
        });

    }else{
        $.ajax({
                data:{ token, motivo_rechazo, id_factura, id_user},
                url: 'https://flora.tierragro.com/api/rechazofactura',
                type:'POST',
                dataType :'JSON',
                success:function(data){
                    datas = JSON.parse(data);
                    window.location.href = 'https://flora.tierragro.com/invoice/faccosto';
                }
            });

    }









  /*  $('.close').trigger('click');
    $("#loader").show('slow');
    var token=$("input[name=_token]").val();
    var motivo_rechazo = $("#motivo_rechazo_id").val();
    var id_factura = $("#invoice_id").val();
    var id_rol = $("#rol_id").val();
    var id_user = $("#id_user").val();

    $.ajax({
      data:{ token, motivo_rechazo, id_factura, id_user},
      url: 'https://flora.tierragro.com/api/rechazofactura',
      type:'POST',
      dataType :'JSON',
      success:function(data){
         datas = JSON.parse(data);
         window.location.href = 'https://flora.tierragro.com/invoices';
      }
  });*/   

}


function CalculoPorcentaje(id){
    var A=($("#valorFactura").val());
    var B=($("#value"+id).val()).replace(/\./g, '');
    var C=B.replace(/\,/g, '.');


    var Porcentaje=((100*(Math.trunc(C)))/(Math.trunc(A))).toFixed();
    $("#percent"+id).val(Porcentaje);
    $("#percenta"+id).val(Porcentaje);

}



function SeleccionAprobador(){
  var token=$("input[name=_token]").val();
  var flow_id= $("#flow_id").val();
  $("#approver_id").empty();
    $.ajax({
      data:{ token, flow_id },
      url: 'https://flora.tierragro.com/api/flowapprovers',
      type:'POST',
      dataType :'JSON',
      success:function(data){
        var cantidad= data.length;
        for (let i = 0; i < cantidad; i++) {
            $("#approver_id").append('<option value='+data[i].code+'>'+data[i].name+'</option>');
            
        }
      }
  });
  
 /* if (flow_id == '55') {
    $(".coces").hide('slow');
  }else{
    $(".coces").show('slow');
  }*/
}


function ValidacionProceso(){
    var token=$("input[name=_token]").val();
    var usuarioSeleccionado= $("#approver_id").val();

    if (usuarioSeleccionado == '2101') {
        $("#BotonCadenaValidar").show('slow');
        $("#BotonCadenaAprobar").show('slow');
        $("#BotonValidar").hide('slow');
        $("#BotonAprobar").hide('slow');
        
    }else{
        $("#BotonCadenaValidar").hide('slow');
        $("#BotonCadenaAprobar").hide('slow');
        $("#BotonValidar").show('slow');
        $("#BotonAprobar").show('slow');

    }
}


function Validacion(estado){
    swal({
            icon: 'warning',
            title: "¿Esta seguro/a de aprobar la factura?",
            text: 'Recuerde que esta acción actualiza la información en la DIAN',
            buttons: ["Aceptar", "Cancelar"],
            dangerMode: true,
        })
            .then(isClose => {
                if (isClose) {
                    return false;
                } else {

                    $('.close').trigger('click');
                    $("#loader").show('slow');
                    var token=$("input[name=_token]").val();
                   // var motivo_rechazo = $("#motivo_rechazo_id").val();
                    var id_factura = $("#invoice_id").val();
                    var id_rol = $("#rol_id").val();
                    var id_user = $("#id_user").val();
                    var approber_id = $("#approver_id").val();
                    var note= $("#concept").val();
                    if (approber_id == '') {
                        swal("Error!", "Debe seleccionar un aprobador", "error");
                        $("#loader").hide('slow');
                        return false;
                    }else{
                        $.ajax({
                        data:{ token, approber_id, id_factura, id_user, estado, note},
                        url: 'https://flora.tierragro.com/api/envioEstadoCadenaCompras',
                        type:'POST',
                        dataType :'JSON',
                        success:function(data){
                            datas = JSON.parse(data);
                            if ((datas.statusCode == 200)) {
                                swal("¡Bien!", "La factura fue aprobada de forma exitosa", "success");
                                setTimeout(window.location.href = 'https://flora.tierragro.com/invoice/faccosto', 10000);
                               // window.location.href = 'https://flora.tierragro.com/invoice/faccosto';
                            }else{
                                $("#loader").hide('slow');
                                swal("La factura fue aprobada en flora. Debes validar su estado en cadena");
                                setTimeout(window.location.href = 'https://flora.tierragro.com/invoice/faccosto', 10000);
                            }

                        }
                    });
                    //swal("¡Bien!", "La factura fue aprobada de forma exitosa"+estado, "success");

                    }


            }
        });
}

function ValidacionRechazo(){
    var motivoRechazo=$("#motivo_rechazo_id").val();
    console.log("Ingreso a esta funcion:"+motivoRechazo);
}
</script>
@endsection
