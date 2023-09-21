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
    <div class="modal fade" role="dialog" id="distribucion">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Distribución centros de costos y cuentas</h4>
        </div>
        <div class="modal-body">
            <table class="table-responsive-md table-bordered table-striped table-sm" id="adjuntosdistribucion">
                <thead>
                    <tr>
                      <th>Centro de costo</th>
                      <th>Cuenta</th>
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
  <div class="modal fade" role="dialog" id="motivorechazo">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Motivo del rechazo</h4>
        </div>
        <div class="modal-body">
          <form action="{{url('anticipos/rechazarlegalizaciongastos')}}" method="POST" enctype="multipart/form-data">
                        @csrf
               <input type="text" name="invoice_id" id="invoice_id" style="display: none;">
            <label for="exampleFormControlTextarea1">Por favor ingresa el motivo del rechazo de la legalización:</label>
            <textarea class="form-control" id="motivo_rechazo" name="motivo_rechazo" rows="3" required="required"></textarea><br>
            <button type="submit" class="btn btn-success">Guardar</button>
          </form>     
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>


  <!-- Modal Flujo de Legalizaciones -->
  <div class="modal fade" role="dialog" id="modal-flujo-anticipo">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Flujo de legalizaciones</h4>
        </div>
        <div class="modal-body">
          <table class="table-responsive-md table-bordered table-striped table-sm" 
            id="tabla-flujo-anticipo">
            <thead>
              <tr>
                <th>Usuario </th>
                <th>Usuario Siguiente</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
      
    </div>
  </div>



<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Informe de legalización de gastos. </div><br>
                <img src="../img/excel.png" alt="Informe_excel" width="80" height="80" style="margin-left:90%;" onclick="GenerarExcel();">
                <div class="card-body">
                    <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                              <th># gasto</th>
                              <th>Empresa</th>
                              <th>Solicitante</th>
                              <th>Fecha pago</th>
                              <th>Valor reintegro</th>
                              <th>Concepto gasto</th>
                              <th>Motivo_rechazo</th>
                              <th>Usuario rechazo</th>
                              <th>Flujo</th>
                              <th>Adjunto</th>
                              <th>Distribución</th>
                              <th>Estado</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($datos as $dato)
                              <td>{{$dato->id}}</td>
                              <td>{{$dato->empresa}}</td>
                              <td>{{$dato->usuario}}</td>
                              <td>{{$dato->fecha_pago}}</td>
                              <td>{{$dato->valor}}</td>
                              <td>{{$dato->concepto}}</td>
                              <td>{{$dato->motivo_rechazo}}</td>
                              <td>{{$dato->usuario_rechazo}}</td>
                              <td>
                                <input type='button' class='btn btn-info' 
                                style='color:white;'  value='Ver' onclick='flujoGastos("{{$dato->id}}");'>
                              </td>
                              <td>
                                <input type='button' class='btn btn-info' value='Ver' 
                                  style='color:white;' onclick='CargarModalFiles("{{$dato->id}}");'>
                              </td>
                              <td>
                                <input type='button' class='btn btn-info' value='Ver' 
                                  style='color:white;' onclick='CargarModalDistribucion("{{$dato->id}}");'>
                              </td>
                              <td>{{$dato->estado}}</td>
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

let urlBase_ = "{{ config('app.getUrlBase') }}"

function CargarModalFiles(id){
  var token=$("input[name=_token]").val();
  $('#archivosadjuntos').modal('show');
  $.ajax({
      data:{token:token,
           id:id},
      url: urlBase_ + '/api/adjuntosfilesgastos',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#adjuntosfiles tbody tr').remove();
          var Cantidad_Elementos=data.length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#adjuntosfiles > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<th>"+data[i].fecha+"</th>" +
                "<th><a href='"+urlBase_+"/facturas/"+data[i].file+"'>"+data[i].file+"</a></th>"+
              "</tr>");
          }
    }
  });
}



function CargarMotivoRechazo(id){
  var token=$("input[name=_token]").val();
  $('#motivorechazo').modal('show');
  $('#invoice_id').val(id);
  /*$.ajax({
      data:{token:token,
           id:id},
      url:'http://localhost/flora/public/api/adjuntosfilesanticipos',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#adjuntosfiles tbody tr').remove();
          var Cantidad_Elementos=data.length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#adjuntosfiles > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<th>"+data[i].date+"</th>" +
                "<th><a href='http://localhost/flora/storage/app/anticipos/"+data[i].file+"'>"+data[i].file+"</a></th>"+
              "</tr>");
          }
    }
  });*/
}


function CargarModalDistribucion(id){
  var token=$("input[name=_token]").val();
  $('#distribucion').modal('show');
  $.ajax({
      data:{token:token,
           id:id},
      url: urlBase_ + '/api/adjuntosdistribuciongastos',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#adjuntosdistribucion tbody tr').remove();
          var Cantidad_Elementos=data.length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#adjuntosdistribucion > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<th>"+data[i].ceco+"</th>" +
                "<th>"+data[i].cuenta+"</th>" +
                "<th>"+data[i].valor+"</th>" +
              "</tr>");
          }
    }
  });
}




function flujoGastos(id_documento){
  $("#modal-flujo-anticipo").modal('show');
   var token=$("input[name=_token]").val();
  $.ajax({
      data:{token:token,
           id:id_documento},
      url:'http://localhost/flora/public/api/flujogastos',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#tabla-flujo-anticipo tbody tr').remove();
          var Cantidad_Elementos=data.length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#tabla-flujo-anticipo > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<th>"+data[i].nombre1+"</th>" +
                "<th>"+data[i].nombre2+"</th>" +
                "<th>"+data[i].fecha+"</th>" +
              "</tr>");
          }
    }
  });

}


function GenerarExcel(){
$(document).ready(function () {
    $("#Informe_flujo").table2excel({
        filename: "Informe_flujo.xls"
    });

    $("#Informe_cecos").table2excel({
        filename: "Informe_cecos.xls"
    });
});

}




const load = () => {
  const btnsFlujos = document.querySelectorAll('.btns-flujo')
  for( let btn of btnsFlujos ){
    btn.addEventListener('click', function() {
      let id = this.getAttribute('data')
      cargarFlujo( id )
    })
  }
}

document.addEventListener("DOMContentLoaded", load, false)

</script>
@endsection