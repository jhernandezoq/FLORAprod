@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Consultar/Editar</div><br>
                  <form method="POST" action="{{url('reports/lookinginvoices')}}">
                  @csrf
                    <div class="form-group">
                      <label for="reference" style="margin-right: 2%;">Referencia:</label>
                      <select name="reference" class="form-control" id="invoice">
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
                        @foreach($states AS $state)
                        <option value="{{$state->id}}">{{$state->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <button type="button" class="btn btn-success" style="margin-top: 1%;" onclick="load_data_invoices_report(1);">Buscar</button><br>
                  </form><br>
                  @if($part==0)
                  <h3 id="mensaje">Ingresa los datos para iniciar la busqueda</h3>
                  @endif

                 <div class="card-body" id="tabla" style="display: none;">
                    <table class="table-responsive-md table-bordered table-striped table-sm" id="facturas">
                        <thead>
                            <tr>
                              <th>Referencia</th>
                              <th>Proveedor</th>
                              <th>Flujo</th>
                              <th>Vencimiento</th>
                              <th>Total</th>
                              <th>Estado</th>
                              <th>Editar</th>
                              <th>Borrar</th>
                              <th></th>
                            </tr>
                        </thead>                    
                        <tbody>
                        </tbody>
                    </table><br>
                    
                   </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

  $(document).ready(function () {
  // load_data_invoices_report(1);
   $('#supplier').select2();
   $('#invoice').select2();
 //  $('#supplier_nit').select2();
 //  $('#egress').select2();
});

function GenerarExcel(){
$(document).ready(function () {
    $("#facturas").table2excel({
        filename: "facturas.xls"
    });
});

}



function load_data_invoices_report(paginate){
  var token=$("input[name=_token]").val();
  var page=paginate;
  var reference=$("#invoice").val();
  var supplier=$("#supplier").val();
  var state=$("#state").val();
  $("#tabla").show('slow');
  $("#mensaje").hide('slow');
  $('#pagination span').remove();
  $.ajax({
      data:{token:token,
            page:page,
             reference:reference,
             supplier:supplier,
             state:state},
      url:'https://flora.tierragro.com/api/load_data_invoices_report',
      type:'POST',
      dataType :'JSON',
      success:function(data){
        var Previus=(data.page)-1;
        var Next =(data.page)+1;
        var Anterior='Anterior';
        var Siguiente='Siguiente';
        $('#pagination').append("<span class=\'btn btn-success\' style=\'cursor:pointer;padding:6px;border:1px solid #ccc;\' id=\'"+Previus+"\' onclick=\'load_data_invoices_report("+Previus+");\'>"+Anterior+"</span>");
         for (var i = 1; i <= data.total_pages; i++) {
            $('#pagination').append("<span class=\'btn btn-success\' style=\'cursor:pointer;padding:6px;border:1px solid #ccc;\' id=\'"+i+"\' onclick=\'load_data_invoices_report("+i+");\'>"+i+"</span>");
         }
         $('#pagination').append("<span class=\'btn btn-success\' style=\'cursor:pointer;padding:6px;border:1px solid #ccc;\' id=\'"+Next+"\' onclick=\'load_data_invoices_report("+Next+");\'>"+Siguiente+"</span>");
          $('#facturas tbody tr').remove();
          var Cantidad_Elementos=(data.data_information).length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#facturas > tbody").append(
             "<tr>"+
                "<td>"+data.data_information[i]['number']+"</td>"+
                "<td>"+data.data_information[i]['supplier']+"</td>"+
                "<td>"+data.data_information[i]['flow']+"</td>"+
                "<td>"+data.data_information[i]['due']+"</td>"+
                "<td>"+data.data_information[i]['total']+"</td>"+
                "<td>"+data.data_information[i]['state']+"</td>"+
                "<td><a href=\"../reports/editinvoices/"+data.data_information[i]['id']+"\"><button type=\"submit\" class=\"btn btn-success\">Editar</button></a></td>"+
                "<td><a href=\"../reports/deleteinvoices/"+data.data_information[i]['id']+"\"><button type=\"submit\" class=\"btn btn-success\">Borrar</button></a></td>"+
              "</tr>");
          }
    }
  });
}

</script>
@endsection