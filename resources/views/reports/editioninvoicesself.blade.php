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
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($datas as $data)
                              <td>{{$data->number}}</td>
                              <td>{{$data->supplier}}</td>
                              <td>{{$data->flow}}</td>
                              <td>{{$data->due}}</td>
                              <td>{{$data->total}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table><br>
                    <h4><i>A continuación ingrese los items a editar de la factura</i></h4><br>
                    <form method="POST" action="{{url('reports/finaleditinvoicesself')}}">
                        @csrf
                        <div class="form-row">
                          <input type="text" name="invoice_id" value="{{$data->id}}" style="display: none;">
                            <div class="form-group col-sm-3">
                                <label for="subtotal">Referencia:</label>
                                <input type="text" class="form-control format-number" id="reference" name="reference" placeholder="Referencia">
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="iva">Proveedor:</label>
                                  <select class="form-control" name="supplier">
                                    <option value="" selected="selected">Seleccione el proveedor...</option>
                                    @foreach($suppliers AS $supplier)
                                      <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                    @endforeach
                                  </select>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="total">Total:</label>
                                <input type="text" class="form-control format-number" id="total" name="total" placeholder="Total">
                            </div>
                            <div class="form-group col-sm-3">
                              <button type="submit" class="btn btn-success" style="margin-top: 12%;">Guardar</button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection