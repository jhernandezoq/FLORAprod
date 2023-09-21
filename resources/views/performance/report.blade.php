@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Informes valoración') }}</div>
                  <form action="{{url('performance/seeker')}}" method="POST">
                  @csrf
                   <div class="form-row">
                        <div class="form-group col-sm-3">
                           <label for="user">Usuario:</label>
                           <select class="form-control" name="user" id="user">
                              <option value='0'>Seleccione el usuario...</option>
                              @foreach($users as $user)
                              <option value="{{$user->id}}">{{$user->name}}</option>
                              @endforeach
                           </select>
                        </div> 



                        <div class="form-group col-sm-3">
                           <label for="ubication">Área:</label>
                           <select class="form-control" name="ubication" id="ubication">
                              <option value='0'>Seleccione el área...</option>
                              @foreach($ubications as $ubication)
                              <option value="{{$ubication->ubication_name}}">{{$ubication->ubication_name}}</option>
                              @endforeach
                          </select>
                        </div>
                     

                                            
                        <div class="form-group col-sm-3">
                           <label for="leaders">Jefe:</label>
                           <select class="form-control" name='leaders' id="leaders">
                              <option value='0'>Seleccione el lider...</option>
                              @foreach($leaders as $leader)
                              <option value="{{$leader->id}}">{{$leader->name}}</option>
                              @endforeach
                           </select>
                        </div>


                        
                        <div class="form-group col-sm-3">
                           <label for="year">Año:</label>
                           <select class="form-control" name="year" id="year">
                              <option value='0'>Seleccione el año...</option>
                              <option value="2021">2021</option>
                              <option value="2022">2022</option>
                              <option value="2023">2023</option>
                              <option value="2024">2024</option>
                              <option value="2025">2025</option>
                              <option value="2026">2026</option>
                              <option value="2027">2027</option>
                              <option value="2028">2028</option>
                              <option value="2029">2029</option>
                              <option value="2030">2030</option>
                           </select>
                        </div>


                     
                      </div>              

                     <button type="submit" class="btn btn-success" style="margin-top: 1%;">Buscar</button><br>

                  </form><br>
                  <img src="../img/excel.png" style="margin-left: 90%;" onclick="GenerarExcel();">
            <div style="margin-top: 3%;">
            <div class="card-body">
                <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%" id="performance_table">
                  <thead style="background-color: #90d3c1; color:white;">
                    <tr>
                      <th scope="col">Nombre</th>
                      <th scope="col">Evaludo por</th>
                      <th scope="col">Líder</th>
                      <th scope="col">Area</th>
                      <th scope="col">Año</th>
                      <th scope="col">Promedio Ser</th>
                      <th scope="col" style="display: none;">Objetivo Ser</th>
                      <th scope="col" style="display: none;">Accion Ser</th>
                      <th scope="col" style="display: none;">Fecha Ser</th>
                      <th scope="col">Promedio Saber</th>
                      <th scope="col" style="display: none;">Objetivo Saber</th>
                      <th scope="col" style="display: none;">Accion Saber</th>
                      <th scope="col" style="display: none;">Fecha Saber</th>
                      <th scope="col">Promedio Hacer</th>
                      <th scope="col" style="display: none;">Objetivo Hacer</th>
                      <th scope="col" style="display: none;">Accion Hacer</th>
                      <th scope="col" style="display: none;">Fecha Hacer</th>
                      <th scope="col">Promedio Total</th>
                    </tr>
                  </thead>
                 <tbody>
                    @foreach($data AS $datas)
                    <tr>
                      <th>{{$datas->name}}</th>
                      <td>{{$datas->jefe}}</td>
                      <td>{{$datas->leader}}</td>
                      <td>{{$datas->area}}</td>

                       @foreach($datapdiser AS $ser)
                        @if(($ser->id == $datas->id) && ($ser->year == $datas->year))
                           <td>{{$ser->year}}</td>
                           <td>{{$ser->average}}</td>
                           <td style="display: none;">{{$ser->objective}}</td>
                           <td style="display: none;">{{$ser->action}}</td>
                           <td style="display: none;">{{$ser->followupdate}}</td>
                        @endif
                       @endforeach
                       @foreach($datapdisaber AS $saber)
                        @if(($saber->id == $datas->id) && ($saber->year == $datas->year))
                           <td>{{$saber->average}}</td>
                           <td style="display: none;">{{$saber->objective}}</td>
                           <td style="display: none;">{{$saber->action}}</td>
                           <td style="display: none;">{{$saber->followupdate}}</td>
                        @endif
                       @endforeach
                       @foreach($datapdihacer AS $hacer)
                        @if(($hacer->id == $datas->id) && ($hacer->year == $datas->year))
                           <td>{{$hacer->average}}</td>
                           <td style="display: none;">{{$hacer->objective}}</td>
                           <td style="display: none;">{{$hacer->action}}</td>
                           <td style="display: none;">{{$hacer->followupdate}}</td>
                        @endif
                       @endforeach
                       @foreach($datapditotal AS $total)
                        @if(($total->id == $datas->id) && ($total->year == $datas->year))
                           <td>{{$total->average}}</td>
                        @endif
                       @endforeach
                    </tr>
                    @endforeach
                  </tbody>
                </table>
               </div> 
            </div>
        </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

  $(document).ready(function () {
   $('#user').select2();
   $('#leaders').select2();
   $('#ubication').select2();
   $('#year').select2();
});

function GenerarExcel(){
$(document).ready(function () {
    $("#performance_table").table2excel({
        filename: "valoración.xls"
    });
});

}


$('select').on('change', function() {
  alert( this.value );
});



</script>
@endsection
