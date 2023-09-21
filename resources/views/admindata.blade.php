@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Administración') }}</div>
            </div>
        </div>
    </div>
</div><br>


<div class="container">
    <label for="name">Seleccione el usuario:</label><br>
    <select class="form-control" id="id" name="id" style="width: 100%;">
      @foreach($users as $user)
      <option value="{{$user->id}}">{{$user->name}}</option>
      @endforeach
    </select>
<div><br><br>

<button type="submit" class="btn btn-success" style="margin-left: 45%;" onclick="Formulario();" id="BuscarUser">Buscar</button><br>


<form id="FormularioPermisos" action="{{url('/adminpermissions')}}" method="POST" style="display: none;">
  @csrf
<button type="submit" class="btn btn-success" style="margin-left: 45%; margin-top: 2%;" onclick="FormularioOcultar();" id="BuscarUser">Guardar</button>
<input type="text" name="id_user" id="id_user" style="display: none;">
<div class="card">
  <div class="card-body">
   <div class="form-row">
    <div class="col-sm-4">
        <div class="alert alert-secondary" role="alert">
          <h4 class="alert-heading">Aplicaciones..</h4>
           @foreach($applications as $application)
          <hr>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="Aplicaciones" id="{{$application->id}}" value="{{$application->id}}" onclick="VerificarModulos('{{$application->id}}');">
                  <label class="form-check-label" for="{{$application->id}}">
                    {{$application->id}}. 
                    {{$application->name}}
                  </label>
                </div>
            @endforeach
        </div>
     </div>

    <div class="col-sm-4">
        <div class="alert alert-secondary" role="alert">
          <h4 class="alert-heading">Modulos</h4>
           @foreach($modulesfinal as $modulefinal)
          <hr>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="Modulos" id="{{$modulefinal->id}}" value="{{$modulefinal->id}}" disabled="disabled" onclick="VerificarFunciones('{{$modulefinal->id}}');">
                  <label class="form-check-label" for="{{$modulefinal->id}}">
                  {{$modulefinal->id}}. 
                  {{$modulefinal->name}}
                  </label>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-sm-4">
        <div class="alert alert-secondary" role="alert">
          <h4 class="alert-heading">Funciones</h4>
           @foreach($functions as $function)
          <hr>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="Funciones" id="{{$function->id}}" value="{{$function->id}}" disabled="disabled">
                  <label class="form-check-label" for="{{$function->id}}">
                  {{$function->id}}. 
                  {{$function->name}}
                  </label>
                </div>
            @endforeach
        </div>
    </div>
</div>
</form>

@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function () {

   $('#id').select2();

});

var UsuarioSeleccionado;
function Formulario(){
  $("#FormularioPermisos").show('slow');
  var UsuarioSeleccionado=$("#id").val();
  $("#id_user").val(UsuarioSeleccionado);
}


function FormularioOcultar(){

 // $("#FormularioPermisos").hide('slow');

  //var UsuarioSeleccionado=$("#id").val();


}


const listApplications = {
  'Yo': 1,
  'Comunidad': 2,
  'Servicios': 3,
  'Procesos': 4,
  'Herramientas': 5,
  'Informes': 6,
  'Permisos': 7,
}

const listModules = [
  { id: 1, name: 'Casos' },
  { id: 2, name: 'Facturas' },
  { id: 3, name: 'Valoración' },
  { id: 4, name: 'Informes' },
  { id: 5, name: 'Administración' },
  { id: 7, name: 'Actualiza tus datos' },
  { id: 8, name: 'Anticipos' },
  { id: 9, name: 'Doc. Soporte' },//(1 !!!)
]

const listFunctions = [
  { id: 1, name: 'Reportados por mi' },
  { id: 2, name: 'Pendiente por mi gestión' },
  { id: 3, name: 'Radicar facturas' },
  { id: 4, name: 'Gestionar facturas' },
  { id: 5, name: 'Flujo de facturas' },
  { id: 6, name: 'Autovaloración' },
  { id: 7, name: 'Valoración equipo' },
  { id: 8, name: 'Informes valoración' },
  { id: 9, name: 'Habilitar autovaloración' },
  { id: 10, name: 'Actualización empleados' },
  { id: 11, name: 'Faltantes valoración' },
  { id: 12, name: 'Informe facturas radicadas' },
  { id: 13, name: 'Consulta/Edición facturas' },
  { id: 14, name: 'Informe tickets' },
  { id: 15, name: 'Administración' },
  { id: 16, name: 'Informe permisos' },
  { id: 17, name: 'Directorio empleados' },
  { id: 18, name: 'Informe gestión facturas' },
  { id: 19, name: 'Información adicional empleados' },
  { id: 20, name: 'Informe rappi' },
  { id: 22, name: 'Datos personales' },
  { id: 23, name: 'Gestion BD Empleados' },
  { id: 24, name: 'Anticipos' },
  { id: 25, name: 'Gestión anticipos' },
  { id: 26, name: 'Pagar anticipos' },
  { id: 27, name: 'Legalización anticipos' },
  { id: 28, name: 'Historial anticipos' },
  { id: 29, name: 'Gestión legalizaciones' },
  { id: 30, name: 'Cierre de legalización' },
  { id: 31, name: 'Informe anticipos' },
  { id: 33, name: 'Carga de empleados' },
  { id: 34, name: 'Consecutivos DIAN - DS' },//(2 !!!)
  { id: 35, name: 'Generar Documento' },
  { id: 36, name: 'Nota de ajuste' },
  { id: 37, name: 'Legalización gastos' },
  { id: 38, name: 'Gestión legalización gastos' },
  { id: 39, name: 'Informe gastos' },
  { id: 40, name: 'Historial gastos' },
  { id: 41, name: 'Pagos legalización gastos' },
  { id: 42, name: 'Distribución proveedores' },
  { id: 43, name: 'Devoluciones' },
  { id: 44, name: 'Gestión devoluciones' },
  { id: 45, name: 'Informe devoluciones' },
  { id: 46, name: 'Historial devoluciones' },
]

const activateDeactivateModules = ( idsActivate ) => {
  let i
  for( i in listModules ){
    let item = listModules[ i ]

    let j, activate = false
    for( j in idsActivate ){
      let idActivate = idsActivate[ j ]
      if( idActivate == item.id ) activate = true
    }

    let valueRadio = `input[type=radio][name=Modulos][value=${item.id}]`
    if( activate )
      $(valueRadio).prop('disabled', false );
    else
      $(valueRadio).prop('disabled', true );

    $(valueRadio).prop('checked', false );
  }
}

const activateDeactivateFunctions = ( idsActivate ) => {
  let i
  for( i in listFunctions ){
    let item = listFunctions[ i ]

    let j, activate = false
    for( j in idsActivate ){
      let idActivate = idsActivate[ j ]
      if( idActivate == item.id ) activate = true
    }

    let valueRadio = `input[type=radio][name=Funciones][value=${item.id}]`
    if( activate )
      $(valueRadio).prop('disabled', false )
    else
      $(valueRadio).prop('disabled', true )

    $(valueRadio).prop('checked', false )
  }
}

const activateDeactivateOnlyFunctions = ( idsActivate ) => {
  let i
  for( i in listFunctions ){
    let item = listFunctions[ i ]

    let j, activate = false
    for( j in idsActivate ){
      let idActivate = idsActivate[ j ]
      if( idActivate == item.id ) activate = true
    }

    let valueRadio = `input[type=radio][name=Funciones][value=${item.id}]`
    if( activate )
      $(valueRadio).prop('disabled', false )
    else
      $(valueRadio).prop('disabled', true )
  }
}

function VerificarModulos( idAplicacion ){
  if( idAplicacion == listApplications['Yo'] ){
    activateDeactivateModules( [ 3, 7 ] )

    activateDeactivateFunctions( [ ] )
  }
  else if ( idAplicacion == listApplications['Comunidad'] ){
    activateDeactivateModules( [ ] )

    activateDeactivateFunctions( [ ] )
  }
  else if ( idAplicacion == listApplications['Servicios'] ){
    activateDeactivateModules( [ 1 ] )
    
    activateDeactivateFunctions( [ ] )
  }
  else if( idAplicacion == listApplications['Procesos'] ) {
    activateDeactivateModules( [ 2, 8, 9 ] ) //(3 !!!) => 9
    
    //activateDeactivateFunctions( [ 31 ] )
    activateDeactivateFunctions( [ ] )
  }
  else if ( idAplicacion == listApplications['Herramientas'] ) {
    activateDeactivateModules( [ ] )
    
    activateDeactivateFunctions( [ ] )
  }
  else if ( idAplicacion == listApplications['Informes'] ) {
    activateDeactivateModules( [ 4 ] )
    
    activateDeactivateFunctions( [ 31 ] )
  }
  else if ( idAplicacion == listApplications['Permisos'] ) {
    activateDeactivateModules( [ 5 ] )
    
    activateDeactivateFunctions( [ 33 ] )
  }
}

function VerificarFunciones(idModulo){

  if( idModulo == listModules[ 0 ].id && listModules[ 0 ].name == 'Casos'  ){
    activateDeactivateOnlyFunctions( [ 1,2 ] )
  }
  else if ( idModulo == listModules[ 1 ].id && listModules[ 1 ].name == 'Facturas' ){
    activateDeactivateOnlyFunctions( [ 3,4,5,19,43,44,45,46] )
  }
  else if (idModulo == listModules[ 2 ].id && listModules[ 2 ].name == 'Valoración' ) {
    activateDeactivateOnlyFunctions( [ 6,7,9,10, ] )
  }
  else if (idModulo == listModules[ 3 ].id && listModules[ 3 ].name == 'Informes' ) {
    activateDeactivateOnlyFunctions( [8,11,12,13,14,17,18,19,31 ] )
  }
  else if (idModulo == listModules[ 4 ].id && listModules[ 4 ].name == 'Administración' ) {
    activateDeactivateOnlyFunctions( [ 15,16,33 ] )
  }
  else if (idModulo == listModules[ 5 ].id && listModules[ 5 ].name == 'Actualiza tus datos' ) {
    activateDeactivateOnlyFunctions( [ 22,23 ] )
  }
  if (idModulo == listModules[ 6 ].id && listModules[ 6 ].name == 'Anticipos' ) {
    activateDeactivateOnlyFunctions( [ 24,25,26,27,28,29,30,32 ] )
  }
   //(4 !!!) => 34
  if (idModulo == listModules[ 7 ].id && listModules[ 7 ].name == 'Doc. Soporte' ) {
    activateDeactivateOnlyFunctions( [ 34, 35 ] )
  }
}


</script>
@endsection
