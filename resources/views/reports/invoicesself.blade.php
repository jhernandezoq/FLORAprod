@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Informe facturas radicadas</div><br>
 <form class="form-inline" method="POST" action="{{url('reports/lookinginvoicesself')}}">
 @csrf
  <div class="form-group">
    <label for="reference" style="margin-right: 2%;">Referencia:</label>
    <select name="reference" class="form-control" id="reference">
    	<option value="0" selected="selected">Seleccione...</option>
    	@foreach($references AS $reference)
    	   <option value="{{$reference->id}}">{{$reference->number}}</option>
    	@endforeach
    </select>
  </div>
  <div class="form-group">
    <label for="supplier" style="margin-right: 2%;">Proveedor:</label>
    <select name="supplier" class="form-control" id="supplier">
    	<option value="0" selected="selected">Seleccione...</option>
    	@foreach($suppliers AS $supplier)
    	<option value="{{$supplier->id}}">{{$supplier->name}}</option>
    	@endforeach
    </select>
  </div>
  <div class="form-group">
    <label for="state" style="margin-right: 2%;">Estado:</label>
    <select name="state" class="form-control">
    	<option value="0" selected="selected">Seleccione...</option>
      <option value="1">Radicada</option>
      <option value="3">Validada</option>
    </select>
  </div>

  <div class="form-group">
    <label for="state" style="margin-right: 2%;">Radicador:</label>
    <input type="text" name="radicador" value="{{$user->first_name}}" disabled="disabled" style="width: 80%;">
  </div>
  <button type="submit" class="btn btn-success" style="margin-top: 2%;">Buscar</button>
</form>
<img src="../img/excel.png" style="width: 7%; height: 7%; margin-left: 90%;" onclick="GenerarExcel();">
                <div class="card-body">
                    <table class="table-responsive-md table-bordered table-striped table-sm" id="incidentes_table">
                        <thead>
                            <tr>
                              <th>Referencia</th>
                              <th>Fecha radicación</th>
                              <th>Proveedor</th>
                              <th>Vencimiento</th>
                              <th>Total</th>
                              <th>Moneda</th>
                              <th>Estado</th>
                              <th id="EditarTitle">Editar</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($datas as $data)
                              <td>{{$data->number}}</td>
                              <td>{{$data->created_at}}</td>
                              <td>{{$data->supplier}}</td>
                              <td>{{$data->due}}</td>
                              <td>{{$data->total}}</td>
                              <td>{{$data->currency}}</td>
                              <td>{{$data->state}}</td>
                              <td id="EditarFuction"><form method="POST" action="{{url('reports/editioninvoicesself')}}">
                              	@csrf
                              <input type="text" name="invoice_id" value="{{$data->id}}" style="display: none;">
                              <button type="submit" class="btn btn-success">Editar</button>
                              </form></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">


  $(document).ready(function () {
   $('#supplier').select2();
   $('#reference').select2();
   });
  

function GenerarExcel(){
$(document).ready(function () {
    $("#incidentes_table").table2excel({
        filename: "facturas.xls"
    });
});

}


$('select').on('change', function() {
  alert( this.value );
});



</script>
@endsection