@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center performance-area">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Faltantes valoración') }}</div>
            </div><br>
        </div>
    </div>
          <img src="../img/excel.png" style="margin-left: 80%;" onclick="GenerarExcel();">
            <div style="margin-top: 3%;">
                <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%" id="missing_table">
                  <thead style="background-color: #90d3c1; color:white;">
                    <tr>
                      <th scope="col">Nombre</th>
                      <th scope="col">Cédula</th>
                      <th scope="col">Cargo</th>
                      <th scope="col">Líder</th>
                      <th scope="col">Area</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($data AS $datas)
                    <tr>
                      <th>{{$datas->name}}</th>
                      <td>{{$datas->cedula}}</td>
                      <td>{{$datas->profile}}</td>
                      <td>{{$datas->lider}}</td>
                      <td>{{$datas->ubication}}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
            </div>
        </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

function GenerarExcel(){
$(document).ready(function () {
    $("#missing_table").table2excel({
        filename: "faltantes_valoracion.xls"
    });
});

}


$('select').on('change', function() {
  alert( this.value );
});



</script>
@endsection
