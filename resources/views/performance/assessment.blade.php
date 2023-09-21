@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center performance-area">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><p>{{ __('Valoración') }}</p>
                    <ul class="list-inline">
                        <li class="list-inline-item"><h5>Responde la siguiente encuesta siendo:</h5></li>
                        <li class="list-inline-item"><h5>1: No se observa</h5></li>
                        <li class="list-inline-item"><h5>2: Bajo</h5></li>
                        <li class="list-inline-item"><h5>3: Regular</h5></li>
                        <li class="list-inline-item"><h5>4: Bueno</h5></li>
                        <li class="list-inline-item"><h5>5: Sobresaliente</h5></li>
                    </ul>
                </div>                
                    @if(count($errors) > 0)
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                  <span>Recuerda:</span>
                                  <li>Si el promedio de alguna de las dimensiones es menor a 4.5, es obligatorio completar el PDI correspondiente a esa dimensión</li>
                                  <li>Cuando llenes un PDI es obligatorio llenar la información del objetivo, la acción y la fecha de compromiso</li>
                            </ul>
                        </div>
                    @endif
                    <input type="textarea" name="countresponses" value="{{$countresponses}}" style="display: none;" id="countresponses">
                <div class="card-body">
                    @if($show == 1)
                    <form action="{{url('performance/storeAssessment')}}" method="POST" id="formulario">
                        @csrf
                        @php
                            $dimension = 0;
                            $pregunta = 0;
                        @endphp
                        @foreach($responses as $response)
                        <div class="title">
                            @php
                                if($response->question->dimension->id != $dimension)
                                {
                                    echo "Dimensión del ".$response->question->dimension->name."<span class='orange'>.</span>";
                                    $dimension = $response->question->dimension->id;
                                    $pregunta = 0;
                                }
                                $pregunta++;
                            @endphp
                        </div>
                        <input type="hidden" name="validate" value="validate">
                        <input type="hidden" name="evaluated_id" value="{{$person}}">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-success" role="alert">
                                      <h4 class="alert-heading">Pregunta {{$pregunta}}</h4>
                                      <div class="row">
                                            <div class="col-sm-10 question-text">
                                                {{$response->question->text}}
                                            </div>
                                            <div class="col-sm-2 response-value">
                                                {{round($response->value)}}
                                            </div>
                                      </div>
                                      
                                      <hr>
                                      @for($i=1;$i<=5;$i++)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input"
                                             type="radio" name="{{$response->question_id}}" id="{{$response->question_id}}" value="{{$i}}" {{ (old($response->question_id) == $i) ? 'checked' : ''}} required="required">
                                            <label class="form-check-label" for="{{$response->question_id}}-{{$i}}">{{$i}}</label>
                                            </div>
                                      @endfor
                                    </div>      
                                </div>
                            </div>

                            @if($leaderamount > 0)
                                 @if($pregunta == 10)
                                 @if($response->question->dimension->name == 'Ser')
                                  <div class="row">
                                      <div class="col-sm" style="margin-top: 4%; margin-left: 33%;">
                                        <label><h5>Promedio:</h5></label>
                                          <input type="text" name="prom" id="prom" value="{{ session()->get('average1') }}" {{old("prom")}} style="width: 45px;" disabled="disabled">
                                      </div>
                                  </div>
                                 @elseif($response->question->dimension->name == 'Saber')
                                  <div class="row">
                                    <div class="col-sm" style="margin-top: 4%; margin-left: 33%;">
                                      <label><h5>Promedio:</h5></label>
                                        <input type="text" name="prom1" id="prom1" value="{{ session()->get('average2') }}" {{old("prom")}} style="width: 45px;" disabled="disabled">
                                    </div>
                                  </div>
                                 @else
                                  <div class="row">
                                    <div class="col-sm" style="margin-top: 4%; margin-left: 33%;">
                                      <label><h5>Promedio:</h5></label>
                                        <input type="text" name="prom2" id="prom2" value="{{ session()->get('average3') }}" {{old("prom")}} style="width: 45px;" disabled="disabled">
                                    </div>
                                  </div>
                                  @endif
                                      <div class="form-group">
                                    <label for="pdi"> <div class="title"><h5><p>Si tienes un plan de desarrollo individual que deba ser implementado para la dimensi&#243;n del "{{$response->question->dimension->name}}" escribelo:</p></h5></div></label>
                                    <div class="row">
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                       <label><h5>Objetivo:</h5></label>
                                        <textarea type="text" class="form-control" rows="3" name="{{$response->question->dimension->name}}_objective">{{old('Ser_objective')}}</textarea>
                                      </div>
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                        <label><h5>Acción concreta:</h5></label>
                                        <textarea class="form-control" rows="3" name="{{$response->question->dimension->name}}_action"></textarea>
                                      </div>
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                        <label><h5>Fecha de compromiso:</h5></label>
                                        <input class="form-control" type="date" id="example-date-input" style="height: 70%;" name="{{$response->question->dimension->name}}_date">
                                      </div>
                                    </div>
                                  </div>
                                  
                                 @endif
                            @else
                               @if($pregunta == 7)
                                 @if($response->question->dimension->name == 'Ser')
                                  <div class="row">
                                    <div class="col-sm" style="margin-top: 4%; margin-left: 33%;">
                                      <label><h5>Promedio:</h5></label>
                                        <input type="text" name="prom" id="prom" value="{{ session()->get('average1') }}" {{old("prom")}} style="width: 45px;" disabled="disabled">
                                    </div>
                                  </div>    
                                 @elseif($response->question->dimension->name == 'Saber')
                                  <div class="row">
                                    <div class="col-sm" style="margin-top: 4%; margin-left: 33%;">
                                      <label><h5>Promedio:</h5></label>
                                        <input type="text" name="prom1" id="prom1" value="{{ session()->get('average2') }}" {{old("prom")}} style="width: 45px;" disabled="disabled">
                                    </div>
                                  </div>
                                 @else
                                  <div class="row">
                                    <div class="col-sm" style="margin-top: 4%; margin-left: 33%;">
                                      <label><h5>Promedio:</h5></label>
                                        <input type="text" name="prom2" id="prom2" value="{{ session()->get('average3') }}" {{old("prom")}} style="width: 45px;" disabled="disabled">
                                    </div>
                                  </div>
                                  @endif
                                  <div class="form-group">
                                    <label for="exampleFormControlTextarea1"> <div class="title"><h5><p>Si tienes un plan de desarrollo individual que deba ser implementado para la dimensi&#243;n del "{{$response->question->dimension->name}}" escribelo:</p></h5></div></label>
                                    
                                     @if($response->question->dimension->name == 'Ser')
                                     <div class="row">
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                      <label><h5>Objetivo:</h5></label>
                                        <textarea class="form-control" rows="3" name="{{$response->question->dimension->name}}_objective" value="{{session()->get('Ser_objective') }}">{{session()->get('Ser_objective') }}</textarea>
                                      </div>
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                      <label><h5>Acción concreta:</h5></label>
                                        <textarea class="form-control" rows="3" name="{{$response->question->dimension->name}}_action" value="{{ session()->get('Ser_action') }}">{{ session()->get('Ser_action') }}</textarea>
                                      </div>
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                        <label><h5>Fecha de compromiso:</h5></label>
                                        <input class="form-control" type="date" id="example-date-input" style="height: 70%;" name="{{$response->question->dimension->name}}_date" value="{{ session()->get('Ser_date') }}">
                                      </div>
                                    @elseif($response->question->dimension->name == 'Saber')
                                    <div class="row">
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                       <label><h5>Objetivo:</h5></label>
                                        <textarea class="form-control" rows="3" name="{{$response->question->dimension->name}}_objective" value="{{ session()->get('Saber_objective') }}">{{ session()->get('Saber_objective') }}</textarea>
                                      </div>
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                        <label><h5>Acción concreta:</h5></label>
                                        <textarea class="form-control" rows="3" name="{{$response->question->dimension->name}}_action" value="{{ session()->get('Saber_action') }}">{{ session()->get('Saber_action') }}</textarea>
                                      </div>
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                        <label><h5>Fecha de compromiso:</h5></label>
                                        <input class="form-control" type="date" id="example-date-input" style="height: 70%;" name="{{$response->question->dimension->name}}_date" value="{{ session()->get('Saber_date') }}">
                                      </div>
                                     @else
                                     <div class="row">
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                       <label><h5>Objetivo:</h5></label>
                                        <textarea class="form-control" rows="3" name="{{$response->question->dimension->name}}_objective" value="{{ session()->get('Hacer_objective') }}">{{ session()->get('Hacer_objective') }}</textarea>
                                      </div>
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                        <label><h5>Acción concreta:</h5></label>
                                        <textarea class="form-control" rows="3" name="{{$response->question->dimension->name}}_action" value="{{ session()->get('Hacer_action') }}">{{ session()->get('Hacer_action') }}</textarea>
                                      </div>
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                        <label><h5>Fecha de compromiso:</h5></label>
                                        <input class="form-control" type="date" id="example-date-input" style="height: 70%;" name="{{$response->question->dimension->name}}_date" value="{{ session()->get('Hacer_date') }}">
                                      </div>
                                     @endif
                                    </div>
                                  </div>
                               @endif
                            @endif
                        @endforeach

                        <div class="row justify-content-center">
                            <input type="submit" class="btn btn-success" name="guardar" value="Guardar"  style="margin-top: 4%;">
                        </div>
                    </form>
                    @else
                        @php
                            $dimension = 0;
                            $pregunta = 0;
                        @endphp
                        @foreach($responses as $response)
                            <div class="title">
                                @php
                                    if($response->question->dimension->id != $dimension)
                                    {
                                        echo "Dimensión del ".$response->question->dimension->name."<span class='orange'>.</span>";
                                        $dimension = $response->question->dimension->id;
                                        $pregunta = 0;
                                    }
                                    $pregunta++;
                                @endphp
                            </div>
                            <div class="alert alert-success" role="alert">
                              <h4 class="alert-heading">Pregunta {{$pregunta}}</h4>
                              <p class="question-text">{{$response->question->text}}.</p>
                              <hr>
                              <div class="row">
                                  <div class="col-sm-6 response-value">
                                    <span class="badge badge-success">Autovaloración : {{round($response->value)}}</span></span>
                                  </div>
                                  <div class="col-sm-6 response-value">
                                      <span class="badge badge-info" style="background-color: #ff701f; color: white;">Valoración : {{round($leaderResponses[$loop->index]->value)}}</span></span>
                                  </div>
                              </div>
                            </div>
                           @if($leaderamount > 0)
                                 @if($pregunta == 10)
                                  <div class="form-group">
                                    <label for="exampleFormControlTextarea1"> <div class="title"><h5><p>Plan de desarrollo individual a implementar para la dimensi&#243;n del "{{$response->question->dimension->name}}":</p></h5></div></label>
                                    @foreach($pdi as $pdis)
                                     @if($pdis->dimension_id == $response->question->dimension->id)
                                      <div class="row">
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                       <label><h5>Objetivo:</h5></label>
                                        <textarea  disabled="disabled" class="form-control" rows="3">{{$pdis->objective}}</textarea>
                                      </div>
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                        <label><h5>Acción concreta:</h5></label>
                                        <textarea disabled="disabled" class="form-control" rows="3">{{$pdis->action}}</textarea>
                                      </div>
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                        <label><h5>Fecha de compromiso:</h5></label>
                                        <input class="form-control" type="date" id="example-date-input" disabled="disabled" style="height: 70%;" value={{$pdis->followupdate}}>
                                      </div>
                                     @endif
                                    @endforeach
                                    
                                  </div>
                                 @endif
                            @else
                               @if($pregunta == 7)
                                  <div class="form-group">
                                    <label for="exampleFormControlTextarea1"> <div class="title"><h5><p>Plan de desarrollo individual a implementar para la dimensi&#243;n del "{{$response->question->dimension->name}}":</p></h5></div></label>
                                    @foreach($pdi as $pdis)
                                     @if($pdis->dimension_id == $response->question->dimension->id)
                                      <div class="row">
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                       <label><h5>Objetivo:</h5></label>
                                        <textarea  disabled="disabled" class="form-control" rows="3">{{$pdis->objective}}</textarea>
                                      </div>
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                        <label><h5>Acción concreta:</h5></label>
                                        <textarea disabled="disabled" class="form-control" rows="3">{{$pdis->action}}</textarea>
                                      </div>
                                      <div class="col-sm" style="margin-bottom: 2%;">
                                        <label><h5>Fecha de compromiso:</h5></label>
                                        <input class="form-control" type="date" id="example-date-input" disabled="disabled" style="height: 70%;" value={{$pdis->followupdate}}>
                                      </div>
                                     @endif
                                    @endforeach
                                    
                                  </div>
                               @endif
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(e) {
        var $inRadio = $("#formulario").find("input[type='radio']");

       // var $inRadio = $('input[name=radioName]:checked', '#formulario').val()



        var $inResultado = $("#formulario").find("#prom");
        var $inResultado1 = $("#formulario").find("#prom1");
        var $inResultado2 = $("#formulario").find("#prom2");
        var $valores = {};
        var $valores1 = {};
        var $valores2 = {};
        
        $inRadio.on("change", function(){
            
           // var $valor = +$(this).val();
            var $nombre = $(this).attr("name");

            
            if ($nombre == '1' || $nombre == '2' || $nombre == '3' || $nombre == '4' || $nombre == '5' || $nombre == '6' || $nombre == '7' || $nombre == '8' || $nombre == '9' || $nombre == '10') {
              var $valor = +$(this).val();
            $valores[""+ $nombre+ ""] = $valor;
            
            var $suma = 0;
            $.each($valores, function(indice, $valorArray){
               $suma =+ $suma + $valorArray; 
            });
            var cantidadrespuestas=$("#countresponses").val();
            if (cantidadrespuestas == '30') {
              $inResultado.val(($suma/10).toFixed(2));
            }else{
            $inResultado.val(($suma/7).toFixed(2));
            }
          }else if($nombre == '11' || $nombre == '12' || $nombre == '13' || $nombre == '14' || $nombre == '15' || $nombre == '16' || $nombre == '17' || $nombre == '18' || $nombre == '19' || $nombre == '20'){
            var $valor1 = +$(this).val();
            $valores1[""+ $nombre+ ""] = $valor1;
            
            var $suma1 = 0;
            
            $.each($valores1, function(indice1, $valorArray1){
               $suma1 =+ $suma1 + $valorArray1; 
            });
            var cantidadrespuestas=$("#countresponses").val();
            if (cantidadrespuestas == '30') {
              $inResultado1.val(($suma1/10).toFixed(2));
            }else{
            $inResultado1.val(($suma1/7).toFixed(2));
            }

          }else{
            var $valor2 = +$(this).val();
            $valores2[""+ $nombre+ ""] = $valor2;
            
            var $suma2 = 0;
            
            $.each($valores2, function(indice2, $valorArray2){
               $suma2 =+ $suma2 + $valorArray2; 
            });
            var cantidadrespuestas=$("#countresponses").val();
            if (cantidadrespuestas == '30') {
              $inResultado2.val(($suma2/10).toFixed(2));
            }else{
            $inResultado2.val(($suma2/7).toFixed(2));
            }
          }
        });

    });
 //$('#prom').val(sum);

</script>
@endsection