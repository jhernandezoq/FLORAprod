@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center ticket-area">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Mis casos') }}</div>
                <br>

                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-12">
                            <form action="{{url('ticket/search')}}" method="GET" enctype="multipart/form-data">
                            @csrf
                                <input type="hidden" name="user_type" value="user">
                          <div class="form-group">
                            <label for="TipoBusquedas">Seleccione el tipo de busqueda:</label>
                            <select class="form-control" id="TipoBusquedas" onchange="TipoBusqueda()" name="TipoBusquedas">
                              <option value="0" selected="selected" >Seleccione...</option>
                              <option value="1">Numero de caso</option>
                              <option value="2">Estado</option>
                              <option value="3">Palabra clave</option>
                            </select><br>
                            <div class="col-sm-3" style="margin-left: -1%;">
                                <a href="http://www.perezycardona.com/CIS/index.php?noAUTO=1" target="_blanck" class="btn btn-info">Nuevo</a>
                            </div>
                          </div>


                                <div class="form-row">
                                    <div class="form-group col-sm-9" style="display: none;" id="input_busqueda">
                                        <input type="text" class="form-control" id="text" name="text" placeholder="Ingresa el dato a buscar...">
                                    </div>
                                    <div class="form-group col-sm-9" style="display: none;" id="estado">
                                        <select class="form-control" name="state">
                                          <option value="0" selected="selected">Seleccione...</option>
                                          @foreach($states AS $state)
                                          <option value="{{$state->id}}">{{$state->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3" id="button" style="display: none;">
                                        <input type="submit" class="btn btn-success" name="buscar" value="Buscar">
                                    </div>
                                </div>
                            </form>
                    </div>
                  </div>
                  <br>
                  @if($count>0)
                    <div class="row">
                  @foreach($tickets as $ticket)
                     <div class="col-sm-3 ticket d-flex align-items-start flex-column" style="overflow-y: scroll;">
                            <div class="ticket-tool">{{$ticket->tool->name}}<span class="id orange"> [#{{$ticket->id}}]</span></div>
                            <div class="ticket-agent">{{Str::limit($ticket->agent->name,25)}}</div>
                            <div class="ticket-state">{{$ticket->state->name}}</div>
                            <div class="text text-justify">{{Str::limit($ticket->text,300)}}</div>
                      <div class="actions mt-auto">
                                <a href="{{ url('ticket') }}/{{ $ticket->id }}"><i class="far fa-edit" style="font-size: 30px;margin-right: 10px; color: white;" title="Detalle"></i></a>
                      </div>
                        </div>
                  @endforeach
                    </div>
                  @else
                  <strong><em><h5>No tienes casos aún</h5></em></strong>
                  @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
function TipoBusqueda(){
    var TipoBusqueda=$("#TipoBusquedas").val();
    if (TipoBusqueda == 0) {
     $("#input_busqueda").hide(1000);
     $("#estado").hide(1000);
     $("#button").hide(1000);  
    }else if(TipoBusqueda == 1){
     $("#input_busqueda").show(1000);
     $("#estado").hide(1000);
     $("#button").show(1000);     
    }else if(TipoBusqueda == 2){
     $("#input_busqueda").hide(1000);
     $("#estado").show(1000);
     $("#button").show(1000);  
    }else{
     $("#input_busqueda").show(1000);
     $("#estado").hide(1000);
     $("#button").show(1000);  
    }
}
</script>
@endsection
