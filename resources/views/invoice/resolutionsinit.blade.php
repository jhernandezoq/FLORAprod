@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Listado de resoluciones</div><br><br>
                   <a href="{{url('invoice/resolutions')}}"><img src="../img/agregaradjunto.png" alt="Crear resolución" width="50" height="50" title="Crear nueva resolución"></a><br><br>
                    @if($cantidad_resolutions > 0)
                     <div class="row">
                        <div class="col"><br><br>
                            <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Compañia</th>
                                        <th>Número resolución</th>
                                        <th>Fecha inicio</th>
                                        <th>Fecha fin</th>
                                        <th>Prefijo</th>
                                        <th>Consecutivo inicial</th>
                                        <th>Consecutivo final</th>
                                        <th>Estado</th>
                                        <th>Inactivar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resolutions as $res)
                                    <tr>
                                        @if($res->id_company == 1000)
                                         <td>Pérez y Cardona S.A.S</td>
                                        @elseif($res->id_company == 2000)
                                         <td>MP Galagro S.A.S</td>
                                        @elseif($res->id_company == 3000)
                                         <td>Super Agro S.A.S</td>
                                         else
                                         <td>Antonio Pérez</td>
                                         @endif
                                        <td>{{$res->resolution_number}}</td>
                                        <td>{{$res->begin_date}}</td>
                                        <td>{{$res->finish_date}}</td>
                                        <td>{{$res->prefijo}}</td>
                                        <td>{{$res->int_number}}</td>
                                        <td>{{$res->end_number}}</td>
                                        @if($res->active == 1)
                                        <td>Activo</td>
                                        @else
                                        <td>Inactivo</td>
                                        @endif
                                        @if($res->active == 1)
                                        <td>
                                        <form method="POST" action="{{url('invoice/resolutionsinactive')}}">
                                        @csrf
                                        <input type="text" name="id_resolution" value="{{$res->id}}" style="display: none;">
                                        <button type="submit" class="btn btn-success">Inactivar</button>
                                       </form>
                                       </td>
                                        @else
                                        <td><button type="button" class="btn btn-success" disabled="disabled">Inactivar</button></td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                     </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

</script>
@endsection
