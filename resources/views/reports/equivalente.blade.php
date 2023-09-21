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
                <div class="card-header">Documentos equivalentes generados</div>
                <div class="card-body"><h5>Documentos generados sin finalizar: <b>{{$countDocuments}}</b></h5></div>
                  @if($countDocuments>0)
                    <table class="table-responsive-md table-bordered table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                              <th>Numero de documento</th>
                              <th>Archivos adjuntos</th>
                              <th>Proveedor</th>
                              <th>Nit/CC proveedor</th>
                              <th>Fecha del documento</th>
                              <th>Total</th>
                              <th>Estado</th>
                              <th>Editar</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($datos as $dato)
                              <td><form method="POST" action="{{url('pdf/equivalentepdf')}}">
                                 <input type="text" name="document_id" value="{{$dato->id}}" style="display: none;">
                                @csrf
                              <button type="submit" class="" style="outline:none; text-decoration: none; border:none; background-color:white; color:#008f39; text-decoration-line: underline; text-align: center;">{{$dato->numero_documento}}</button>
                              </form></td>
                              <td><input type='button' class='btn btn-info' value='Ver' style='color:white; margin-left: 30%;' onclick='CargarModalFiles("{{$dato->id}}");'></td>
                              <td>{{$dato->proveedor}}</td>
                              <td>{{$dato->id_proveedor}}</td>
                              <td>{{$dato->fecha_documento}}</td>
                              <td>{{$dato->Total}}</td>
                              <td>{{$dato->estado}}</td>
                              <td><form method="POST" action="{{url('reports/equivalentedit')}}">
                                 <input type="text" name="document_id" value="{{$dato->id}}" style="display: none;">
                                @csrf
                              <button type="submit" class="btn btn-success">Editar</button>
                              </form></td>
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

function CargarModalFiles(id){
  var token=$("input[name=_token]").val();
  $('#archivosadjuntos').modal('show');
  $.ajax({
      data:{token:token,
           id:id},
      url:'http://localhost/flora/public/api/adjuntosfilesequivalentes',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#adjuntosfiles tbody tr').remove();
          var Cantidad_Elementos=data.length;
          for (var i = 0; i < Cantidad_Elementos; i++) {
            $("#adjuntosfiles > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<th>"+data[i].date+"</th>" +
                "<th><a href='http://localhost/flora/storage/app/equivalentes/"+data[i].file+"'>"+data[i].file+"</a></th>"+
              "</tr>");
          }
    }
  });
}


</script>
@endsection