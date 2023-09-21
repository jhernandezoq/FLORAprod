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
  <div class="modal fade" role="dialog" id="motivorechazo">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Motivo del rechazo</h4>
        </div>
        <div class="modal-body">
          <form action="{{url('anticipos/')}}/rechazar" method="POST" enctype="multipart/form-data">
                        @csrf
               <input type="text" name="invoice_id" id="invoice_id" style="display: none;">
               <input type="text" name="id_usuario" id="id_usuario" style="display: none;">
            <label for="exampleFormControlTextarea1">Por favor ingresa el motivo por el cual rechazas el anticipo:</label>
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


  <!-- Modal Flujo de Anticipo -->
  <div class="modal fade" role="dialog" id="modal-flujo-anticipo">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Flujo de anticipos</h4>
        </div>
        <div class="modal-body">
          <div id="cargando-flujo-anticipo">Cargando...</div>
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
                <div class="card-header">Anticipos por gestionar</div>
            
                <div class="card-body">
                  @if($count)
                    <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                              <th># anticipo</th>
                              <th>Solicitante</th>
                              <th>Cédula</th>
                              <th>Último aprobador</th>
                              <th>Cargo aprobador</th>
                              <th>Empresa</th>
                              <th>Fecha pago</th>
                              <th>Valor anticipo</th>
                              <th>Forma de pago</th>
                              <th>Concepto</th>
                              <th>Flujo</th>
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
                              <td>{{$anticipo->cedula}}</td>
                              <td>{{$anticipo->ultimo_aprobador}}</td>
                              <td>{{$anticipo->cargo_aprobador}}</td>
                              <td>{{$anticipo->empresa}}</td>
                              <td>{{$anticipo->fecha_pago}}</td>
                              <td>{{$anticipo->valor_anticipo}}</td>
                              <td>{{$anticipo->forma_pago}}</td>
                              <td>{{$anticipo->concepto}}</td>
                              <td>
                                <input type='button' class='btn btn-info' 
                                value='Ver' style='color:white;' 
                                onclick='cargarFlujo("{{$anticipo->id}}");'>
                              </td>
                              <td><input type='button' class='btn btn-info' value='Ver' style='color:white;' onclick='CargarModalFiles("{{$anticipo->id}}");'></td>
                              <td>{{$anticipo->estado}}</td>
                              <td><a href="{{url('anticipos/')}}/{{$anticipo->id}}/{{$anticipo->id_user}}/{{$user}}/aceptar" class="btn btn-success">Aprobar</a>
                              </td>
                              <td><input type='button' class='btn btn-danger' value='Rechazar' style='color:white;' onclick='CargarMotivoRechazo("{{$anticipo->id}}","{{$id_usuario}}");'></td>
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




const cargarFlujo = ( id ) => {
  let type = 'Anticipos'//'Legalización anticipos'

  $('#modal-flujo-anticipo').modal('show');
  $('#cargando-flujo-anticipo').show()
  $('#tabla-flujo-anticipo').hide()

  fetch( urlBase_ + '/api/anticipos/anticipos-log', {
    method: 'POST',
    body: JSON.stringify({ id, type }),
    headers: {
      'Content-Type': 'application/json'
    }
  })
  .then(res => res.json())
  .catch(error => console.error('Error:', error))
  .then( res => {
    let i, html_ = ''
    for( i in res.data ){
      let row = res.data[ i ]
      html_ += `<tr class="even gradeC">
          <th>${row.init_user}</th>
          <th>${row.next_user}</th>
          <th>${row.date_}</th>
        </tr>`
    }
    let tableBody = document.querySelector('#tabla-flujo-anticipo > tbody')
    tableBody.innerHTML = html_

    $('#cargando-flujo-anticipo').hide()
    $('#tabla-flujo-anticipo').show()
  })
}

  




const load = () => {
  const btnsFlujos = document.querySelectorAll('.btns-flujo')
  for( let btn of btnsFlujos ){
    btn.addEventListener('click', function() {
      let id = this.getAttribute('data')
      //console.log(' id anticipo = '+ id )
      cargarFlujo( id )
    })
  }
}

document.addEventListener("DOMContentLoaded", load, false)


</script>
@endsection