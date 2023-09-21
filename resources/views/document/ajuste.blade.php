@extends('layouts.app')

@section('content')

<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Documentos de soporte 
                </div><br><br>
                <div class="card-body">
                    @if($count)
                        <div class="div-table-responsive">
                            <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                    <th>Compañía</th>
                                    <th>Proveedor</th>
                                    <th>Documento</th>
                                    <th>Fecha de Pago</th>
                                    <th>Valor bruto</th>
                                    <th>Valor descuentos</th>
                                    <th>Valor total</th>
                                    <th>Crear Nota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach( $rows as $row )
                                    <td>{{$row->company}}</td>
                                    <td>{{$row->supplier_reason}}</td>
                                    <td>{{$row->document_number}}</td>
                                    <td>{{$row->date_due_payment}}</td>
                                    <td>
                                        {{ number_format($row->gross_total_and_tribute, 0,',','.') }}
                                    </td>
                                    <td>
                                        {{ number_format($row->discounts_total_and_detail, 0,',','.') }}
                                    </td>
                                    <td>
                                        {{ number_format($row->pay_total, 0,',','.') }}
                                    </td>
                                    <td>
                                        <a href="{{url('document/ajuste')}}/{{$row->id}}" 
                                        class="btn btn-info">
                                            Crear
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