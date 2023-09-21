@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Edición de facturas</div>
                <div class="card-body">
                    <table class="table-responsive-md table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                              <th>Referencia</th>
                              <th>Proveedor</th>
                              <th>Flujo</th>
                              <th>Vencimiento</th>
                              <th>Total</th>
                              <th>Estado</th>
                              <th>Nueva referencia</th>
                              <th></th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($datas as $data)
                              <td>{{$data->number}}</td>
                              <td>{{$data->supplier}}</td>
                              <td>{{$data->flow}}</td>
                              <td>{{$data->due}}</td>
                              <td>{{$data->total}}</td>
                              <td>{{$data->state}}</td>
                              <td><form method="POST" action="{{url('reports/finaleditinvoices')}}">
                              	@csrf
                              <input type="text" name="invoice_id" value="{{$data->id}}" style="display: none;">
                              <input type="text" name="reference" style="max-width: 50%;">
                              <button type="submit" class="btn btn-success">Guardar</button>
                              </form></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection