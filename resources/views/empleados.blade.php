@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Actualización de empleados') }}</div>
            </div>
        </div>
    </div>
</div><br>

<div class="container" id="id_boton_rojo">
<i><h4>Da click en el botón rojo de "Actualizar" para actualizar los jefes directos de los empleados</h4></i><br><br>
<form action="{{url('/actualizacionjefes')}}"  method="post">
	@csrf
	<button type="submit" class="btn btn-danger" style="width: 20%; margin-left: 40%;" onclick='ActualizarJefes();'>Actualizar</button>
</form>


</div>

<div class="loader" style="margin-left: 45%; display: none;" id="loader">
</div>

<div class="container" id="id_boton_verde" style="display: none;">
<i><h4>Da click en el botón verde de actualizar para ingresar a los empleados nuevos de la compañia</h4></i>
</div>


@endsection

@section('scripts')
<script type="text/javascript">

$(document).ready(function () {
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

});



function ActualizarJefes(){
  var token=$("input[name=_token]").val();
  $("#id_boton_rojo").hide('slow');
  $("#loader").show('slow'); 

  /*$.ajax({
      data:{token:token},
      url:'https://flora.tierragro.com/api/actualizacionjefes',
      type:'GET',
      dataType :'JSON',
      success:function(data){
          

    }
  });*/


}


function GenerarExcel(){
$(document).ready(function () {
    $("#directorio").table2excel({
        filename: "directorio.xls"
    });
});

}



</script>

@endsection


