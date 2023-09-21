@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Anticipos por legalizar</div>
                <div class="card-body"><h5>Legalizaciones pendientes: {{$count[0]->count}}</h5></div>
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
                              <th>Concepto</th>
                              <th>Estado</th>
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
                              <td>{{$anticipo->concepto}}</td>
                              <td>{{$anticipo->estado}}</td>
                              <td><a href="{{url('anticipos/')}}/{{$anticipo->id}}/{{$anticipo->id_user}}" class="btn btn-outline-success">Legalizar</a>
                              </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <p>¡no tienes anticipos pendientes por legalizar!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection