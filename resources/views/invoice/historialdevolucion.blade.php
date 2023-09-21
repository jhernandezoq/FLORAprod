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
    <div class="modal fade" role="dialog" id="ediciondatos" style="margin-left:-10%;">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content" style="width:150%;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Edición de datos</h4>
        </div>
        <div class="modal-body">

        <div class="card-body">
                    <form action="{{url('devoluciones/update')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                      <div class="form-row">
                        <input id="id_devolucion" name="id_devolucion" type="text" style="display:none;">
                        <input id="id_last_person" name="id_last_person" type="text" style="display:none;">
                        <div class="form-group col-sm-4">
                                <label for="date_data">Nombre del cliente:</label>
                                <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" placeholder="Nombre..." required>
                        </div>
                        <div class="form-group col-sm-4">
                                <label for="date_data">Identificación del cliente:</label>
                                <input type="text" class="form-control" id="id_cliente" name="id_cliente" placeholder="Identificación..." required>
                        </div>
                        <div class="form-group col-sm-4">
                                <label for="date_data">Fecha soporte transacción:</label>
                                <input type="text" class="form-control" id="fecha_pago" name="fecha_pago" placeholder="yyyy-mm-dd" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
                        </div>
                        <div class="form-group col-sm-4">
                                <label for="supplier_name">Cuenta bancaria:</label>
                                    <select class="form-control" id="forma_pago" name="forma_pago" required>
                                      <option>Banco de Bogotá-Corriente</option>
                                      <option>Banco de Bogotá/PAYU-Corriente</option>
                                      <option>Bancolombia-Ahorros</option>
                                      <option>Bancolombia-Corriente</option>
                                      <option>Bancolombia-Ahorros QR</option>
                                      <option>Banco de Occidente-Corriente</option>
                                      <option>Banco Davivienda-Ahorros</option>
                                      <option>Banco Agrario-Corriente</option>
                                      <option>BBVA-Corriente</option>
                                      <option>Banco Popular-Corriente</option>
                                      <option>ITAU-Corriente</option>
                                    </select>
                        </div>
                        <div class="form-group col-sm-4">
                                <label for="date_data">Valor de devolución:</label>
                                <input type="text" class="form-control format-number" id="valor" name="valor" placeholder="Valor..." required>
                        </div>
                        <div class="form-group col-sm-4">
                                <label for="date_data">Motivo devolución:</label>
                                <input type="text" class="form-control format-number" id="motivo_rechazo" name="motivo_rechazo" placeholder="Valor..." required>
                        </div>
                        <div>
                         <div class='coces' id='campoadjunto1'><div class='form-group col-sm-6'><label for='file' id='divadjunto1'>Documento adicional:</label><input type='file' class='form-control' id='file' name='file' placeholder='' style='width: 350px; margin-left: :1%;' required="required"></div><div class='form-group col-sm-4'></div></div>
                        </div>
                        <button type="submit" class="btn btn-success" style="margin-left:45%;margin-top:2%;">Actualizar</button>
                  </form>

          </div>
        </div>



       
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
          <form action="{{url('anticipos/')}}/rechazarlegalizacion" method="POST" enctype="multipart/form-data">
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
          <h4 class="modal-title">Flujo de devoluciones</h4>
        </div>
        <div class="modal-body">
          <table class="table-responsive-md table-bordered table-striped table-sm" 
            id="tabla-flujo-anticipo">
            <thead>
              <tr>
                <th>Usuario </th>
                <th>Usuario Siguiente</th>
                <th>Estado</th>
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
                <div class="card-header">Historial de devoluciones. </div>
                <div class="card-body">
                    <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                              <th># devolución</th>
                              <th>Cliente</th>
                              <th>Id_cliente</th>
                              <th>Fecha pago</th>
                              <th>Area</th>
                              <th>Cuenta</th>
                              <th>Valor</th>
                              <th>Motivo devolución</th>
                              <th>Estado</th>
                              <th>Flujo aprobación</th>
                              <th>Documentación</th>
                              <th>Editar</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($datos as $dato)
                              <td>{{$dato->id}}</td>
                              <td>{{$dato->cliente}}</td>
                              <td>{{$dato->id_cliente}}</td>
                              <td>{{$dato->fecha_pago}}</td>
                              <td>{{$dato->area}}</td>
                              <td>{{$dato->forma_pago}}</td>
                              <td>{{$dato->valor}}</td>
                              <td>{{$dato->motivo}}</td>
                              <td>{{$dato->estado}}</td>
                              <td>
                                <input type='button' class='btn btn-info' 
                                style='color:white;'  value='Ver' onclick='CargarFlujo("{{$dato->id}}");'>
                              </td>
                              <td>
                                <input type='button' class='btn btn-info' value='Ver' 
                                  style='color:white;' onclick='CargarModalFiles("{{$dato->id}}");'>
                              </td>
                              @if ($dato->estado == 'Rechazada')
                              <td>
                                <input type='button' class='btn btn-success' value='Ver' 
                                  style='color:white;' onclick='CargarEdicion("{{$dato->id}}");'>
                              </td>
                              @else
                              <td>
                                <input type='button' class='btn btn-info' value='Ver' 
                                  style='color:white;' onclick='CargarEdicion("{{$dato->id}}");' disabled>
                              </td>
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
@endsection

@section('scripts')
<script type="text/javascript">

let urlBase_ = "{{ config('app.getUrlBase') }}"

function CargarModalFiles(id){
  let token = $("input[name=_token]").val();
  $('#archivosadjuntos').modal('show');
  
  $.ajax({
      data:{ token, id },
      //url:'https://flora.tierragro.com/api/adjuntosfilesanticipos',
      //url:'http://localhost/flora/public/api/adjuntosfilesanticipos',
      url: 'https://flora.tierragro.com/api/adjuntosfilesdevoluciones',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#adjuntosfiles tbody tr').remove();
          let Cantidad_Elementos = data.length;
          let i
          for( i = 0; i < Cantidad_Elementos; i++ ){
            $("#adjuntosfiles > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<th>"+data[i].date+"</th>" +
                "<th><a href='"+urlBase_+"/facturas/"+data[i].file+"'>"+data[i].file+"</a></th>"+
              "</tr>");
          }
      }
  });
  
}


function CargarEdicion(id){
  $('#ediciondatos').modal('show');
  let token = $("input[name=_token]").val();

  
  $.ajax({
      data:{ token, id },
      //url:'https://flora.tierragro.com/api/adjuntosfilesanticipos',
      //url:'http://localhost/flora/public/api/adjuntosfilesanticipos',
      url: 'https://flora.tierragro.com/api/datosdevoluciones',
      type:'POST',
      dataType :'JSON',
      success:function(data){
        $('#nombre_cliente').val(data[0].cliente);
        $('#id_cliente').val(data[0].id_cliente);
        $('#fecha_pago').val(data[0].fecha_pago);
        $("#forma_pago").prepend("<option value="+data[0].forma_pago+" selected='selected'>"+data[0].forma_pago+"</option>");
        $('#valor').val(data[0].valor);
        $('#id_devolucion').val(data[0].id);
        $('#id_last_person').val(data[0].last_user_id);
        $('#motivo_rechazo').val(data[0].motivo);
      }
  });
  
}

function CargarModalDistribucion(id){
  var token=$("input[name=_token]").val();
  $('#distribucion').modal('show');
  $.ajax({
      data:{token:token,
           id:id},
      url: 'https://flora.tierragro.com/api/adjuntosdistribuciongastos',
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




function CargarFlujo( id ){

$('#modal-flujo-anticipo').modal('show');
var token=$("input[name=_token]").val();
$.ajax({
    data:{token:token,
         id:id},
    url:'https://flora.tierragro.com/api/devoluciones-log',
    type:'POST',
    dataType :'JSON',
    success:function(data){
        $('#tabla-flujo-anticipo tbody tr').remove();
        var Cantidad_Elementos=data.length;
        for (var i = 0; i < Cantidad_Elementos; i++) {
          $("#tabla-flujo-anticipo > tbody").append(
           "<tr class=\"even gradeC\">" +
              "<th>"+data[i].init_user+"</th>" +
              "<th>"+data[i].next_user+"</th>" +
              "<th>"+data[i].estado+"</th>" +
              "<th>"+data[i].date+"</th>" +
            "</tr>");
        }
  }
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