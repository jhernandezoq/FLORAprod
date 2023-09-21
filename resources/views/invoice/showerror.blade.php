@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Factura {{$invoice->supplier->name}} - {{$invoice->number}}</div>
                 <ul>
                    <div class="alert alert-danger" role="alert">
                      <span>Lo sentimos:</span>
                      <li>Por favor verifica la persona que seleccionaste en el flujo, ya que según la opción de Aprobar/Rechazar que escogiste, esta persona no corresponde en el nivel seleccionado</li>
                    </div>
                </ul>

                <div class="card-body">
                    <div class="row">
                        <div class="col d-flex flex-row justify-content-center">
                            <div class="d-flex flex-column text-center">
                                <a href="{{asset('facturas')}}/{{$invoice->file}}"><svg width="100px" height="100px" viewBox="0 0 16 16" class="bi bi-file-earmark-text-fill" fill="#52b788" xmlns="http://www.w3.org/2000/svg">
                                  <path fill-rule="evenodd" d="M2 2a2 2 0 0 1 2-2h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm7 2l.5-2.5 3 3L10 5a1 1 0 0 1-1-1zM4.5 8a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zM4 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5z"/>
                                </svg></a>
                                <br>
                                <h1>{{ucfirst($invoice->concept)}}</h1>
                                <h3>$ {{number_format($invoice->total,2)}} {{$invoice->currency}}</h3>
                                <input type="text" name="valorFactura" id="valorFactura" style="display: none;" value="{{$invoice->total}}">
                                <h6>${{number_format($invoice->subtotal,2)}} {{$invoice->currency}}| ${{number_format($invoice->iva,2)}} {{$invoice->currency}}</h6>
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

                                @for($i=$cont+1;$i<=$cont+5;$i++)
                                    <div class="form-row">
                                        <div class="col">
                                            <select class="form-control" id="coce{{$i}}" name="coce{{$i}}">
                                                <option value="">Centro de costos...</option>   
                                                @foreach($costCenters as $coce)
                                                    <option value="{{$coce->id}}">{{$coce->name}} - {{$coce->code}}</option>  
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                          <input type="text" class="form-control format-number" name="percent{{$i}}" placeholder="Porcentajesss">
                                        </div>
                                        <div class="col">
                                          <input type="text" class="form-control format-number" name="value{{$i}}" placeholder="Valor">
                                        </div>
                                    </div>
                                    <br>
                                @endfor
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
                            <input type="text" class="form-control" id="egreso" name="egreso" placeholder="Numero de egreso" required>
                        </div>
                        @else
                        <input type="text" class="form-control" id="egreso" name="egreso" placeholder="Numero de egreso" value="N/A" style="display: none;">
                        @endif
                        <br>
                        <div class="row">
                            <div class="col d-flex flex-row justify-content-center">
                                 <div class="d-flex flex-column text-center">
                                    <input type="submit" class="btn btn-danger" name="action" value="Rechazar">
                                </div>

                                <div class="d-flex flex-column text-center">
                                    @if($typeapprover[0]->typeapprover == 1)
                                       <input type="submit" class="btn btn-success" name="action" value="Validar">
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


function EliminarCampo(id){
    i=i-1;
    $("#countfields").val(i);
    $("#percenta"+id).remove();
    $("#campo"+id).hide('slow', function(){ 
        $("#campo"+id).remove(); });
}


function CalculoPorcentaje(id){
    var A=($("#valorFactura").val());
    var B=($("#value"+id).val()).replace(/\./g, '');
    var C=B.replace(/\,/g, '.');


    var Porcentaje=((100*(Math.trunc(C)))/(Math.trunc(A))).toFixed();
    $("#percent"+id).val(Porcentaje);
    $("#percenta"+id).val(Porcentaje);

}
</script>
@endsection
