@extends('layouts.app')

@section('content')

  <!-- Modal -->
  <div class="modal fade" role="dialog" id="archivosadjuntos">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Documentos adjuntos</h4>
        </div>
        <div class="modal-body">
                    <table class="table-responsive-md table-bordered table-striped table-sm" id="adjuntosfiles">
                        <thead>
                            <tr>
                              <th>Fecha</th>
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
                              <th>Centro de costo</th>
                              <th>Valor</th>
                              <th>Cuenta asociada</th>
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
  <div class="modal fade" role="dialog" id="flujo_aprobaciones">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Flujo aprobaciones</h4>
        </div>
        <div class="modal-body">
                    <table class="table-responsive-md table-bordered table-striped table-sm" id="datos_flujo">
                        <thead>
                            <tr>
                              <th>Fecha</th>
                              <th>Usuario inicial</th>
                              <th>Usuario siguiente</th>
                              <th>Estado</th>
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
                <div class="card-header">Anticipos generados</div><br>

            <form action="{{url('/reports/anticiposfinder')}}" method="POST">
              @csrf
              <div class="form-row">
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



                  @if($countDocuments>0)
                    <table class="table-responsive-md table-bordered table-sm" cellspacing="0" width="100%" id="anticipos">
                        <thead>
                            <tr>
                              <th># de anticipo</th>
                              <th>Fecha de pago</th>
                              <th>Valor anticipo</th>
                              <th>Empresa</th>
                              <th>Forma de pago</th>
                              <th>Concepto</th>
                              <th>Proveedor</th>
                              <th>Solicitante</th>
                              <th>Responsable</th>
                              <th>Estado</th>
                              <th>Adjunto anticipo</th>
                              <th>Adjunto legalización</th>
                              <th>Centros costo asociados</th>
                              <th>Flujo aprobaciones</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($datos as $dato)
                              <td>{{$dato->id}}</td>
                              <td>{{$dato->fecha_pago}}</td>
                              <td>{{$dato->valor_anticipo}}</td>
                              <td>{{$dato->empresa}}</td>
                              <td>{{$dato->forma_pago}}</td>
                              <td>{{$dato->concepto}}</td>
                              <td>{{$dato->proveedor}}</td>
                              <td>{{$dato->name}}</td>
                              <td>{{$dato->gestionando}}</td>
                              <td>{{$dato->estado}}</td>
                              <td><input type='button' class='btn btn-info' value='Ver' style='color:white;' onclick='CargarModalFilesAnticipos("{{$dato->id}}");'></td>
                              <td><input type='button' class='btn btn-info' value='Ver' style='color:white;' onclick='CargarModalFilesLegalizaciones("{{$dato->id}}");'></td>
                              <td><input type='button' class='btn btn-info' value='Ver' style='color:white;' onclick='CargarModal("{{$dato->id}}");'></td>
                              <td><input type='button' class='btn btn-info' value='Ver' style='color:white;' onclick='CargarModalFlujo("{{$dato->id}}");'></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <p>¡no tienes documentos pendientes por gestionar!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

  $(document).ready(function () {
   $('#user').select2();
   load_data_anticipos();
});

function CargarModalFilesLegalizaciones(id){
  var token=$("input[name=_token]").val();
  $('#archivosadjuntos').modal('show');
  $.ajax({
      data:{token:token,
           id:id},
      url:'https://flora.tierragro.com/api/adjuntosfileslegalizaciones',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#adjuntosfiles tbody tr').remove();
          var Cantidad_Elementos=data.length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#adjuntosfiles > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<th>"+data[i].date+"</th>" +
                "<th><a href='https://flora.tierragro.com/facturas/"+data[i].file+"'>"+data[i].file+"</a></th>"+
              "</tr>");
          }
    }
  });
}


function CargarModalFilesAnticipos(id){
var token=$("input[name=_token]").val();
  $('#archivosadjuntos').modal('show');
  $.ajax({
      data:{token:token,
           id:id},
      url:'https://flora.tierragro.com/api/adjuntosfilesanticipos',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#adjuntosfiles tbody tr').remove();
          var Cantidad_Elementos=data.length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#adjuntosfiles > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<th>"+data[i].date+"</th>" +
                "<th><a href='https://flora.tierragro.com/facturas/"+data[i].file+"'>"+data[i].file+"</a></th>"+
              "</tr>");
          }
    }
  });
}



function CargarModal(id){
  var token=$("input[name=_token]").val();
  $('#CentrosCosto').modal('show');
  $.ajax({
      data:{token:token,
           id:id},
      url:'https://flora.tierragro.com/api/costcenteranticipos',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#centrosid tbody tr').remove();
          var Cantidad_Elementos=data.length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#centrosid > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<th>"+data[i].centcost+"</th>"+
                "<th>"+data[i].monto+"</th>"+
                "<th>"+data[i].cuenta+"</th>"+
              "</tr>");
          }
    }
  });
}


function CargarModalFlujo(id){
  var token=$("input[name=_token]").val();
  $('#flujo_aprobaciones').modal('show');
  $.ajax({
      data:{token:token,
           id:id},
      url:'https://flora.tierragro.com/api/flujoanticipos',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#datos_flujo tbody tr').remove();
          var Cantidad_Elementos=data.length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#datos_flujo > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<th>"+data[i].fecha+"</th>"+
                "<th>"+data[i].nombre+"</th>"+
                "<th>"+data[i].nombres+"</th>"+
                "<th>"+data[i].tipo+"</th>"+
              "</tr>");
          }
    }
  });
}

function GenerarExcel(){
$(document).ready(function () {
    $("#anticipos").table2excel({
        filename: "anticipos.xls"
    });
});

}

function load_data_anticipos(paginate){
  var token=$("input[name=_token]").val();
  var page=paginate;
  $('#pagination span').remove();
  $.ajax({
      data:{token:token,
            page:page},
      url:'https://flora.tierragro.com/routes/api/load_data_anticipos',
            type:'POST',
      dataType :'JSON',
      success:function(data){
        var Previus=(data.page)-1;
        var Next =(data.page)+1;
        var Anterior='Anterior';
        var Siguiente='Siguiente';
        $('#pagination').append("<span class=\'btn btn-success\' style=\'cursor:pointer;padding:6px;border:1px solid #ccc;\' id=\'"+Previus+"\' onclick=\'load_data_anticipos("+Previus+");\'>"+Anterior+"</span>");
         for (var i = 1; i <= data.total_pages; i++) {
            $('#pagination').append("<span class=\'btn btn-success\' style=\'cursor:pointer;padding:6px;border:1px solid #ccc;\' id=\'"+i+"\' onclick=\'load_data_anticipos("+i+");\'>"+i+"</span>");
         }
         $('#pagination').append("<span class=\'btn btn-success\' style=\'cursor:pointer;padding:6px;border:1px solid #ccc;\' id=\'"+Next+"\' onclick=\'load_data_anticipos("+Next+");\'>"+Siguiente+"</span>");
          $('#anticipos tbody tr').remove();

          var Cantidad_Elementos=(data.data_information).length;
          for (var i = 0; i < Cantidad_Elementos; i++) {

            $("#anticipos > tbody").append(
             "<tr class=\"even gradeC\">" +
             "<td><a href=\"https://flora.tierragro.com/facturas/"+data.data_information[i]['file']+"\">"+data.data_information[i]['id']+"</a></td>"+
              "<td>"+data.data_information[i]['fecha_pago']+"</td>"+
              "<td>"+data.data_information[i]['valor_anticipo']+"</td>"+
              "<td>"+data.data_information[i]['empresa']+"</td>"+
              "<td>"+data.data_information[i]['forma_pago']+"</td>"+              
              "<td>"+data.data_information[i]['concepto']+"</td>"+
              "<td>"+data.data_information[i]['proveedor']+"</td>"+
              "<td>"+data.data_information[i]['name']+"</td>"+
              "<td>"+data.data_information[i]['gestionando']+"</td>"+
              "<td>"+data.data_information[i]['estado']+"</td>"+
              
              "<td><input type=\"button\" class=\"btn btn-info\" value=\"Ver\" style=\"color:white;\" onclick=\"CargarModalFilesAnticipos("+data.data_information[i]['id']+");\"></td>"+
              "<td><input type=\"button\" class=\"btn btn-info\" value=\"Ver\" style=\"color:white;\" onclick=\"CargarModalFilesLegalizaciones("+data.data_information[i]['id']+");\"></td>"+
              "<td><input type=\"button\" class=\"btn btn-info\" value=\"Ver\" style=\"color:white;\" onclick=\CargarModal("+data.data_information[i]['id']+");\"></td>"+        

              "</tr>");
              
          }
        }
  });
}


</script>
@endsection