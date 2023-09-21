@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Directorio empleados</div>
            <form action="{{url('/reports/directoryfinder')}}" method="POST">
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
                <div class="form-group col-sm-3"><br>
                   <label for="user">Lider:</label>
                   <select class="form-control" id="leader" name="leader">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($leaders as $leader)
                      <option value="{{$leader->id}}">{{$leader->name}}</option>
                    @endforeach
                   </select>
                </div>
                <div class="form-group col-sm-3"><br>
                   <label for="profile">Ubicación:</label>
                   <select class="form-control" id="ubication" name="ubication">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($ubications as $ubication)
                      <option value="{{$ubication->ubication_name}}">{{$ubication->ubication_name}}</option>
                    @endforeach
                   </select>
                </div>
              </div>


              <button type="submit" class="btn btn-info" style="float: left; margin-right: : 1%;margin-bottom: 1%;">Buscar</button><br>
            </form><br>
            <img src="../img/excel.png" style="width: 7%; height: 7%; margin-left: 1%;" onclick="GenerarExcel();">

                <div class="card-body">
                    @csrf
                    <table class="table-responsive-md table-bordered table-striped table-sm" id="directorio">
                        <thead>
                            <tr>
                              <th>Nombre</th>
                              <th>Cargo</th>
                              <th>Nombre líder</th>
                              <th>Ubicación</th>
                              <th>Correo corporativo</th>
                              <th>Extensión</th>
                              <th>Celular corporativo</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($datas as $data)
                              <td>{{$data->name}}</td>
                              <td>{{$data->profile}}</td>
                              <td>{{$data->lider}}</td>
                              <td>{{$data->ubication}}</td>
                              <td>{{$data->email}}</td>
                              <td>{{$data->extension}}</td>
                              <td>{{$data->phone}}</td>
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
   $('#leader').select2();
   $('#ubication').select2();
});

function GenerarExcel(){
$(document).ready(function () {
    $("#directorio").table2excel({
        filename: "directorio.xls"
    });
});

}

</script>
@endsection