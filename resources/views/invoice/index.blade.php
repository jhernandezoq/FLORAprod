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
        <input id="id_invoice" type="text" style="display:none;">
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
                <div class="card-header">Facturas por Gestionar</div>
                <div class="card-body"><h5>Facturas pendientes:{{$countInvoices}}</h5></div>
                @if($user->profile_name == 'COORDINADORA DE TESORERIA')
                <a href="{{url('invoice/aprobacion_masiva')}}"><div class="card-body"><button type="button" class="btn btn-danger">Autorización masiva</button></div></a>
                @endif                
                <div class="card-body">
                  @if($countInvoices>0)
                    <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                              <th></th>
                              <th>Proveedor</th>
                              <th>Factura</th>
                              <th>Subtotal</th>
                              <th>Total</th>
                              <th>Concepto</th>
                              <th>Moneda</th>
                              <th>Vencimiento</th>
                              <th>Gestionar</th>
                              @if($user->ubication_name == 'CONTABILIDAD')
                              <th>Reasignar</th>
                              @endif
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($invoices as $invoice)

                            @if($invoice->priority == 1)
                              <td class="text-center">
                                <svg width="30" height="30" viewBox="0 0 16 16" class="bi bi-exclamation" fill="red" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                                </svg>
                              </td>
                            @else
                              <td></td>
                            @endif
                              <td>{{$invoice->supplier}}</td>
                              <td>{{$invoice->number}}</td>
                              <td>${{number_format($invoice->subtotal,2)}}</td>
                              <td>${{number_format($invoice->total,2)}}</td>
                              <td>{{$invoice->concept}}</td>
                              <td>{{$invoice->currency}}</td>
                              <td>{{$invoice->due_date}}</td>
                              <td><a href="{{url('invoice/')}}/{{$invoice->invoice_id}}/{{$invoice->next_user_id}}" class="btn btn-outline-success">Gestionar</a>
                              </td>
                              @if($user->ubication_name == 'CONTABILIDAD')
                                <td><input type="button" class="btn btn-success" value="Reasignar" onclick="CargarModalEdicion({{$invoice->invoice_id}},{{$user->id}});"></td>
                              @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <p>¡no tienes facturas pendientes por gestionar!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script type="text/javascript">

function CargarModalEdicion(id, id_user){
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
              $("#id_user_on").val(id_user);

       }
    });


    $("#id_invoice").val(id);

    $("#id_users").select2();

  
}


function Actualizar(){
    var id_usuario= $("#id_users").val();
    var id_invoice = $("#id_invoice").val();
    var user_on = $("#id_user_on").val();
    $.ajax({
      data:{ id_usuario : id_usuario,
             id_invoice : id_invoice,
             user_on : user_on },
      url:'https://flora.tierragro.com/api/invoicesdistributionsupdategestion',
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

</script>
@endsection