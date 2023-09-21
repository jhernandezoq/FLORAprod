@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Gestión de documento equivalente {{$datos[0]->documento}}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col d-flex flex-row justify-content-center">
                            <div class="d-flex flex-column text-center">
                                <form method="POST" action="{{url('pdf/equivalentepdf')}}">
                                 <input type="text" name="document_id" value="{{$datos[0]->id}}" style="display: none;">
                                @csrf
                                <input type="image" src="../img/recibocaja.png" width="100px" height="100px">
                               </form>
                                <br>
                                <h3>$ {{number_format($datos[0]->total,2)}} {{$datos[0]->currency}}</h3>
                                <h6>${{number_format($datos[0]->total,2)}} {{$datos[0]->currency}} | {{$datos[0]->date}}</h6>
                            </div>
                        </div>
                    </div>
                    <br>
                    <form action="{{url('/invoice/loggestionequivalents')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="text" name="id_documento" value="{{$datos[0]->id}}" style="display: none;">
                        <div class="form-group">
                            <label for="description">Observación:</label>
                            <textarea class="form-control" id="concept" name="description" rows="3" aria-describedby="descriptionHelp" placeholder="Factura en proceso..."></textarea>
                        </div>
                        <br>
                        @if($user_rol != '3')
                        <div class="form-group">
                            <label for="approver_id">Solicitar Aprobación de:</label>
                            <select class="form-control" id="approver_id" name="approver_id" required>
                                <option value="">Seleccionar Aprobador...</option>   
                                    @foreach($approvers AS $approver)
                                    <option value="{{$approver->user_id}}">{{$approver->name}}</option> 
                                    @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col d-flex flex-row justify-content-center">
                                 <div class="d-flex flex-column text-center">
                                    <input type="submit" class="btn btn-danger" name="action" value="Rechazar">
                                </div>
                                 
                                <div class="d-flex flex-column text-center">
                                     @if($user_rol == '1')
                                       <input type="submit" class="btn btn-success" name="action" value="Validar">
                                     @elseif($user_rol == '2')
                                            <input type="submit" class="btn btn-success" name="action" value="Aprobar">
                                     @else
                                            <input type="submit" class="btn btn-success" name="action" value="Finalizar">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                    <br>
                    @if($cantidad_flow > 0)
                     <div class="row">
                        <div class="col">
                            <table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Responsable</th>
                                        <th>Estado</th>
                                        <th>Observación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($flow as $fl)
                                    <tr>
                                        <td>{{$fl->date}}</td>
                                        <td>{{$fl->name}}</td>
                                        <td>{{$fl->estado}}</td>
                                        <td>{{$fl->description}}</td>
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
