@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center ticket-show">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Aceptar solución al caso #{{$ticket->id}}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 text-justify">
                            {{$ticket->text}}
                        </div>
                    </div>
                    <div>
                        <div>
                            <form action="{{ url('ticket') }}/{{$ticket->id}}/accept" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="title">Observación de la solución<span class="orange">.</span></div>
                                    <textarea class="form-control" id="text" name="text" rows="3"></textarea>
                                </div>

                                <div class="row justify-content-center">
                                    <input type="submit" class="btn btn-success" name="solucionar" value="Aprobar Solución">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection