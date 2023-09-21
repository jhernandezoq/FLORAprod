@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Edición de permisos</div>
            <form action="{{url('/report/permissionfinder')}}" method="POST">
              @csrf
              <div class="form-row">
                <div class="form-group col-sm-4"><br>
                   <label for="user">Usuario:</label>
                   <select class="form-control" id="user" name="user">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($users as $user)
                      <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                   </select>
                </div>
                <div class="form-group col-sm-4"><br>
                   <label for="profile">Cargo:</label>
                   <select class="form-control" id="profile" name="profile">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($profiles as $profile)
                      <option value="{{$profile->name}}">{{$profile->name}}</option>
                    @endforeach
                   </select>
                </div>
                <div class="form-group col-sm-4"><br>
                   <label for="profile">Función:</label>
                   <select class="form-control" id="function" name="function">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($functions as $function)
                      <option value="{{$function->id}}">{{$function->name}}</option>
                    @endforeach
                   </select>
                </div>
              </div>
              <button type="submit" class="btn btn-info" style="float: left; margin-right: : 1%;margin-bottom: 1%;">Buscar</button><br>
            </form>

                <div class="card-body">
                  <form action="{{url('/report/permissioneditfinder')}}" method="POST">
                    @csrf
                  <button type="submit" class="btn btn-success" style="float: right; margin-left: 5%;margin-bottom: 1%;">Guardar</button><br>
                    <table class="table-responsive-md table-bordered table-striped table-sm">
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
                              <input type="checkbox" id="{{$data->id}}" name="{{$data->id}}" value="{{$data->id}}" checked="checked">
                               @else
                               <input type="checkbox" id="{{$data->id}}" name="{{$data->id}}" value="{{$data->id}}">
                               @endif
                              </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                  </form>
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

});

</script>
@endsection