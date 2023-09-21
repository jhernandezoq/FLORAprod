@extends('layouts.app')

@section('content')


  <!-- Modal -->
  <div class="modal fade" role="dialog" id="modal_rechazo">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Motivos para el rechazo</h4>
        </div>
            <div style="margin-left:2%; margin-right:2%;">
                <input type="hidden"  id="invoice_id" name="invoice_id" value="{{$invoice->id}}">
                <input type="hidden" id="role_id" name="role_id" value="{{$approver->role_id}}">
                <input type="hidden" id="id_user" name="id_user" value="{{$user}}">
                <label for="motivo_rechazo_id">Seleccione el motivo del rechazo:</label>
                <select class="form-control" id="motivo_rechazo_id" name="motivo_rechazo_id" style="margin-bottom=2%;">
                    <option value="1">Error al radicar la factura</option>
                    <option value="2">Selección errada de centros de costo</option>
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
                                <h3>TOTAL ${{number_format($invoice->total,2)}} {{$invoice->currency}}</h3>
                                <input type="text" name="valorFactura" id="valorFactura" style="display: none;" value="{{$invoice->total}}">
                                <h6>SUBTOTAL ${{number_format($invoice->subtotal,2)}} {{$invoice->currency}}</h6>
                            </div>
                        </div>
                    </div>
                    <br>
                    <form action="{{url('log')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                        <input type="hidden" name="role_id" value="{{$approver->role_id}}">

                        @if($approver->role_id == 1)
                            @if($invoice->distribution->count()>0)
                            <div class="coces">
                                <h5 class="text-center"><strong>Centros de Costos</strong></h5>
                              @foreach($invoice->distribution as $coce)
                              
                                <div class="form-row">
                                        <div class="col">
                                            <select class="form-control" id="coce{{$loop->iteration}}" name="coce{{$loop->iteration}}" required>
                                                <option value="">Centro de costos...</option>   
                                                @foreach($costCenters as $cocex)
                                                    @if($cocex->id == $coce->id)
                                                        <option value="{{$cocex->id}}" selected>{{$cocex->name}} - {{$cocex->code}}</option>
                                                    @else
                                                        <option value="{{$cocex->id}}">{{$cocex->name}} - {{$cocex->code}}</option>
                                                    @endif  
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                          <input type="text" class="form-control" name="percent{{$loop->iteration}}" placeholder="Porcentaje" value="{{$coce->pivot->percentage}}" required>
                                        </div>
                                        <div class="col">
                                          <input type="text" class="form-control format-number" name="value{{$loop->iteration}}" placeholder="Valor" value="{{number_format($coce->pivot->value,0)}}" required>
                                        </div>
                                    </div>
                                    <br>
                                    @php
                                        $cont = $loop->iteration;
                                    @endphp
                              @endforeach

                            </div> 
                            @else
                            <div class="coces">
                                <h5 class="text-center"><strong>Centros de Costos</strong></h5>
                                    <div class="form-row" style="margin-bottom: 1%;">
                                        <div class="col">
                                            <select class="form-control" id="coce1" name="coce1" required>
                                                <option value="">Centro de costos...</option>   
                                                @foreach($costCenters as $coce)
                                                    <option value="{{$coce->id}}">{{$coce->name}} - {{$coce->code}}</option>  
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                          <input type="text" class="form-control" name="percent1" id="percent1" placeholder="Porcentaje" required disabled="disabled">
                                          <input type="text" class="form-control" name="percenta1" id="percenta1" placeholder="Porcentaje" style="display: none;">
                                        </div>
                                        <div class="col">
                                          <input type="text" class="form-control format-number" name="value1" id="value1" placeholder="Valor" required onchange="CalculoPorcentaje('1');">
                                        </div>
                                    </div>
                                <div id="NuevoCampo"></div><br>
                                <img src="../../img/agregar.png" alt="Agregar centro de costo" width="50" height="50" style="margin-left: 86%;" onclick="AgregarCampos();"
                                id="imagen_add">
                            </div><br>
                            <input type="text" name="countfields"  id="countfields" width="10px;" value="1" style="display: none;">
                            @endif



                            <div class="coces">
                                <h5 class="text-center"><strong>Centro de costo que autoriza el gasto</strong></h5>
                                <div class="form-row" style="margin-bottom: 1%;">
                                        <div class="col">
                                            <select class="form-control" id="autorization_user" name="autorization_user" required>
                                                <option value="">Centros de costo...</option>   
                                                @foreach($costCenters as $coce)
                                                    <option value="{{$coce->name}}">{{$coce->name}} - {{$coce->code}}</option>  
                                                @endforeach
                                            </select><br><br>
                                        </div>
                                </div><br>
                                <div id="NuevoCampoCC"></div><br>
                                <img src="../../img/agregar.png" alt="Agregar centro de costo" width="50" height="50" style="margin-left: 86%; display:none;" onclick="AgregarCamposCC();" id="imagen_add">
                                <input type="text" name="countfieldsCC"  id="countfieldsCC" width="10px;" value="1" style="display: none;">
                            </div> 



                        @else
                            <table class="table-responsive-md table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Centro de Costos</th>
                                        <th>Porcentaje</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                        
                                <tbody>
                                @foreach($invoice->distribution as $coce)
                                    <tr>
                                        <td>{{$coce->name}}</td>
                                        <td>{{$coce->pivot->percentage}}%</td>
                                        <td>${{number_format($coce->pivot->value,0)}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table><br><br>

                            <table class="table-responsive-md table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Centro autorizacación</th>
                                        <th>Centros destino</th>
                                    </tr>
                                </thead>
                        
                                <tbody>
                                @foreach($invoiceCCS as $invoiceCC)
                                    <tr>
                                        <td>{{$invoiceCCAutorizations[0]->name}}</td>
                                        <td>{{$invoiceCC->name}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                        <div class="form-group">
                            <label for="description">Observación:</label>
                            <textarea class="form-control" id="concept" name="description" rows="3" aria-describedby="descriptionHelp" placeholder="Factura en proceso..."></textarea>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="file">Archivo Soporte:</label>
                            <input type="file" class="form-control" id="file" name="file" placeholder="">
                        </div>
                        <div class="loader" id="loader" style='display:none;'></div>
                        @if($flow_id == 60)
                        <div class="form-group">
                            <label for="flow_id">Flujo:</label>
                            <select class="form-control" id="flow_id" name="flow_id" required onchange="SeleccionAprobador();">
                                <option value="">Seleccionar Flujo...</option>   
                                @foreach($flows as $flow)
                                    <option value="{{$flow->id}}">{{$flow->name}}</option>  
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if($typeapprover[0]->typeapprover != 3)
                        <div class="form-group">
                            <label for="approver_id">Solicitar Aprobación de:</label>
                            <select class="form-control" id="approver_id" name="approver_id" required>
                                <option value="">Seleccionar Aprobador...</option>   
                                @foreach($approvers as $ap)
                                    <option value="{{$ap->user->id}}">{{$ap->user->name}}</option> 
                                @endforeach
                            </select>
                        </div>
                        @else
                            @if($typeenter=0)
                            <input type="hidden" name="approver_id" value="{{$user->id}}">
                            @else
                            <input type="hidden" name="approver_id" value="{{$user}}">
                            @endif
                        @endif
                        @if($diference == 1)
                        <div class="form-group">
                            <label for="egreso">Numero de egreso:</label>
                            <input type="text" class="form-control" id="egreso" name="egreso" placeholder="Numero de egreso">
                        </div>
                        @else
                        <input type="text" class="form-control" id="egreso" name="egreso" placeholder="Numero de egreso" value="N/A" style="display: none;">
                        @endif
                        <br>
                        <div class="row">
                            <div class="col d-flex flex-row justify-content-center">
                                 <div class="d-flex flex-column text-center">
                                    <input type="button" class="btn btn-danger" onclick="ModalRechazo();" value="Rechazar">
                                </div>

                                <div class="d-flex text-center">
                                    @if($typeapprover[0]->typeapprover == 1)
                                       <input type="submit" class="btn btn-success" name="action" value="Validar">
                                        @if($flow_id == 60)
                                        <input type="submit" class="btn btn-warning" name="action" value="Retornar">
                                        @endif
                                    @else
                                        @if($typeapprover[0]->typeapprover == 2)
                                            <input type="submit" class="btn btn-success" name="action" value="Aprobar">
                                        @else
                                            <input type="submit" class="btn btn-success" name="action" value="Finalizar">
                                        @endif
                                    @endif
                                </div>

                                
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
    $('.close').trigger('click');
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
       /*  const { datos } = datas;
          if ((datas['statusCode'] == 200) && (datas['eventCode'] != 32)) {
            $("#loader").hide('slow');
            window.location.href = 'http://localhost/flora/public/invoices';
          }else{
            $("#loader").hide('slow');
            alert("Problemas con el envio a cadena");
            window.location.href = 'http://localhost/flora/public/invoices';
          }*/

      }
  });   

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
</script>
@endsection
