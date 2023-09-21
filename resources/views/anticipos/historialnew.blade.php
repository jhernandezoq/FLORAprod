@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Historial anticipos</div>
                <div class="card-body">
                  <textarea class="form-control" id="concepto_anticipo" name='concepto_anticipo' rows="4" required="" style="display: none;"></textarea>
                  @if($count != 0)
                    <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                              <th># Anticipo</th>
                              <th>Fecha de pago</th>
                              <th>Valor</th>
                              <th>Forma de pago</th>
                              <th>Concepto</th>
                              <th>Usuario encargado</th>
                              <th>Estado</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($anticipos as $anticipo)
                              <td>{{$anticipo->id}}</td>
                              <td>{{$anticipo->fecha_pago}}</td>
                              <td>{{$anticipo->valor_anticipo}}</td>
                              <td>{{$anticipo->forma_pago}}</td>
                              <td>{{$anticipo->concepto}}</td>
                              <td>{{$anticipo->gestionando}}</td>
                              <td>{{$anticipo->estado}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <p>¡no tienes anticipos solicitados!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection