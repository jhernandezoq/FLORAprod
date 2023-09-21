@extends('layouts.app')

@section('content')



  <!-- Modal -->
  <div class="modal fade" role="dialog" id="CentrosCosto">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Centros de costo asociados</h4>
        </div>
        <div class="modal-body">
                    <table class="table-responsive-md table-bordered table-striped table-sm" id="centrosid">
                        <thead>
                            <tr>
                              <th>Codigo</th>
                              <th>Centro costo</th>
                              <th>Porcentaje</th>
                              <th>Valor</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        </tbody>
                    </table>          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>






  <!-- Modal -->
  <div class="modal fade" role="dialog" id="archivosadjuntos">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Documentos asociados</h4>
        </div>
        <div class="modal-body">
                    <table class="table-responsive-md table-bordered table-striped table-sm" id="adjuntosfiles">
                        <thead>
                            <tr>
                              <th>Fecha</th>
                              <th>Usuario</th>
                              <th>Archivo</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        </tbody>
                    </table>          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>




<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Informes gestión facturas</div>
            <form action="{{url('/reports/invoicesfinder')}}" method="POST">
              @csrf
              <div class="form-row">
                <div class="form-group col-sm-3"><br>
                   <label for="user">Proveedor:</label>
                   <select class="form-control" id="supplier" name="supplier">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($suppliers as $supplier)
                      <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                    @endforeach
                   </select>
                </div>
                <div class="form-group col-sm-3"><br>
                   <label for="profile">Factura:</label>
                   <select class="form-control" id="invoice" name="invoice">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($invoices as $invoice)
                      <option value="{{$invoice->id}}">{{$invoice->number}}</option>
                    @endforeach
                   </select>
                </div>
               <div class="form-group col-sm-3"><br>
                   <label for="user">Nit:</label>
                   <select class="form-control" id="supplier_nit" name="supplier_nit">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($suppliers as $supplier)
                      <option value="{{$supplier->nit}}">{{$supplier->nit}} - {{$supplier->name}}</option>
                    @endforeach
                   </select>
                </div>
               <div class="form-group col-sm-3"><br>
                   <label for="user">Numero egreso:</label>
                   <select class="form-control" id="egress" name="egress">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($egresos as $egreso)
                      <option value="{{$egreso->egreso}}">{{$egreso->egreso}}</option>
                    @endforeach
                   </select>
                </div>
                <div class="form-row col-sm-6" style="border-color: gray;border-width: 1px;border-style: dotted; margin-bottom: 2%;">
                  <h4>Busqueda por rango de fechas</h4>
                   <div class="form-group col-sm-6">
                       <label for="fecha_inicial">Fecha inicial:</label>
                       <input type="date" name="fecha_inicial" id="fecha_inicial" placeholder="Fecha inicial" style="width: 100%;">
                    </div>
                   <div class="form-group col-sm-6">
                       <label for="fecha_final">Fecha final:</label>
                       <input type="date" name="fecha_final" id="fecha_final" placeholder="Fecha final" style="width: 100%;">
                    </div>
                </div>
               <div class="form-row col-sm-6" style="margin-left: 1%;">
                   <label for="user">Usuario:</label>
                   <select class="form-control" id="user" name="user">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($users as $user)
                      <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                   </select>
                </div>
              </div>
              <button type="submit" class="btn btn-info" style="float: left; margin-right: : 1%;margin-bottom: 1%;">Buscar</button><br>
            </form><br>
            <img src="../img/excel.png" style="width: 7%; height: 7%; margin-left: 1%;" onclick="GenerarExcel();">

                <div class="card-body">
                    <table class="table-responsive-md table-bordered table-striped table-sm" id="facturas">
                        <thead>
                            <tr>
                              <th>Numero</th>
                              <th>Fecha radicación</th>
                              <th>Proveedor</th>
                              <th>Centros costo</th>
                              <th>Archivos adjuntos</th>
                              <th>Nit</th>
                              <th>Total</th>
                              <th>Numero egreso</th>
                              <th>Estado</th>
                              <th>Moneda</th>
                              <th>Concepto</th>
                              <th>Usuario gestiona</th>
                              <th>Compañia</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($information_data as $data)
                              <td><a href="https://flora.tierragro.com/facturas/{{$data->file}}">{{$data->number}}</a></td>
                              <td>{{$data->created_at}}</td>
                              <td>{{$data->supplier}}</td>
                              <td>
                                <form>
                                  @csrf
                                  <input type="button" class="btn btn-info" value="Ver" style="color:white;" onclick="CargarModal('{{$data->id}}');">
                                </form>
                              </td>
                              <td>
                                <form>
                                  @csrf
                                  <input type="button" class="btn btn-info" value="Ver" style="color:white;" onclick="CargarModalFiles('{{$data->id}}');">
                                </form>
                              </td>
                              <td>{{$data->supplier_nit}}</td>
                              <td>{{$data->total}}</td>
                              <td>{{$data->egress}}</td>
                              <td>{{$data->state}}</td>
                              <td>{{$data->currency}}</td>
                              <td>{{$data->concept}}</td>
                              <td>{{$data->name}}</td>
                              <td>{{$data->company}}</td>
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
   $('#invoice').select2();
   $('#supplier_nit').select2();
   $('#egress').select2();
   $('#radication_time').select2();
   $('#user').select2();
});

function GenerarExcel(){
$(document).ready(function () {
    $("#facturas").table2excel({
        filename: "facturas.xls"
    });
});

}


function CargarModal(id){
  var token=$("input[name=_token]").val();
  $('#CentrosCosto').modal('show');
  $.ajax({
      data:{token:token,
           id:id},
      url:'https://flora.tierragro.com/api/costcenter',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#centrosid tbody tr').remove();
          var Cantidad_Elementos=data.length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#centrosid > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<th>"+data[i].code+"</th>" +
                "<th>"+data[i].name+"</th>"+
                "<th>"+data[i].percentage+"</th>"+
                "<th>"+(parseFloat(data[i].value)).toLocaleString()+"</th>"+
              "</tr>");
          }
    }
  });
}


function CargarModalFiles(id){
  var token=$("input[name=_token]").val();
  $('#archivosadjuntos').modal('show');
  $.ajax({
      data:{token:token,
           id:id},
      url:'https://flora.tierragro.com/api/adjuntosfiles',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#adjuntosfiles tbody tr').remove();
          var Cantidad_Elementos=data.length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#adjuntosfiles > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<th>"+data[i].date+"</th>" +
                "<th>"+data[i].name+"</th>"+
                "<th><a href='https://flora.tierragro.com/facturas/"+data[i].file+"'>"+data[i].file+"</a></th>"+
              "</tr>");
          }
    }
  });
}

</script>
@endsection