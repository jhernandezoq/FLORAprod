@extends('layouts.app')

@section('content')


  <!-- Modal -->
  <div class="modal fade" role="dialog" id="UsuarioEncargado">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" style="margin-left: -2%;">&times;</button>
          <h4 class="modal-title">Seleccione el usuario encargado</h4>
        </div>
        <input id="id_proveedor" type="text" style="display:none;">
        <input id="id_user_on" type="text" style="display:none;">
        <div class="modal-body">
            <table class="table-responsive-md table-bordered table-striped table-sm" id="adjuntosfiles">
                <thead>
                    <tr>
                      <th>Usuario</th>
                    </tr>
                </thead>
                
                <tbody>
                </tbody>
            </table>          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" data-dismiss="modal" onclick="Actualizar();">Actualizar</button>
        </div>
      </div>
      
    </div>
  </div>


<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                        <div id="success_message" class="alert alert-success" role="success" style="display:none;">
                            <ul>
                                  <span>Excelente!!</span>
                                  <li>Los cambios se guardaron de forma exitosa!!.</li>
                            </ul>
                        </div>
                <div class="card-header">Distribución de proveedores</div>
                <br><div style="margin-left:90%;"><button type="button" class="btn btn-success" data-dismiss="modal" onclick="ActualizarFacturas();" style="display:none;">Actualizar</button></div>

                <div class="card-body">
                    <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                        		<th>Proveedor</th>
                              	<th>Usuario encargado</th>
                              	<th>Correo usuario</th>
                              	<th>Editar</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($distributions as $distribution)
                        	<tr>
                              <td>{{$distribution->supplier_name}}</td>
                              <td>{{$distribution->user_name}}</td>
                              <td>{{$distribution->user_email}}</td>
                              <td><input type="button" class="btn btn-success" value="Editar" onclick="CargarModalEdicion({{$distribution->id}});"></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <input type="text" id="user_on" value="{{$user->id}}" style="display:none;">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script type="text/javascript">
$(document).ready(function () {

$('#id').select2();
$("#id_users").select2();

});


function CargarModalEdicion(id){
    $('#UsuarioEncargado').modal('show');
    $("#id_users").select2();
  
  $.ajax({
      data:{ id },
      url:'https://flora.tierragro.com/api/usersdistributions',
      type:'POST',
      dataType :'JSON',
      success:function(data){
          $('#adjuntosfiles tbody tr').remove();
          let Cantidad_Elementos = data.length;
          let i
          optionValue='';
          for( i = 0; i < Cantidad_Elementos; i++ ){              
            optionValue = optionValue + "<option value="+data[i].id+">"+data[i].name+"</option>";
          }

            $("#adjuntosfiles > tbody").append(
             "<tr class=\"even gradeC\">" +
                "<td><select id=\"id_users\" name=\"id\" class=\"users\">"+optionValue+"</select></td>"+
              "</tr>");
              $("#id_users").select2();

       }
    });


    $("#id_proveedor").val(id);

    $("#id_users").select2();

  
}


function Actualizar(){
    var id_usuario= $("#id_users").val();
    var id_proveedor = $("#id_proveedor").val();
    var user_on = $("#user_on").val();
    $.ajax({
      data:{ id_usuario : id_usuario,
             id_proveedor : id_proveedor,
             user_on : user_on },
      url:'https://flora.tierragro.com/api/usersdistributionsupdate',
      type:'POST',
      dataType :'JSON',
      success:function(data){
        if(data == 1){
            $("#success_message").show('slow');
            window.scroll(0, 0);
        }

        setTimeout(location.reload(), 5000);
        

       }
    });
    
}



function ActualizarFacturas(){
    alert("Ingreso aqui");
   /* var id_usuario= $("#id_users").val();
    var id_proveedor = $("#id_proveedor").val();
    var user_on = $("#user_on").val();
    $.ajax({
      data:{ id_usuario : id_usuario,
             id_proveedor : id_proveedor,
             user_on : user_on },
      url:'http://localhost/flora/public/api/usersdistributionsupdate',
      type:'POST',
      dataType :'JSON',
      success:function(data){
        if(data == 1){
            $("#success_message").show('slow');
            window.scroll(0, 0);
        }

        setTimeout(location.reload(), 5000);
        

       }
    });*/
    
}


</script>
@endsection