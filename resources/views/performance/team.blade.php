@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center performance-area">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Mi Equipo') }}</div>
                <div class="card-body">
                    @foreach($team as $member)
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="alert alert-secondary" role="alert" style="height: 80%;">
                                    <a href="{{url ('performance/assessment')}}/{{$member->id}}" class="alert-link">{{$member->name}}</a>
                                    @foreach($responses as $response)
                                       @if($response->evaluated_id == $member->id)
                                        <img src="../img/checked.png" style="width:20px;height:20px; margin-left: 1%; margin-top: -1%;">
                                        @endif
                                     @endforeach
                                     @foreach($history as $histories)
                                      @if($histories->evaluated_id == $member->id)
                                      <a href="{{url('performance/history')}}/{{$member->id}}"><img src="../img/historial.png" style="width:20px;height:20px; margin-left: 1%; margin-top: -1%;"></a>
                                      @endif
                                     @endforeach
                                    <hr>
                                    @if($countverification == 0)
                                       <p><i>El usuario aún no ha realizado el proceso</i></p>
                                    @else
                                    @foreach($lastverifications as $lastverification)
                                     @if(($lastverification->evaluated_id == $member->id) && ($lastverification->count_responses != 0) && ($lastverification->count_pdis == 0))
                                      <p><i>Ya puede realizar el proceso de valoración</i></p>
                                     @elseif(($lastverification->evaluated_id == $member->id) && ($lastverification->count_responses == 0) && ($lastverification->count_pdis == 0))
                                     <p><i>El usuario aún no ha realizado el proceso</i></p>
                                     @endif
                                    @endforeach
                                    @endif
                                    @foreach($data as $datas)
                                     @if($datas->evaluated_id == $member->id)
                                        @if($datas->amount >0)
                                    @if($datas->amountpd > 0)
                                    <p>Dimensiones</p>
                                    @foreach($mainaverage as $averag)
                                     @if($averag->evaluated_id == $member->id)
                                        @if($averag->dimension==1)
                                        <div class="row">
                                            <div class="col-sm-2 response-value">
                                                <span class="badge badge-dark">Ser: {{$averag->average}}</span>
                                            </div>
                                            @elseif($averag->dimension==2)
                                            <div class="col-sm-2 response-value">
                                                <span class="badge badge-dark">Saber: {{$averag->average}}</span>
                                            </div>
                                            @elseif($averag->dimension==3)
                                            <div class="col-sm-2 response-value">
                                                <span class="badge badge-dark">Hacer: {{$averag->average}}</span>
                                            </div>
                                            @else
                                            <div class="col-sm-2 response-value">
                                                <span class="badge badge-dark">Total: {{$averag->average}}</span>
                                            </div>
                                        </div>
                                        @endif
                                    @endif
                                    @endforeach
                                    @else
                                    <p><i>Ya puedes realizar el proceso de valoración</i></p>
                                    @endif
                                    @else
                                    <p><i>El usuario aún no ha realizado el proceso</i></p>
                                    @endif
                                    @endif
                                    @endforeach
                                </div><br><br>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection