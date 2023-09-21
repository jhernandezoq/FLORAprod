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
                <div class="card-header">Facturas de costo por gestionar</div>
                <div class="card-body"><h5>Facturas pendientes:{{$countInvoices}}</h5></div>
                @if($role!=1)
                <div class="card-body">
                    <form action="{{url('/invoices/invoicesfindercosto')}}" method="POST">
                      @csrf
                      <div class="form-row">
                        <div class="form-group col-sm-6"><br>
                          <label for="user">Proveedor:</label>
                          <select class="form-control" id="supplier_nit" name="supplier_nit">
                            <option value="0" selected="selected">Seleccione...</option>
                            @foreach($suppliers as $supplier)
                              <option value="{{$supplier->id}}">{{$supplier->nit}} - {{$supplier->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group col-sm-6"><br>
                          <label for="profile">Factura:</label>
                          <select class="form-control" id="invoice" name="invoice">
                            <option value="0" selected="selected">Seleccione...</option>
                            @foreach($invoices as $invoice)
                              <option value="{{$invoice->invoice_id}}">{{$invoice->number}}</option>
                            @endforeach
                          </select>
                        </div>
                      <button type="submit" class="btn btn-info" style="float: left; margin-right: : 1%;margin-bottom: 1%;">Buscar</button><br>
                    </form><br>
                   </div>
                   </div>
                  @endif
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
                              <th>Observación</th>
                              <th>Fech envio</th>
                              <th>Gestionar</th>
                              @if($role==1)
                               <th>Reasignar</th>
                              @endif
                              @if($role !=1)
                               <th>Revisado</th>
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
                              <td><a href="https://flora.tierragro.com/facturas/{{$invoice->file}}" target="_blank">{{$invoice->number}}</a></td>
                              <td>${{number_format($invoice->subtotal,2)}}</td>
                              <td>${{number_format($invoice->total,2)}}</td>
                              <td>{{$invoice->description}}</td>
                              <td>{{$invoice->due_date}}</td>
                              <td><a href="{{url('invoice/facosto')}}/{{$invoice->invoice_id}}/{{$invoice->next_user_id}}" class="btn btn-outline-success">Gestionar</a></td>
                              </td>
                              @if($role==1)
                              <td><input type="button" class="btn btn-success" value="Reasignar" onclick="CargarModalEdicion({{$invoice->invoice_id}},{{$user->id}});"></td>
                              @endif
                              @if($role!=1)
                                 @if($invoice->revision == '0')
                                  <td><div class="form-check" style="margin-left:40%;"><input class="form-check-input" type="checkbox" value="{{$invoice->invoice_id}}" id="{{$invoice->invoice_id}}" onchange="Revision({{$invoice->invoice_id}});"></div></td>
                                 @else
                                 <td><div class="form-check" style="margin-left:40%;"><input class="form-check-input" type="checkbox"  checked value="{{$invoice->invoice_id}}" id="{{$invoice->invoice_id}}" onchange="Revision({{$invoice->invoice_id}});"></div></td>
                                 @endif
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

$(document).ready(function () {
  // load_data_invoices(1);
   $('#supplier').select2();
   $('#invoice').select2();
   $('#supplier_nit').select2();
});

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
      url:'https://flora.tierragro.com/api/invoicesdistributionsupdate',
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


function Revision(id_factura){
  var estado='';
  var factura=id_factura;
  if($('#'+id_factura).is(':checked') ) {
    estado=1;
      $.ajax({
        data:{ factura: factura,
               estado: estado },
        url:'https://flora.tierragro.com/api/invoicesrevision',
        type:'POST',
        dataType :'JSON',
        success:function(data){
          console.log("Registro actualizado");
          
        }
      });
    
  }else{
    estado=0;
    $.ajax({
      data:{ factura: factura,
               estado: estado },
      url:'https://flora.tierragro.com/api/invoicesrevision',
      type:'POST',
      dataType :'JSON',
      success:function(data){
        console.log("Registro actualizado");

       }
    });

  }

}

</script>
@endsection