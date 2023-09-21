@extends('layouts.app')

@section('content')

<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Resoluciones DIAN / Internas 
                </div>
                <div class="card-body">
                    <div>
                        <a 
                        href="{{url('dianresolution/create')}}/" 
                        class="btn btn-success">
                            Nueva resolución
                        </a>
                        <hr />
                    </div>
                @if($count)
                    <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Compañía</th>
                                <th>Resolución</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Actual</th>
                                <th>Estado</th>
                                <th>Fecha Vencimiento</th>
                                <th>Prefijo</th>
                                <th>Tipo</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach( $rows as $row )
                            <tr>
                                <td>{{$row->id}}</td>
                                <td>{{$row->company}}</td>
                                <td>{{$row->resolution}}</td>
                                <td>{{$row->start}}</td>
                                <td>{{$row->end}}</td>
                                <td>{{$row->current}}</td>
                                <td>
                                    {{ $row->active == 1 ? 'Activo' : 'Inactivo' }}
                                </td>
                                <td>{{$row->date_due}}</td>
                                <td>{{$row->prefix}}</td>
                                <td>{{$row->type}}</td>
                                <td>
                                <a href="{{url('dianresolution/')}}/{{$row->id}}" 
                                class="btn btn-info">
                                Editar
                                </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No hay resoluciones </p>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection