@extends('layouts.app')
@include('sweetalert::alert')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Informe tickets</div><br>
 <form method="GET" action="{{url('reports/lookingtickets')}}">
 @csrf
  <div class="form-group">
    <label for="tool" style="margin-right: 2%;">Herramienta:</label>
    <select name="tool" class="form-control">
    	<option value="0" selected="selected">Seleccione...</option>
      @foreach($tools AS $tool)
      <option value="{{$tool->id}}">{{$tool->name}}</option>
      @endforeach
    </select>
  </div>
  <div class="form-group">
    <label for="agent" style="margin-right: 2%;">Agente:</label>
    <select name="agent" class="form-control">
      <option value="0" selected="selected">Seleccione...</option>
      @foreach($agents AS $agent)
       <option value="{{$agent->id}}">{{$agent->name}}</option>
      @endforeach
    </select>
  </div>
  <div class="form-group">
    <label for="state" style="margin-right: 2%;">Estado:</label>
    <select name="state" class="form-control">
    	<option value="0" selected="selected">Seleccione...</option>
      @foreach($states AS $state)
      <option value="{{$state->id}}">{{$state->name}}</option>
      @endforeach
    </select>
  </div>
  <div>
    <button type="submit" class="btn btn-success">Buscar</button><br>
  </div>
</form><br>
<img src="../img/excel.png" style="margin-left: 90%;" onclick="GenerarExcel();">
                <div class="card-body">
                  <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%" id="incidentes_table">
                    <thead style="background-color: #90d3c1; color:white;">
                            <tr>
                              <th>Caso</th>
                              <th>Fecha</th>
                              <th>Fecha ultimo registro</th>
                              <th>Herramienta</th>
                              <th>Usuario</th>
                              <th>Detalle</th>
                              <th>Agente</th>
                              <th>Estado</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($datas as $data)
                              <td>{{$data->number}}</td>
                              <td>{{$data->date}}</td>
                              <td>{{$data->updated}}</td>
                              <td>{{$data->tool}}</td>
                              <td>{{$data->user}}</td>
                              <td>{{$data->detail}}</td>
                              <td>{{$data->agent}}</td>
                              <td>{{$data->state}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                    <ul class="pagination pagination-sm justify-content-end">
                      @if($pagination_state == 1)
                        {!! $datas->onEachSide(1)->links() !!}
                      @endif
                     </ul>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

function GenerarExcel(){
$(document).ready(function () {
    $("#incidentes_table").table2excel({
        filename: "Incidentes.xls"
    });
});

}


$('select').on('change', function() {
  alert( this.value );
});



</script>
@endsection