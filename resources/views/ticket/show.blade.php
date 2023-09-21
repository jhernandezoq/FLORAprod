@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center ticket-show">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Caso # {{$ticket->id}}</div>

                <div class="card-body">
                    @if($ticket->status_id == 4)
                        @if($showSolution == 1)
                            <div class="card-border">
                              <div class="card-body">
                                <h5 class="card-title">¡Hola! {{$ticket->agent->first_name}} ha solucionado tu caso:</h5>
                                <p class="card-text">{{$solution->text}}</p>
                                <a href="{{ url('ticket')}}/{{ $ticket->id }}/solutiona" class="btn btn-success">Aceptar</a>
                                <a href="{{ url('ticket')}}/{{ $ticket->id }}/solutiond" class="btn btn-danger">Rechazar</a>
                              </div>
                            </div>
                        @endif
                    @endif
                    
                    <div class="row">
                        <div class="col-sm-6 text-justify">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="title">Descripción del caso<span class="orange">.</span></div>
                                    {{$ticket->text}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="title">Soporte usuario<span class="orange">.</span></div>
                                    @if($ticket->file <> "")
                                        <a href="{{asset('tickets')}}/{{$ticket->file}}">{{$ticket->file}}</a>
                                    @else
                                        No hay archivo de soporte del usuario.
                                    @endif
                                
                                </div>
                            </div>
                            <div class="row">
                              <div class="col-sm-12">
                                <div class="title">Soporte TI<span class='orange'>.</span></div>
                                   @if($elements != 0)
                                    @foreach($data as $data)
                                      <a href="{{asset('tickets')}}/{{$data->file}}">{{$data->file}}</a><br>
                                    @endforeach
                                   @else
                                   No hay archivo de soporte de TI.<br><br>
                                   @endif
                                </div>
                            </div>
                            @if($elements != 0)
                            <div class="row" style="margin-top: -20%;">
                                <div class="col-sm-12">
                                    <div class="title">Responsable<span class="orange">.</span></div>
                                    {{$ticket->agent->name}}
                                </div>
                            </div>
                            @else
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="title">Responsable<span class="orange">.</span></div>
                                    {{$ticket->agent->name}}
                                </div>
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="title">Estado<span class="orange">.</span></div>
                                    <div class="badge badge-pill badge-primary">{{$ticket->state->name}}</div>
                                </div>
                            </div>
                            
                            @if($ticket->status_id != 5 and $ticket->status_id != 7)
                            <div class="commentForm">
                                <form action="{{ url('ticketlog') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="ticket_id" value="{{$ticket->id}}">
                                    <div class="form-group">
                                        <textarea class="form-control" id="text" name="text" rows="3" required></textarea>
                                    </div>
                                    <div class="row justify-content-end">
                                        <input type="submit" class="btn btn-success" name="comentar" value="Comentar">
                                    </div>
                                </form>
                            </div>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="title">Historia<span class="orange">.</span></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    @foreach($ticket->log as $log)
                                        <div class="row">
                                            <div class="col-sm-4">{{$log->created_at}}</div>
                                            <div class="col-sm-8 text-justify">{{$log->text}}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection