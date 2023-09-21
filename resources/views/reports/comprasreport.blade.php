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
                <div class="form-group col-sm-6"><br>
                   <label for="user">Proveedor:</label>
                   <select class="form-control" id="supplier" name="supplier">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($suppliers as $supplier)
                      <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                    @endforeach
                   </select>
                </div>
                <div class="form-group col-sm-6"><br>
                   <label for="profile">Factura:</label>
                   <select class="form-control" id="invoice" name="invoice">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($invoices as $invoice)
                      <option value="{{$invoice->id}}">{{$invoice->number}}</option>
                    @endforeach
                   </select>
                </div>
                <div class="form-row col-sm-12" style="border-color: gray;border-width: 1px;border-style: dotted; margin-bottom: 2%;">
                  <h4 class="form-group col-sm-12">Busqueda por rango de fechas</h4><br>
                   <div class="form-group col-sm-6">
                       <label for="fecha_inicial">Fecha inicial:</label>
                       <input type="date" name="fecha_inicial" id="fecha_inicial" placeholder="Fecha inicial" style="width: 100%;">
                    </div>
                   <div class="form-group col-sm-6">
                       <label for="fecha_final">Fecha final:</label>
                       <input type="date" name="fecha_final" id="fecha_final" placeholder="Fecha final" style="width: 100%;">
                    </div>
                </div>
              </div>
              <button type="submit" class="btn btn-info" style="float: left; margin-right: : 1%;margin-bottom: 1%;">Buscar</button><br>
            </form><br>
           <div class="contenedor-imagenes" style="display: flex;">
            <img src="../img/excel.png" style="width: 7%; height: 7%; margin-left: 1%;" onclick="GenerarExcelCompleto();" placeholder="Informe completo">
           </div>
            </div>

                    <table id="facturascompletas" class="table-responsive-md table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                              <th>#Factura</th>
                              <th>Orden compra</th>
                              <th>Proveedor</th>
                              <th>Fecha creación</th>
                              <th>Tipo novedad</th>
                              <th>Responsable</th>
                              <th>Observacion</th>
                              <th>Fecha gestión</th>
                              <th>Validador</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach($datos AS $dato)
                          <tr>
                           <td><a href="https://flora.tierragro.com/facturas/{{$dato->file}}" target="_blank">{{$dato->factura}}</a></td>
                           <td>{{$dato->orden_compra}}</td>
                           <td>{{$dato->proveedor}}</td>
                           <td>{{$dato->fecha_creacion}}</td>
                           <td>{{$dato->tipo_novedad}}</td>
                           <td>{{$dato->responsable}}</td>
                           <td>{{$dato->description}}</td>
                           <td>{{$dato->fecha_gestion}}</td>
                           <td>{{$dato->usuario}}</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
            </div>
        </div>
        
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

  $(document).ready(function () {
  // load_data_invoices(1);
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


function GenerarExcelCompleto(){
$(document).ready(function () {
    $("#facturascompletas").table2excel({
        filename: "facturas.xls"
    });
    $("#facturascompletasflujo").table2excel({
        filename: "facturasflujo.xls"
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

function load_data_invoices(paginate){
  var token=$("input[name=_token]").val();
  var page=paginate;
  $('#pagination span').remove();
  $.ajax({
      data:{token:token,
            page:page},
      url:'https://flora.tierragro.com/api/load_data_invoices',
      type:'POST',
      dataType :'JSON',
      success:function(data){
        var Previus=(data.page)-1;
        var Next =(data.page)+1;
        var Anterior='Anterior';
        var Siguiente='Siguiente';
        $('#pagination').append("<span class=\'btn btn-success\' style=\'cursor:pointer;padding:6px;border:1px solid #ccc;\' id=\'"+Previus+"\' onclick=\'load_data_invoices("+Previus+");\'>"+Anterior+"</span>");
         for (var i = 1; i <= data.total_pages; i++) {
            $('#pagination').append("<span class=\'btn btn-success\' style=\'cursor:pointer;padding:6px;border:1px solid #ccc;\' id=\'"+i+"\' onclick=\'load_data_invoices("+i+");\'>"+i+"</span>");
         }
         $('#pagination').append("<span class=\'btn btn-success\' style=\'cursor:pointer;padding:6px;border:1px solid #ccc;\' id=\'"+Next+"\' onclick=\'load_data_invoices("+Next+");\'>"+Siguiente+"</span>");
          $('#facturas tbody tr').remove();
          var Cantidad_Elementos=(data.data_information).length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#facturas > tbody").append(
             "<tr class=\"even gradeC\">" +
             "<td><a href=\"https://flora.tierragro.com/facturas/"+data.data_information[i]['file']+"\">"+data.data_information[i]['number']+"</a></td>"+
              "<td>"+data.data_information[i]['created_at']+"</td>"+
              "<td>"+data.data_information[i]['supplier']+"</td>"+

              "<td><input type=\"button\" class=\"btn btn-info\" value=\"Ver\" style=\"color:white;\" onclick=\"CargarModal("+data.data_information[i]['id']+");\"></td>"+
              "<td><input type=\"button\" class=\"btn btn-info\" value=\"Ver\" style=\"color:white;\" onclick=\"CargarModalFiles("+data.data_information[i]['id']+");\"></td>"+
              "<td>"+data.data_information[i]['supplier_nit']+"</td>"+
              "<td>"+data.data_information[i]['total']+"</td>"+
              "<td>"+data.data_information[i]['egress']+"</td>"+
              "<td>"+data.data_information[i]['state']+"</td>"+
              "<td>"+data.data_information[i]['currency']+"</td>"+
              "<td>"+data.data_information[i]['concept']+"</td>"+
              "<td>"+data.data_information[i]['name']+"</td>"+
              "<td>"+data.data_information[i]['company']+"</td>"+
              "</tr>");
          }
    }
  });
}
</script>
@endsection