@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Información adicional empleados</div>
            <form action="{{url('/reports/usersfinder')}}" method="POST">
              @csrf
              <div class="form-row">
                <div class="form-group col-sm-12"><br>
                   <label for="user">Nombre:</label>
                   <select class="form-control" id="user" name="user">
                    <option value="0" selected="selected">Seleccione...</option>
                     @foreach($users AS $user)
                      <option value="{{$user->cedula}}">{{$user->name}}</option>
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
                              <th>Cédula</th>
                              <th>Cargo</th>
                              <th>Ubicación</th>
                              <th>Vehículo</th>
                              <th>Modelo vehículo</th>
                              <th>Cantidad mascotas</th>
                              <th>Especie mascotas</th>
                              <th>Nombres mascotas</th>
                              <th>Tipo vivienda</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                          @foreach($users AS $user)
                            <td>{{$user->name}}</td>
                            <td>{{$user->cedula}}</td>
                            <td>{{$user->cargo}}</td>
                            <td>{{$user->ubicacion}}</td>
                            <td>{{$user->vehiculo}}</td>
                            <td>{{$user->modelo}}</td>
                            <td>{{$user->cantidad_mascotas}}</td>
                            <td>{{$user->especie_mascotas}}</td>
                            <td>{{$user->nombre_mascotas}}</td>
                            <td>{{$user->tipo_vivienda}}</td>                            
                          </tr>
                          @endforeach                         
                        </tbody>
                    </table>


                    <table class="table-responsive-md table-bordered table-striped table-sm" id="directorio_total" style="display: none;">
                        <thead>
                            <tr>
                              <th>Nombre</th>
                              <th>Cédula</th>
                              <th>Cargo</th>
                              <th>Ubicación</th>
                              <th>Email</th>                              
                              <th>Genero</th>
                              <th>Telefono fijo</th>
                              <th>Celular personal</th>
                              <th>Dirección residencia</th>
                              <th>Tipo vivienda</th>
                              <th>Barrio</th>
                              <th>Municipio</th>
                              <th>Nombre unidad</th>
                              <th>Estado civil</th>
                              <th>Nivel estudios</th>
                              <th>Formacion actual</th>
                              <th>Estudia actualmente</th>
                              <th>Estudio actual</th>
                              <th>Eps</th>
                              <th>Fondo de pensiones</th>
                              <th>Fondo de cesantias</th>
                              <th>Grupo de trabajo</th>
                              <th>Sede labora</th>
                              <th>Conyuge</th>
                              <th>Grupo sanguineo</th>                             
                              <th>Contacto emergencia</th>
                              <th>Parentesco contacto</th>
                              <th>Celular contacto</th>
                              <th>Tipo de vehículo</th>
                              <th>Marca</th>
                              <th>Modelo vehículo</th>
                              <th>Placa</th>
                              <th>Experiencia conduccion</th>
                              <th>Categoria licencia</th>
                              <th>Vencimiento licencia</th>
                              <th>Cantidad mascotas</th>
                              <th>Especie mascotas</th>
                              <th>Nombres mascotas</th>
                              <th>Posee hijos</th>
                              <th>Cantidad hijos</th>
                           			<td>Nombre hijo 1</td>
                            		<td>Tipo de documento hijo 1</td>
                            	 	<td>Numero de documento hijo 1</td>
                            	 	<td>fecha de nacimiento hijo 1</td>
                            	 	<td>Nombre hijo 2</td>
                            		<td>Tipo de documento hijo 2</td>
                            	 	<td>Numero de documento hijo 2</td>
                            	 	<td>fecha de nacimiento hijo 2</td>
                              		<td>Nombre hijo 3</td>
                            		<td>Tipo de documento hijo 3</td>
                            	 	<td>Numero de documento hijo 3</td>
                            	 	<td>fecha de nacimiento hijo 3</td>
                              		<td>Nombre hijo 4</td>
                            		<td>Tipo de documento hijo 4</td>
                            	 	<td>Numero de documento hijo 4</td>
                            	 	<td>fecha de nacimiento hijo 4</td>                                             
                            </tr>
                        </thead>
                        
                        <tbody>
                          @foreach($users AS $user)
                            <td>{{$user->name}}</td>
                            <td>{{$user->cedula}}</td>
                            <td>{{$user->cargo}}</td>
                            <td>{{$user->ubicacion}}</td> 
                            <td>{{$user->email}}</td>                          
                            <td>{{$user->gender}}</td>
                            <td>{{$user->telefono_fijo}}</td>
                            <td>{{$user->celular_personal}}</td>
                            <td>{{$user->direccion_residencia}}</td>
                            <td>{{$user->tipo_vivienda}}</td>
                            <td>{{$user->barrio}}</td>
                            <td>{{$user->ciudad}}</td>
                            <td>{{$user->nombre_unidad}}</td>
                            <td>{{$user->estado_civil}}</td>
                            <td>{{$user->nivel_estudios}}</td>
                            <td>{{$user->formacion_actual}}</td>
                            <td>{{$user->estudia_actualmente}}</td>
                            <td>{{$user->estudio_actual}}</td>
                            <td>{{$user->eps}}</td>
                            <td>{{$user->fondo_pensiones}}</td>
                            <td>{{$user->fondo_cesantias}}</td>
                            <td>{{$user->grupo_trabajo}}</td>
                            <td>{{$user->sede_labora}}</td>
                            <td>{{$user->conyuge}}</td>
                            <td>{{$user->grupo_sanguineo}}</td>
                            <td>{{$user->contacto_emergencia}}</td>
                            <td>{{$user->parentesco_contacto_emergencia}}</td>
                            <td>{{$user->celular_contacto_emergencia}}</td>
                            <td>{{$user->tipo_vehiculo}}</td>
                            <td>{{$user->vehiculo}}</td>
                            <td>{{$user->modelo}}</td>
                            <td>{{$user->placa_vehiculo}}</td>
                            <td>{{$user->experiencia_conduccion}}</td>
                            <td>{{$user->categoria_licencia}}</td>
                            <td>{{$user->vencimiento_licencia}}</td>
                            <td>{{$user->cantidad_mascotas}}</td>
                            <td>{{$user->especie_mascotas}}</td>
                            <td>{{$user->nombre_mascotas}}</td>
                            <td>{{$user->posee_hijos}}</td>
                            <td>{{$user->cantidad_hijos}}</td>

                             @foreach($hijos AS $hijo)
                                @if($hijo->id_user == $user->id)
                            	 <td>{{$hijo->nombre_hijo}}</td>
                            	 <td>{{$hijo->tipo_documento_hijo}}</td>
                            	 <td>{{$hijo->numero_documento_hijo}}</td>
                            	 <td>{{$hijo->fecha_nacimiento_hijo}}</td>                            	
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
</div>
@endsection

@section('scripts')
<script type="text/javascript">

  $(document).ready(function () {
   $('#user').select2();
   $('#cedula').select2();
   $('#profile').select2();
   $('#leader').select2();
   $('#ubication').select2();
});

function GenerarExcel(){
$(document).ready(function () {
    $("#directorio_total").table2excel({
        filename: "informe_usuarios.xls"
    });
});

}

</script>
@endsection