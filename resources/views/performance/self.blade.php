@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center performance-area">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><p>{{ __('Autovaloración') }}</p>
                    <ul class="list-inline">
                        <li class="list-inline-item"><h5>Responde la siguiente encuesta siendo:</h5></li>
                        <li class="list-inline-item"><h5>1: No se observa</h5></li>
                        <li class="list-inline-item"><h5>2: Bajo</h5></li>
                        <li class="list-inline-item"><h5>3: Regular</h5></li>
                        <li class="list-inline-item"><h5>4: Bueno</h5></li>
                        <li class="list-inline-item"><h5>5: Sobresaliente</h5></li>
                    </ul>
                </div>    
                <div class="card-body">
                    @if($show == 1)
                        @foreach($responses as $response)
                        <div class="alert alert-success" role="alert">
                            <div class="row">
                                <div class="col-sm-10 question-text">
                                      {{$response->question->text}}
                                </div>
                                <div class="col-sm-2 response-value">
                                    {{round($response->value)}}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                    <form action="{{url('performance/storeSelfAssessment')}}" method="POST">
                    @csrf
                    @php
                        $dimension = 0;
                        $pregunta = 0;
                    @endphp
                    @foreach($questions as $question)
                        <div class="title">
                            @php
                                if($question->dimension->id != $dimension)
                                {
                                    echo "Dimension del ".$question->dimension->name."<span class='orange'>.</span>";
                                    $dimension = $question->dimension->id;
                                    $pregunta = 0;
                                }
                                $pregunta++;
                            @endphp 
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="alert alert-secondary" role="alert">
                                  <h4 class="alert-heading">Pregunta {{$pregunta}}</h4>
                                  <p class="question-text">{{$question->self_text}}.</p>
                                  <hr>
                                    @for($i=1;$i<=5;$i++)
                                        <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="radio" name="{{$question->id}}" id="{{$question->id}}" value="{{$i}}" required="required">
                                          <label class="form-check-label" for="{{$question->id}}-{{$i}}">{{$i}}</label>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="row justify-content-center">
                        <input type="submit" class="btn btn-success" name="guardar" value="Guardar" id="Guardar" onclick="OcultarBoton();">
                    </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

function OcultarBoton(){
    $("#Guardar").hide('slow');
}
</script>
@endsection