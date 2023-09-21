@extends('layouts.app')

@section('content')


  <!-- Modal Archivos Adjuntos -->
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



  <!-- Modal Motivo Rechazo -->
  <div class="modal fade" role="dialog" id="motivorechazo">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Motivo del rechazo</h4>
        </div>
        <div class="modal-body">
          <form action="{{url('devoluciones/')}}/rechazar" method="POST" enctype="multipart/form-data">
                        @csrf
               <input type="text" name="invoice_id" id="invoice_id" style="display: none;">
               <input type="text" name="id_usuario" id="id_usuario" style="display: none;">
            <label for="exampleFormControlTextarea1">Por favor ingresa el motivo del rechazo de la devolución:</label>
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




    <!-- Modal Motivo Rechazo -->
    <div class="modal fade" role="dialog" id="adjuntar_documento">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Deseas adjuntar algun documento??</h4>
        </div>
        <div class="modal-body">
          <form action="{{url('devoluciones')}}/aceptar" method="POST" enctype="multipart/form-data">
                        @csrf
               <input type="text" name="invoice_id_adjuntar" id="invoice_id_adjuntar" style="display:none;">
               <input type="text" name="id_usuario" id="id_usuario" style="display: none;">
               <div class="alert alert-warning" role="warning">
                 <span>ATENCIÓN</span><br>
                 <span>Recuerda adjuntar algún documento faltante en los casos en que aplique</span>
                </div>
            <label for='file' id='divadjunto1'>Documento adjunto:</label><input type='file' class='form-control' id='file' name='file' placeholder='' style='width: 350px; margin-left: :1%;'><br>
            <button type="submit" class="btn btn-success">Guardar</button>
          </form>        
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>



  <!-- Modal Flujo de Anticipo -->
  <div class="modal fade" role="dialog" id="modal-flujo-anticipo">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Flujo de aprobaciones</h4>
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
                <div class="card-header">
                  Devoluciones por gestionar
                </div>
                <div>
                </div>
            
                <div class="card-body">
                  @if($count)
                    <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                              <th># devolución</th>
                              <th>Nombre cliente</th>
                              <th>Cedula cliente</th>
                              <th>Fecha pago</th>
                              <th>Valor</th>
                              <th>Cuenta</th>
                              <th>Area</th>
                              <th>Motivo devolución</th>
                              <th>Creador solicitud</th>
                              <th>Flujo</th>
                              <th>Adjunto</th>
                              <th>Estado</th>
                              <th>Aprobar</th>
                              <th>Rechazar</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($datos as $dato)
                              <td>{{$dato->id}}</td>
                              <td>{{$dato->cliente}}</td>
                              <td>{{$dato->id_cliente}}</td>
                              <td>{{$dato->fecha_pago}}</td>
                              <td>{{$dato->valor}}</td>
                              <td>{{$dato->forma_pago}}</td>
                              <td>{{$dato->area}}</td>
                              <td>{{$dato->motivo}}</td>
                              <td>{{$dato->usuario_generador}}</td>
                              <td>
                                <input type='button' class='btn btn-info' 
                                value='Ver' style='color:white;' 
                                onclick='CargarFlujo("{{$dato->id}}");'>
                              </td>
                              <td>
                                <input type='button' class='btn btn-info' 
                                value='Ver' style='color:white;' 
                                onclick='CargarModalFiles("{{$dato->id}}");'>
                              </td>
                              <td>{{$dato->estado}}</td>
                              <td>
                                <input type="button" class='btn btn-success' value='Aprobar' onclick='Aprobar_devolucion("{{$dato->id}}");'>
                              </td>
                              <td>
                                <input type='button' class='btn btn-danger' 
                                value='Rechazar' style='color:white;' 
                                onclick='CargarMotivoRechazo("{{$dato->id}}","{{$dato->id_generador}}");'>
                              </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <p>¡no tienes anticipos pendientes por gestionar!</p>
                    @endif
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


function Aprobar_devolucion(id){
  var token=$("input[name=_token]").val();
  $('#adjuntar_documento').modal('show');
  $('#invoice_id_adjuntar').val(id);
}




function CargarMotivoRechazo(id,id_usuario){
  var token=$("input[name=_token]").val();
  $('#motivorechazo').modal('show');
  $('#invoice_id').val(id);
  $('#id_usuario').val(id_usuario);
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
  


</script>
@endsection