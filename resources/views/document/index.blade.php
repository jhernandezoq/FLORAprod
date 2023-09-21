@extends('layouts.app')

@section('content')

<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Documentos de soporte 
                </div>
                <div class="card-body">
                    <div>
                        <a 
                        href="{{url('document/create')}}/" 
                        class="btn btn-success">
                            Nuevo documento
                        </a>
                        <hr />
                    </div>
                    @if($count)
                        <div class="div-table-responsive">
                            <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                    <th>ID</th>
                                    <th>Compañía</th>
                                    <th>Proveedor</th>
                                    <th>Estado</th>
                                    <th>Documento</th>
                                    <th>Fecha de Pago</th>
                                    <th>Valor total</th>
                                    <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach( $rows as $row )
                                    <td>{{$row->id}}</td>
                                    <td>{{$row->company}}</td>
                                    <td>{{$row->supplier_reason}}</td>
                                    <td>{{$row->status}}</td>
                                    <td>{{$row->document_number}}</td>
                                    <td>{{$row->date_due_payment}}</td>
                                    <td>
                                        {{ number_format($row->pay_total, 0,',','.') }}
                                    </td>
                                    <td>
                                        <a href="{{url('document/')}}/{{$row->id}}" 
                                        class="btn btn-info">
                                            Editar
                                        </a>
                                    </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>No hay documentos </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection