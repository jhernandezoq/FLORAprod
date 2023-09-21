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
            <label for="exampleFormControlTextarea1">Por favor ingresa el motivo por el cual rechazas la legalización:</label>
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



<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Legalizaciones por cerrar</div>
                <div class="card-body">
                  @if($count)
                    <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                              <th># anticipo</th>
                              <th>Solicitante</th>
                              <th>Fecha pago</th>
                              <th>Valor anticipo</th>
                              <th>Forma de pago</th>
                              <th>Concepto anticipo</th>
                              <th>Concepto legalización</th>
                              <th>Adjunto</th>
                              <th>Estado</th>
                              <th>Aprobar</th>
                              <th>Rechazar</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($anticipos as $anticipo)
                              <td>{{$anticipo->id}}</td>
                              <td>{{$anticipo->name}}</td>
                              <td>{{$anticipo->fecha_pago}}</td>
                              <td>{{$anticipo->valor_anticipo}}</td>
                              <td>{{$anticipo->forma_pago}}</td>
                              <td>{{$anticipo->concepto}}</td>
                              <td>{{$anticipo->conceptolegalizacion}}</td>
                              <td><input type='button' class='btn btn-info' value='Ver' style='color:white;' onclick='CargarModalFiles("{{$anticipo->id}}");'></td>
                              <td>{{$anticipo->estado}}</td>
                              <td><a href="{{url('anticipos/')}}/{{$anticipo->id}}/{{$anticipo->id_user}}/cerrarlegalizacion" class="btn btn-success">Cerrar</a>
                              </td>
                              <td><input type='button' class='btn btn-danger' value='Rechazar' style='color:white;' onclick='CargarMotivoRechazo("{{$anticipo->id}}");'></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <p>¡no tienes legalizaciones pendientes por cerrar!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

function CargarModalFiles(id){
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


</script>
@endsection