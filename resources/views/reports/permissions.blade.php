@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Informe permisos</div>
                  <form action="{{url('/report/permissionfinder')}}" method="POST">
                  @csrf
                    <div class="form-row">
                      <div class="form-group col-sm-3"><br>
                        <label for="user">Usuario:</label>
                        <select class="form-control" id="user" name="user">
                          <option value="0" selected="selected">Seleccione...</option>
                          @foreach($users as $user)
                          <option value="{{$user->id}}">{{$user->name}}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="form-group col-sm-3"><br>
                        <label for="profile">Cargo:</label>
                        <select class="form-control" id="profile" name="profile">
                          <option value="0" selected="selected">Seleccione...</option>
                          @foreach($profiles as $profile)
                          <option value="{{$profile->profile_name}}">{{$profile->profile_name}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  <div class="form-row">
                    <div class="form-group col-sm-3"><br>
                      <label for="user">Aplicacion:</label>
                      <select class="form-control" id="aplication" name="aplication">
                        <option value="0" selected="selected">Seleccione...</option>
                        @foreach($applications as $application)
                        <option value="{{$application->id}}">{{$application->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-sm-3"><br>
                      <label for="profile">Modulo:</label>
                      <select class="form-control" id="module" name="module">
                        <option value="0" selected="selected">Seleccione...</option>
                        @foreach($modulesfinal as $modulefinal)
                        <option value="{{$modulefinal->id}}">{{$modulefinal->name}}</option>
                        @endforeach
                      </select>
                     </div>
                    <div class="form-group col-sm-3"><br>
                      <label for="profile">Función:</label>
                   <select class="form-control" id="function" name="function">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($functions as $function)
                      <option value="{{$function->id}}">{{$function->name}}</option>
                    @endforeach
                   </select>
                </div>
                <div class="form-group col-sm-3"><br>
                   <label for="profile">Estado:</label>
                   <select class="form-control" id="estado" name="estado">
                    <option value="Vacio" selected="selected">Seleccione...</option>
                      <option value="1">Activo</option>
                      <option value="0">Inactivo</option>
                   </select>
                </div>
              </div>


              <button type="submit" class="btn btn-info" style="float: left; margin-right: : 1%;margin-bottom: 1%;">Buscar</button><br>
            </form><br>
            <img src="../img/excel.png" style="width: 7%; height: 7%; margin-left: 1%;" onclick="GenerarExcel();">

                <div class="card-body">
                    @csrf
                    <table class="table-responsive-md table-bordered table-striped table-sm" id="permisos">
                        <thead>
                            <tr>
                              <th>Nombre</th>
                              <th>Cargo</th>
                              <th>Aplicación</th>
                              <th>Módulo</th>
                              <th>Función</th>
                              <th>Ruta</th>
                              <th>Estado</th>
                              <th>Editar</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($datas as $data)
                              <td>{{$data->name}}</td>
                              <td>{{$data->cargo}}</td>
                              <td>{{$data->aplication}}</td>
                              <td>{{$data->module}}</td>
                              <td>{{$data->functions}}</td>
                              <td>{{$data->route}}</td>
                              <td>{{$data->Estado}}</td>
                              <td>
                                @if($data->Estado == 'Activo')
                              <form action="{{url('/report/permissioneditfinder')}}" method="POST">
                                @csrf
                              <input type="text" name="idpermission" value="{{$data->id}}" style="display: none;">
                              <input type="text" name="state" value="0" style="display: none;">
                              <button type="submit" class="btn btn-success">Inactivar</button>
                              </form>
                               @else
                               <form action="{{url('/report/permissioneditfinder')}}" method="POST">
                                @csrf
                                <input type="text" name="idpermission" value="{{$data->id}}" style="display: none;">
                                <input type="text" name="state" value="1" style="display: none;">
                               <button type="submit" class="btn btn-success">Activar</button>
                               </form>
                               @endif
                              </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function () {

   $('#user').select2();
   $('#profile').select2();
   $('#function').select2();
   $('#aplication').select2();
   $('#module').select2();
   $('#estado').select2();



});

function GenerarExcel(){
$(document).ready(function () {
    $("#permisos").table2excel({
        filename: "permisos.xls"
    });
});

}


</script>
@endsection