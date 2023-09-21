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
                              <th>Fecha pago</th>
                              <th>Valor anticipo</th>
                              <th>Forma de pago</th>
                              <th>Proveedor asociado</th>
                              <th>Concepto</th>
                              <th>Estado</th>
                              <th>Documento</th>
                              <th>Gestionar</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($anticipos as $anticipo)
                              <td>{{$anticipo->id}}</td>
                              <td>{{$anticipo->name}}</td>
                              <td>{{$anticipo->fecha_pago}}</td>
                              <td>{{$anticipo->valor_anticipo}}</td>
                              <td>{{$anticipo->forma_pago}}</td>
                              <td>{{$anticipo->proveedor}}</td>
                              <td>{{$anticipo->concepto}}</td>
                              <td>{{$anticipo->estado}}</td>
                              <td><input type='button' class='btn btn-info' value='Ver' style='color:white;' onclick='CargarModalFiles("{{$anticipo->id}}");'></td>
                              @if($anticipo->estado == '2')
                              <td><a href="" class="btn btn-outline-success">Pagado</a>
                              </td>
                              @else
                              <td><a href="{{url('anticipos/')}}/{{$anticipo->id}}/{{$anticipo->id_user}}/pagar" class="btn btn-outline-success">Pagar</a>
                              @endif
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

function CargarModalFiles(id){
  var token=$("input[name=_token]").val();
  $('#archivosadjuntos').modal('show');
  $.ajax({
      data:{token:token,
           id:id},
      url:'https://flora.tierragro.com/api/adjuntosfilesequivalentes',
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


</script>
@endsection