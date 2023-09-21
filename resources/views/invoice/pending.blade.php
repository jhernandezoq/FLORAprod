@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Flujo de facturas actual</div>

                <div class="card-body">
                  @if($countInvoices>0)
                    <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                            	<th></th>
                        		<th>Factura</th>
                              	<th>Proveedor</th>
                              	<th>Valor</th>
                              	<th>Vencimiento</th>
                              	<th>Responsable</th>
                              	<th>Notificar</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($invoices as $invoice)
                        	<tr>
                            @if($invoice->priority == 1)
                              <td class="text-center">
                              	<svg width="30" height="30" viewBox="0 0 16 16" class="bi bi-exclamation" fill="red" xmlns="http://www.w3.org/2000/svg">
            								  <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
            								</svg>
            							   </td>
                            @else
                              <td></td>
                            @endif
                        	    <td><a href="{{url('invoice')}}/{{$invoice->invoice_id}}/log">{{$invoice->number}}<a></td>
                              <td>{{$invoice->supplier}}</td>
                              <td>${{number_format($invoice->total,0)}}</td>
                              <td>{{$invoice->due_date}}</td>
                              <td>{{$invoice->user}}</td>
                              <td><a href="{{url('invoice/notify')}}/{{$invoice->invoice_id}}" class="btn btn-success">Notificar</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                   
                    @else
                        <p>¡El flujo de facturas esta vacío , no hay facturas pendientes por gestionar!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection