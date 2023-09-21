@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Facturas sin asignar</div>
            <div class="card-body">
            <form action="{{url('/invoices/invoicesfinderunassigned')}}" method="POST">
              @csrf
              <div class="form-row">
                <div class="form-group col-sm-6"><br>
                   <label for="user">Proveedor:</label>
                   <select class="form-control" id="supplier_nit" name="supplier_nit">
                    <option value=a"0" selected="selected">Seleccione...</option>
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
                      <option value="{{$invoice->id}}">{{$invoice->number}}</option>
                    @endforeach
                   </select>
                </div>
              <button type="submit" class="btn btn-info" style="float: left; margin-right: : 1%;margin-bottom: 1%;">Buscar</button><br>
            </form><br>
           </div>
          </div>

                <div class="card-body">
                  @if($countInvoices>0)
                    <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                        		<th>Factura</th>
                              	<th>Proveedor</th>
                                <th>Vencimiento</th>
                                <th>Total</th>
                              	<th>Concepto</th>
                              	<th>Aceptar</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($invoices as $invoice)
                        	<tr>
                            <td><a href="https://flora.tierragro.com/facturas/{{$invoice->file}}" target="_blank">{{$invoice->number}}<a></td>
                              <td>{{$invoice->supplier}}</td>
                              <td>{{$invoice->due_date}}</td>
                              <td>${{number_format($invoice->total,0)}}</td>
                              <td>{{$invoice->concept}}</td>
                              <td><a href="{{url('takeinvoice')}}/{{$invoice->id}}" class="btn btn-success">Aceptar</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                   
                    @else
                        <p>¡El flujo de facturas esta vacío , no hay facturas pendientes por asignar!</p>
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

</script>
@endsection