@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Actualización de datos') }}</div>
            </div>
        </div>
    </div>
</div><br>

<div class="container">
<div class="row justify-content-center">
<div class="col-md-12">
    <div class="card">

   <div class="card-body">
<i><h4>Este formulario es realizado por el área de Gestión Humana para la actualización de los datos de los colaboradores.
Agradecemos tu disposición y tiempo invertido.</h4></i>
 <br>

                     @if(count($errors) > 0)
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                  <span>Recuerda:</span>
                                  <li>Si el promedio de alguna de las dimensiones es menor a 4.5, es obligatorio completar el PDI correspondiente a esa dimensión</li>
                                  <li>Cuando llenes un PDI es obligatorio llenar la información del objetivo, la acción y la fecha de compromiso</li>
                            </ul>
                        </div>
                    @endif
        <form action="{{url('/updateuserdata')}}" method="POST" enctype="multipart/form-data">
            @csrf

                <div style="margin-top: 3%;"><h3>INFORMACIÓN MASCOTAS:</h3></div><br>
                 <div class="form-row">
                        <div class="form-group col-sm-12">
                           <label for="mascotas">Posee Mascotas:</label>
                           <select class="form-control" id="mascotas" name="mascotas" onchange="SeleccionMascotas();">
                             @if($user->mascotas == 'Si')
                              <option value="Si">Si</option>
                             @else
                             <option value="No">No</option>
                             @endif
                              <option value="No">No</option>
                              <option value="Si">Si</option>
                           </select>
                        </div>
                   @if($user->mascotas == 'Si')
                        <div class="form-group col-sm-12" id="div_cantidad_mascotas">
                           <label for="cantidad_mascotas">Cúantas??:</label>
                            <input type="number" class="form-control" id="cantidad_mascotas" name="cantidad_mascotas" placeholder="" value="{{$user->cantidad_mascotas}}">
                        </div>
                        <div class="form-group col-sm-12"  id="div_especies">
                           <label for="especie_mascotas">Especie(s):</label>
                            <input type="text" class="form-control" id="especies_mascotas" name="especies_mascotas" placeholder="" value="{{$user->especie_mascotas}}">
                        </div>
                        <div class="form-group col-sm-12"  id="div_nombre_mascotas">
                           <label for="nombres_mascotas">Nombre de la(s) mascota(s):</label>
                            <input type="text" class="form-control" id="nombres_mascotas" name="nombres_mascotas" placeholder="" value="{{$user->nombre_mascotas}}">
                        </div><br>
                    @else
                        <div class="form-group col-sm-12" style="display: none;" id="div_cantidad_mascotas">
                           <label for="cantidad_mascotas">Cúantas??:</label>
                            <input type="number" class="form-control" id="cantidad_mascotas" name="cantidad_mascotas" placeholder="">
                        </div>
                        <div class="form-group col-sm-12" style="display: none;" id="div_especies">
                           <label for="especie_mascotas">Especie(s):</label>
                            <input type="text" class="form-control" id="especies_mascotas" name="especies_mascotas" placeholder="">
                        </div>

                        <div class="form-group col-sm-12" style="display: none;" id="div_nombre_mascotas">
                           <label for="nombres_mascotas">Nombre de la(s) mascota(s):</label>
                            <input type="text" class="form-control" id="nombres_mascotas" name="nombres_mascotas" placeholder="">
                        </div><br>
                    @endif
                

            <div style="margin-top: 3%;"><h3>DATOS PERSONALES:</h3></div><br>
            <div class="form-row">
                <br>
                <div class="form-group col-sm-4">
                    <label for="correo">Teléfono fijo:</label>
                    <input type="text" class="form-control" id="telefono_fijo" name="telefono_fijo" placeholder="" onkeypress="return valideKey(event);" value="{{$user->telefono_fijo}}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="correo">Celular personal:</label>
                    <input type="text" class="form-control" id="celular_personal" name="celular_personal" placeholder="" onkeypress="return valideKey(event);" required="required" value="{{$user->celular_personal}}">
                </div><br>
                <div class="form-group col-sm-4">
                    <label for="extension">Dirección residencia:</label>
                    <input type="text" class="form-control" id="direccion_residencia" name="direccion_residencia" placeholder="" required="required" value="{{$user->direccion_residencia}}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Barrio:</label>
                    <input type="text" class="form-control" id="barrio" name="barrio" placeholder="" required="required" value="{{$user->barrio}}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Nombre Unidad/Urbanización/Condominio:</label>
                    <input type="text" class="form-control" id="nombre_unidad" name="nombre_unidad" placeholder="" onkeypress="return VerificarApto(event);" value="{{$user->nombre_unidad}}">
                </div><br>
                <div class="form-group col-sm-4" style="display: none;" id="div_apto">
                    <label for="extension">Número Casa/Apartamento:</label>
                    <input type="text" class="form-control" id="apto" name="apto" placeholder="" value="{{$user->apto}}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Ciudad residencia:</label>
                    <input type="text" class="form-control" id="ciudad" name="ciudad" placeholder="" required="required" value="{{$user->ciudad}}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="celular">Estado civil:</label>
                      <select class="form-control" id="estado_civil" name="estado_civil">
                        <option>{{$user->estado_civil}}</option>
                        <option>Casado</option>
                        <option>Soltero</option>
                        <option>Unión libre</option>
                        <option>Viudo</option>
                        <option>Divorciado/Separado</option>
                      </select>
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Correo electrónico personal:</label>
                    <input type="text" class="form-control" id="correo_personal" name="correo_personal" placeholder="" required="required" value="{{$user->email_aux}}">
                </div><br>
                <div class="form-group col-sm-4">
                    <label for="celular">Tipo vivienda:</label>
                      <select class="form-control" id="tipo_vivienda" name="tipo_vivienda" onchange="Tipo_vivienda();">
                        <option>{{$user->tipo_vivienda}}</option>
                        <option>Arrendada</option>
                        <option>Propia</option>
                        <option>Familiar</option>
                        <option>Otro</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_tipo_vivienda_otro" style="display: none;">
                    <label for="extension">Cúal?:</label>
                    <input type="text" class="form-control" id="tipo_vivienda_otro" name="tipo_vivienda_otro" placeholder="">
                </div>
                <div class="form-group col-sm-4">
                    <label for="celular">Estrato:</label>
                      <select class="form-control" id="estrato" name="estrato">
                        <option>{{$user->estrato}}</option>
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option>6</option>
                      </select>
                </div>
                <div class="form-group col-sm-4">
                    <label for="celular">Grupo sanguíneo:</label>
                      <select class="form-control" id="grupo_sanguineo" name="grupo_sanguineo">
                        <option>{{$user->grupo_sanguineo}}</option>
                        <option>O-</option>
                        <option>O+</option>
                        <option>A-</option>
                        <option>A+</option>
                        <option>B-</option>
                        <option>B+</option>
                        <option>AB-</option>
                        <option>AB+</option>
                      </select>
                </div>
                <div class="form-group col-sm-4">
                    <label for="celular">Ya te vacunaste contra el covid-19?:</label>
                      <select class="form-control" id="vacuna" name="vacuna" onchange="Vacuna();">
                        <option>{{$user->vacunado_covid}}</option>
                        <option>Si</option>
                        <option>No</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" style="display: none;" id="div_razon_vacuna">
                    <label for="extension">Por que?:</label>
                    <input type="text" class="form-control" id="razon_vacuna" name="razon_vacuna" placeholder="">
                </div><br>
                <div class="form-group col-sm-4">
                    <label for="celular">Género:</label>
                    <div class="form-group">
                      <select class="form-control" id="genero" name="genero">
                        <option>{{$user->gender}}</option>
                        <option>Masculino</option>
                        <option>Femenino</option>
                      </select>
                    </div>
                </div>
                <br>
                <br>
                <div class="form-group col-sm-12" style="margin-top: 3%;"><h3>DATOS FAMILIARES:</h3></div><br>
                <div class="form-group col-sm-4">
                    <label for="extension">Nombre del cónyuge o compañero(a):</label>
                    <input type="text" class="form-control" id="conyuge" name="conyuge" placeholder="" value="{{$user->conyuge}}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="celular">Tiene hijos:</label>
                      <select required="required" class="form-control" id="posee_hijos" name="posee_hijos" onchange="PoseeHijos();">
                        <option>{{$user->posee_hijos}}</option>
                        <option>No</option>
                        <option>Si</option>
                      </select>
                </div>
                @if($user->posee_hijos == 'Si')
                <div class="form-group col-sm-4" id="CantidadHijos">
                    <label for="extension">Cúantos hijos tiene:</label>
                    <input type="text" class="form-control" id="cantidad_hijos" name="cantidad_hijos" placeholder="" onkeypress="return valideKey(event);" onkeyup="MensajeDatosHijos();" value="{{$user->cantidad_hijos}}">
                </div>
                @else
                <div class="form-group col-sm-4" style="display: none;" id="CantidadHijos">
                    <label for="extension">Cúantos hijos tiene:</label>
                    <input type="text" class="form-control" id="cantidad_hijos" name="cantidad_hijos" placeholder="" onkeypress="return valideKey(event);" onkeyup="MensajeDatosHijos();">
                </div>
                @endif
                <br>
                <img src="img/agregaradjunto.png" alt="Agregar hijos" width="50" height="50" style="display: none; margin-top: 2%; margin-left: 2%; margin-right: 90%;" id="img_hijos" onclick="AgregarInformacionHijos();" {{ Popper::pop('Da click un número de veces igual a la cantidad de hijos que tienes') }}><br><br><br>
                <input type="text" name="countfieldsadd"  id="countfieldsadd" width="10px;" value="1" style="display: none;">
                <br><div id="InformacionHijos"></div><br><br>

                <br><div class="form-group col-sm-4">
                    <label for="extension">Nombre de contacto en caso de emergencia:</label>
                    <input type="text" class="form-control" id="contacto_emergencia" name="contacto_emergencia" placeholder="" required="required" value="{{$user->contacto_emergencia}}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Parentesco contacto de emergencia:</label>
                      <select class="form-control" id="parentesco_contacto_emergencia" name="parentesco_contacto_emergencia" onchange="OtroFamiliar();">
                        <option>{{$user->parentesco_contacto_emergencia}}</option>
                        <option>Madre</option>
                        <option>Padre</option>
                        <option>Hermano(a)</option>
                        <option>Tio(a)</option>
                        <option>Amigo(a)</option>
                        <option>Esposo(a)/Cónyuge/Compañero(a)</option>
                        <option>Otro</option>
                      </select>
                </div>
                <br><div class="form-group col-sm-4" style="display: none;" id="div_otro_familar">
                    <label for="extension">Cúal?:</label>
                    <input type="text" class="form-control" id="otro_familiar" name="otro_familiar" placeholder="">
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Número de contacto en caso de emergencia:</label>
                    <input type="text" class="form-control" id="celular_contacto_emergencia" name="celular_contacto_emergencia" placeholder="" required="required" onkeypress="return valideKey(event);" value="{{$user->celular_contacto_emergencia}}">
                </div><br><br>
                <div class="form-group col-sm-12" style="margin-top: 3%;"><h3>INFORMACIÓN ESTUDIOS:</h3></div><br>
                <div class="form-group col-sm-4">
                    <label for="extension">Nivel de estudios:</label>
                      <select class="form-control" id="nivel_estudios" name="nivel_estudios">
                        <option>{{$user->nivel_estudios}}</option>
                        <option>No Bachiller</option>
                        <option>Bachiller</option>
                        <option>Técnica</option>
                        <option>Tecnológica</option>
                        <option>Profesional</option>
                        <option>Especialización</option>
                        <option>Maestría</option>
                      </select>
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Indique su formación actual:</label>
                    <input type="text" class="form-control" id="formacion_actual" name="formacion_actual" placeholder="" required="required" value="{{$user->formacion_actual}}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Estudia actualmente:</label>
                      <select class="form-control" id="EstudiaActualmente" name="EstudiaActualmente" onchange="Estudia_actualmente();">
                        <option>{{$user->estudia_actualmente}}</option>
                        <option>No</option>
                        <option>Si</option>
                      </select>
                </div>
                @if($user->estudia_actualmente == 'Si')
                <div class="form-group col-sm-4" id="div_estudia_actualmente">
                    <label for="extension">Qué estudia actualmente:</label>
                    <input type="text" class="form-control" id="estudio_actual" name="estudio_actual" placeholder="" value="{{$user->estudio_actual}}">
                </div><br>
                @else
                <div class="form-group col-sm-4" id="div_estudia_actualmente" style="display: none;">
                    <label for="extension">Qué estudia actualmente:</label>
                    <input type="text" class="form-control" id="estudio_actual" name="estudio_actual" placeholder="">
                </div><br>
                @endif
                <div class="form-group col-sm-12" style="margin-top: 3%;"><h3>DATOS LABORALES:</h3></div><br>

                <div class="form-group col-sm-4">
                    <label for="extension">Compañia:</label>
                      <select class="form-control" id="Empresa" name="Empresa">
                        <option>{{$user->empresa}}</option>
                        <option>Perez y Cardona S.A.S</option>
                        <option>MP Galagro</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_estudia_actualmente">
                    <label for="extension">EPS:</label>
                    <input type="text" class="form-control" id="eps" name="eps" placeholder="" required="required" value="{{$user->eps}}">
                </div>
                <div class="form-group col-sm-4" id="div_estudia_actualmente">
                    <label for="extension">Fondo de pensiones:</label>
                    <input type="text" class="form-control" id="fondo_pensiones" name="fondo_pensiones" placeholder="" required="required" value="{{$user->fondo_pensiones}}">
                </div>
                <div class="form-group col-sm-4" id="div_estudia_actualmente">
                    <label for="extension">Fondo de cesantías:</label>
                    <input type="text" class="form-control" id="fondo_cesantias" name="fondo_cesantias" placeholder="" required="required" value="{{$user->fondo_pensiones}}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Grupo de trabajo al que pertenece:</label>
                      <select class="form-control" id="grupo_trabajo" name="grupo_trabajo">
                        <option>{{$user->grupo_trabajo}}</option>
                        <option>Administrativo</option>
                        <option>Comercial</option>
                        <option>Operativo</option>
                        <option>Técnico</option>
                        <option>Conductor</option>
                      </select>
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Sede donde labora:</label>
                      <select class="form-control" id="sede_labora" name="sede_labora" onchange="OtraSede();">
                        <option>{{$user->sede_labora}}</option>
                        <option>MEDELLIN</option>
                        <option >ITAGUI</option>
                        <option>BELLO</option>
                        <option>PALMAS</option>
                        <option>SABANETA</option>
                        <option>LA UNION</option>
                        <option>SAN PEDRO</option>
                        <option>OTRO</option>
                      </select>
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Correo corporativo:</label>
                       <input type="text" class="form-control" id="correo_corporativo" name="correo_corporativo" placeholder="" value="{{$user->email}}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Celular corporativo:</label>
                       <input type="text" class="form-control" id="celular_corporativo" name="celular_corporativo" placeholder="" value="{{$user->phone}}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="extension">Extensión:</label>
                       <input type="text" class="form-control" id="extension" name="extension" placeholder="" value="{{$user->extension}}">
                </div>
                <div class="form-group col-sm-4" id="div_otra_sede" style="display: none;">
                    <label for="extension">Cúal?:</label>
                    <input type="text" class="form-control" id="otra_sede" name="otra_sede" placeholder="">
                </div>
                <br>
                <div class="form-group col-sm-12" style="margin-top: 3%;"><h3>DATOS PLAN ESTRATÉGICO DE MOVILIDAD:</h3></div><br>
                <div class="form-group col-sm-4">
                           <label for="vehiculo">Posee Vehículo:</label>
                           <select class="form-control" id="vehiculo" name="vehiculo" onchange="SeleccionVehiculo();">
                              <option>{{$user->vehiculo}}</option>
                              <option value="No">No</option>
                              <option value="Si">Si</option>
                           </select>
                        </div>
                </div>
                @if($user->vehiculo == 'Si')
                <div class="form-group col-sm-4" id="div_experiencia_conduccion">
                    <label for="extension">Experiencia de conducción en años:</label>
                      <select class="form-control" id="experiencia_conduccion" name="experiencia_conduccion">
                        <option>{{$user->experiencia_conduccion}}</option>
                        <option>1-5</option>
                        <option>6-10</option>
                        <option>11-15</option>
                        <option>16-21</option>
                        <option>más de 22</option>
                        <option>Ninguna</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_tipo_vehiculo">
                    <label for="extension">Tipo de vehículo:</label>
                      <select class="form-control" id="tipo_vehiculo" name="tipo_vehiculo">
                        <option>{{$user->tipo_vehiculo}}</option>
                        <option>Moto</option>
                        <option>Carro</option>
                        <option>Ninguno</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_categoria_licencia">
                    <label for="extension">Categoría licencia de conducción:</label>
                       <input type="text" class="form-control" id="categoria_licencia" name="categoria_licencia" placeholder="" value="{{$user->categoria_licencia}}">
                </div>
                <div class="form-group col-sm-4" id="div_fecha_licencia">
                <label for="extension">Fecha de vencimiento licencia:</label>
                <input type='date' class='form-control' id='vencimiento_licencia' name='vencimiento_licencia' value="{{$user->vencimiento_licencia}}">
                </div>
                <div class="form-group col-sm-4" id="div_placa_vehiculo">
                    <label for="extension">Placa vehículo:</label>
                       <input type='text' class='form-control' id='placa_vehiculo' name='placa_vehiculo' style='' value="{{$user->placa_vehiculo}}">
                </div>
                <div class="form-group col-sm-4" id="div_marca">
                           <label for="vehiculo_marca">Marca:</label>
                            <input type="text" class="form-control" id="vehiculo_marca" name="vehiculo_marca" placeholder="" value="{{$user->vehiculo_marca}}">
                </div>
                <div class="form-group col-sm-12" id="div_modelo">
                           <label for="vehiculo_mmodelo">Modelo:</label>
                            <input type="text" class="form-control" id="vehiculo_modelo" name="vehiculo_modelo" placeholder="" value="{{$user->vehiculo_modelo}}">
                </div>
                @else
                <div class="form-group col-sm-4" id="div_experiencia_conduccion" style="display: none;">
                    <label for="extension">Experiencia de conducción en años:</label>
                      <select class="form-control" id="experiencia_conduccion" name="experiencia_conduccion">
                        <option>1-5</option>
                        <option>6-10</option>
                        <option>11-15</option>
                        <option>16-21</option>
                        <option>más de 22</option>
                        <option>Ninguna</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_tipo_vehiculo" style="display: none;">
                    <label for="extension">Tipo de vehículo:</label>
                      <select class="form-control" id="tipo_vehiculo" name="tipo_vehiculo">
                        <option>Moto</option>
                        <option>Carro</option>
                        <option>Ninguno</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_categoria_licencia" style="display: none;">
                    <label for="extension">Categoría licencia de conducción:</label>
                       <input type="text" class="form-control" id="categoria_licencia" name="categoria_licencia" placeholder="">
                </div>
                <div class="form-group col-sm-4" id="div_fecha_licencia" style="display: none;">
                <label for="extension">Fecha de vencimiento licencia:</label>
                <input type='date' class='form-control' id='vencimiento_licencia' name='vencimiento_licencia'>
                </div>
                <div class="form-group col-sm-4" id="div_placa_vehiculo" style="display: none;">
                    <label for="extension">Placa vehículo:</label>
                       <input type='text' class='form-control' id='placa_vehiculo' name='placa_vehiculo' style=''>
                </div>
                <div class="form-group col-sm-4" style="display: none;" id="div_marca">
                           <label for="vehiculo_marca">Marca:</label>
                            <input type="text" class="form-control" id="vehiculo_marca" name="vehiculo_marca" placeholder="">
                </div>
                <div class="form-group col-sm-12" style="display: none;" id="div_modelo">
                           <label for="vehiculo_mmodelo">Modelo:</label>
                            <input type="text" class="form-control" id="vehiculo_modelo" name="vehiculo_modelo" placeholder="">
                </div>
                @endif
                <div class="form-group col-sm-4" id="div_experiencia_conduccion">
                    <label for="extension">Medio de transporte que utiliza para los trayectos Casa-Trabajo:</label>
                      <select class="form-control" id="medio_transporte" name="medio_transporte">
                        <option>{{$user->medio_transporte}}</option>
                        <option>A pie</option>
                        <option>Bicicleta</option>
                        <option>Moto</option>
                        <option>Carro</option>
                        <option>Transporte público</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_experiencia_conduccion">
                    <label for="extension">Tiempo promedio diario de trayecto en horas (ida y vuelta):</label>
                      <select class="form-control" id="tiempo_promedio_trayecto" name="tiempo_promedio_trayecto">
                        <option>{{$user->tiempo_promedio_trayecto}}</option>
                        <option>1</option>
                        <option>2-3</option>
                        <option>4-5</option>
                        <option>6-7</option>
                        <option>Mas de 8</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_experiencia_conduccion">
                    <label for="extension">Ha tenido accidentes de tránsito:</label><br><br>
                      <select class="form-control" id="accidentes_transito" name="accidentes_transito">
                        <option>{{$user->accidentes_transito}}</option>
                        <option>No</option>
                        <option>Si</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_experiencia_conduccion">
                    <label for="extension">Tipo de accidente más grave que he tenido:</label>
                      <select class="form-control" id="accidente_grave" name="accidente_grave">
                        <option>{{$user->accidentes_grave}}</option>
                        <option>Solo daños</option>
                        <option>Con lesionados</option>
                        <option>Ninguno</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_experiencia_conduccion">
                    <label for="extension">Te encuentras trabajando en:</label>
                      <select class="form-control" id="tipo_trabajo" name="tipo_trabajo">
                        <option>{{$user->tipo_trabajo}}</option>
                        <option>Casa</option>
                        <option>Oficina</option>
                        <option>Alternancia</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_experiencia_conduccion">
                    <label for="extension">Factores de Riesgo que más percibe en sus desplazamientos por la vía pública (HUMANOS) :</label>
                      <select class="form-control" id="factores_riesgo_humanos" name="factores_riesgo_humanos">
                        <option>{{$user->factores_riesgo_humanos}}</option>
                        <option>Distracción</option>
                        <option>Sueño/Fatiga</option>
                        <option>Intolerancia</option>
                        <option>Velocidad</option>
                        <option>Uso de alcohol o drogas</option>
                        <option>No respetar las señales</option>
                        <option>Ninguno</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_experiencia_conduccion">
                    <label for="extension">Factores de Riesgo que más percibe en sus desplazamientos por la vía pública (VÍA Y ENTORNO):</label>
                      <select class="form-control" id="factores_riesgo_entorno" name="factores_riesgo_entorno">
                        <option>{{$user->factores_riesgo_entorno}}</option>
                        <option>Mal estado de la vía</option>
                        <option>Ausencia de señales</option>
                        <option>Clima</option>
                        <option>Ninguno</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_experiencia_conduccion">
                    <label for="extension">Factores de Riesgo que más percibe en sus desplazamientos por la vía pública (VEHÍCULO):</label>
                      <select class="form-control" id="factores_riesgo_vehiculo" name="factores_riesgo_vehiculo">
                        <option>{{$user->factores_riesgo_vehiculo}}</option>
                        <option>Mal estado de la vía</option>
                        <option>Ausencia de señales</option>
                        <option>Clima</option>
                        <option>Ninguno</option>
                      </select>
                </div>
                <div class="form-group col-sm-4" id="div_experiencia_conduccion">
                    <label for="extension">Propuesta de mejoramiento internas para el plan estratégico de movilidad</label>
                      <select class="form-control" id="propuestas_internas" name="propuestas_internas">
                        <option>{{$user->propuestas_internas}}</option>
                        <option>Educación vial para los empleados</option>
                        <option>Establecer estándares de seguridad vial</option>
                        <option>Procesos disciplinarios por incumplimiento de normas de seguridad vial</option>
                        <option>Seguimiento interno a la siniestralidad</option>
                        <option>Seguimiento programas de mantenimiento vehicular</option>
                        <option>Campañas de seguridad vial</option>
                        <option>Ninguna</option>
                      </select>
                </div>
                <div class="form-group col-sm-12" style="margin-left: 45%;">
                   <button type="submit" class="btn btn-success" id="send">Actualizar</button>
                </div>
            <div>
        </form>
    </div>
</div>
</div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">

$(document).ready(function() {
    $('#send').click(function(){
        if($("#email").val().indexOf('@', 0) == -1 || $("#email").val().indexOf('.', 0) == -1) {
            alert('El correo electrónico que ingresaste no es correcto.');
            return false;
        }

        alert('El correo electrónico que ingresaste es correcto.');
    });
});


function valideKey(evt){
    // code is the decimal ASCII representation of the pressed key.
    var code = (evt.which) ? evt.which : evt.keyCode;
    
    if(code==8) { // backspace.
      return true;
    } else if(code>=48 && code<=57) { // is a number.
      return true;
    } else{ // other keys.
      return false;
    }
}


function SeleccionVehiculo(){
  var vehiculo = $("#vehiculo").val();
 if (vehiculo == 'Si') {
    $("#div_marca").show('slow');
    $("#div_modelo").show('slow');
    $('#vehiculo_marca').prop("required", true);
    $('#vehiculo_modelo').prop("required", true);
    $('#div_experiencia_conduccion').show('slow');
    $('#div_tipo_vehiculo').show('slow');
    $('#div_categoria_licencia').show('slow');
    $('#div_fecha_licencia').show('slow');
    $('#div_placa_vehiculo').show('slow');
 }else{
    $('#vehiculo_marca').removeAttr("required");
    $('#vehiculo_modelo').removeAttr("required");
    $("#vehiculo_marca").val('');
    $("#vehiculo_modelo").val('');
    $("#div_marca").hide('slow');
    $("#div_modelo").hide('slow');
    $('#div_experiencia_conduccion').hide('slow');
    $('#div_tipo_vehiculo').hide('slow');
    $('#div_categoria_licencia').hide('slow');
    $('#div_fecha_licencia').hide('slow');
    $('#div_placa_vehiculo').hide('slow');
 }

}



function SeleccionMascotas(){
  var mascotas = $("#mascotas").val();
 if (mascotas == 'Si') {
    $("#div_nombre_mascotas").show('slow');
    $("#div_cantidad_mascotas").show('slow');
    $("#div_especies").show('slow');
    $('#cantidad_mascotas').prop("required", true);
    $('#especies_mascotas').prop("required", true);
    $('#nombres_mascotas').prop("required", true);
 }else{
    $('#cantidad_mascotas').removeAttr("required");
    $('#especies_mascotas').removeAttr("required");
    $('#nombres_mascotas').removeAttr("required");
    $('#cantidad_mascotas').val('');
    $('#especies_mascotas').val('');
    $('#nombres_mascotas').val('');

    $("#div_nombre_mascotas").hide('slow');
    $("#div_cantidad_mascotas").hide('slow');
    $("#div_especies").hide('slow');

 }

}


function TipoVivienda(){
  var vivienda =$("#vivienda").val();

  if (vivienda == 'Otro') {
    $("#vivienda_otro_div").show("slow");
    $('#vivienda_otro').prop("required", true);
  }else{
    $("#vivienda_otro_div").hide("slow");
    $('#vivienda_otro').removeAttr("required");    
  }

}


function VerificarApto(){
  var Unidad=$("#nombre_unidad").val();
  if (Unidad != '') {
     $("#div_apto").show('slow');
     $("#apto").val('');
  }else{
    $("nombre_unidad").val('N/A');
    $("#div_apto").hide('slow');
    $("#apto").val('N/A');
  }
}


function PoseeHijos(){
onkeypress="return valideKey(event);"
  if ($("#posee_hijos").val()=='Si') {
    // $("#img_hijos").show('slow');
     $("#CantidadHijos").show('slow');
     $("#div_nombre_madre").show('slow');
     $("#div_contacto_madre").show('slow');
     $("#div_nombre_padre").show('slow');
     $("#div_contacto_padre").show('slow');
  }else{
    $("#CantidadHijos").hide('slow');
    // $("#img_hijos").hide('slow');
     $("#div_nombre_madre").hide('slow');
     $("#div_contacto_madre").hide('slow');
     $("#div_nombre_padre").hide('slow');
     $("#div_contacto_padre").hide('slow');
  }

}


j=0;
function AgregarInformacionHijos(){
  var cantidad_hijos=$("#cantidad_hijos").val();
if (j < parseInt(cantidad_hijos, 10)) {
  j=j+1;
 $("#countfieldsadd").val(j);
 $("#InformacionHijos").before("<div class='form-row' id='campoadjunto"+j+"'><div class='form-group col-sm-12'><label for='file' id='divadjunto"+j+"'>Nombre hijo "+j+":</label><input type='text' class='form-control' id='nombre_hijo"+j+"' name='nombre_hijo"+j+"' placeholder=''></div><div class='form-group col-sm-12'><label for='TipoDocumento"+j+"'>Tipo documento hijo "+j+"</label><select class='form-control' id='TipoDocumentoHijo"+j+"' name='TipoDocumentoHijo"+j+"'><option>Registro Civil</option><option>Tarjeta de identidad</option><option>Cédula</option></select></div><div class='form-group col-sm-12'><label for='file' id='divadjunto"+j+"'>Número documento hijo "+j+":</label><input type='text' class='form-control' id='numero_documento_hijo"+j+"' name='numero_documento_hijo"+j+"' placeholder=''></div><div class='form-group col-sm-12'><label for='file' id='divadjunto"+j+"'>Fecha nacimiento hijo "+j+":</label><input type='date' class='form-control' id='fecha_nacimiento_hijo"+j+"' name='fecha_nacimiento_hijo"+j+"' style=''></div><div class='form-group col-sm-12'><img src='img/eliminar.png' alt='Agregar campo adicional' width='30' height='30' onclick='EliminarInformacionHijo("+j+");' id='imagen_delete"+j+"'></div></div></div>");
  $(".format-number").on({
    "focus": function (event) {
        $(event.target).select();
    },
    "keyup": function (event) {
        $(event.target).val(function (index, value ) {
            return value.replace(/[^\d\,]/g,"")
                        //.replace(/([0-9])([0-9]{2})$/, '$1,$2')
                        .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
        });
    }
    });



  }
}


function EliminarInformacionHijo(id){
    j=j-1;
    $("#countfieldsadd").val(j);
    $("#file"+id).hide('slow', function(){ 
    $("#file"+id).remove(); 
    });
    $("#imagen_delete"+id).hide('slow', function(){ 
    $("#imagen_delete"+id).remove(); 
    });
    $("#divadjunto"+id).hide('slow', function(){ 
    $("#divadjunto"+id).remove(); 
    });
    $("#campoadjunto"+id).hide('slow', function(){ 
    $("#campoadjunto"+id).remove(); 
    });
    $("#countfieldsadd"+id).hide('slow', function(){ 
    $("#countfieldsadd"+id).remove(); 
    });
}


function Estudia_actualmente(){
  var EstudiaActualidad = $("#EstudiaActualmente").val();
  if (EstudiaActualidad == 'Si') {
    $("#div_estudia_actualmente").show('slow');
  }else{
     $("#div_estudia_actualmente").hide('slow');
  }
}


function Tipo_vivienda(){
  var Tipo_vivienda=$("#tipo_vivienda").val();


  if (Tipo_vivienda == "Otro") {
    $("#div_tipo_vivienda_otro").show('slow');
  }else{
    $("#div_tipo_vivienda_otro").hide('slow');
  }
}


function OtroFamiliar(){
 var parentesco=$("#parentesco_contacto_emergencia").val();
  if (parentesco == "Otro") {
    $("#div_otro_familar").show('slow');
  }else{
    $("#div_otro_familar").hide('slow');
  }

}


function OtraSede(){
 var Sede=$("#sede_labora").val();
  if (Sede == "OTRO") {
    $("#div_otra_sede").show('slow');
  }else{
    $("#div_otra_sede").hide('slow');
  }

}


function MensajeDatosHijos(){

var CantidadHjos=parseInt($("#cantidad_hijos").val());

    for (var i = 1; i <=CantidadHjos; i++) {
        setTimeout(AgregarInformacionHijos(), 2000);
    }

}


function Vacuna(){
	var vacuna=$("#vacuna").val();

	if (vacuna == 'No') {
		$("#div_razon_vacuna").show('slow');
	}else{
		$("#div_razon_vacuna").hide('slow');
	}
}

</script>
@endsection

