@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Factura {{$invoice->supplier->name}} - {{$invoice->number}}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col d-flex flex-row justify-content-center">
                            <div class="d-flex flex-column text-center">
                                <a href="{{asset('facturas')}}/{{$invoice->file}}"><svg width="100px" height="100px" viewBox="0 0 16 16" class="bi bi-file-earmark-text-fill" fill="#52b788" xmlns="http://www.w3.org/2000/svg">
                                  <path fill-rule="evenodd" d="M2 2a2 2 0 0 1 2-2h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm7 2l.5-2.5 3 3L10 5a1 1 0 0 1-1-1zM4.5 8a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zM4 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5z"/>
                                </svg></a>
                                <br>
                                <h1>{{ucfirst($invoice->concept)}}</h1>
                                <h3>$ {{number_format($invoice->total,0)}}</h3>
                                <h6>${{number_format($invoice->subtotal,0)}} | ${{number_format($invoice->iva,0)}}</h6>
                                <h6>{{$invoice->due_date}}</h6>
                            </div>
                        </div>
                    </div>
                    <br>
                    
                    <div class="card-body">
                    <div class="coces">
                                <h5 class="text-center"><strong>Centros de Costos</strong></h5>
                    @foreach($invoice->distribution as $coce)
                              <div class="form-row">
                                      <div class="col">
                                          <select class="form-control" id="coce{{$loop->iteration}}" name="coce{{$loop->iteration}}" required disabled>
                                              <option value="">Centro de costos...</option>   
                                              @foreach($costCenters as $cocex)
                                                  @if($cocex->id == $coce->id)
                                                      <option value="{{$cocex->id}}" selected>{{$cocex->name}} - {{$cocex->code}}</option>
                                                  @else
                                                      <option value="{{$cocex->id}}">{{$cocex->name}} - {{$cocex->code}}</option>
                                                  @endif  
                                              @endforeach
                                          </select>
                                      </div>
                                      <div class="col">
                                        <input type="text" class="form-control" name="percent{{$loop->iteration}}" placeholder="Porcentaje" value="{{$coce->pivot->percentage}}" required disabled>
                                      </div>
                                      <div class="col">
                                        <input type="text" class="form-control format-number" name="value{{$loop->iteration}}" placeholder="Valor" value="{{number_format($coce->pivot->value,0)}}" required disabled>
                                      </div>
                                  </div>
                                  <br>
                                  @php
                                      $cont = $loop->iteration;
                                  @endphp
                    @endforeach
                    </div>
                   </div>


                   <div class="row">
                        <div class="col">
                            <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Responsable</th>
                                        <th>Estado</th>
                                        <th>Observación</th>
                                        <th>Soporte</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->log as $log)
                                    <tr>
                                        <td>{{$log->created_at}}</td>
                                        <td>{{$log->user->name}}</td>
                                        <td>{{$log->state->name}}</td>
                                        <td>{{$log->description}}</td>
                                        @if($log->file <> NULL)
                                            <td><a href="{{asset('facturas')}}/{{$log->file}}">soporte</a></td>
                                        @else
                                        <td></td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection