@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">


                <div class="card-header">Radicar Factura</div>

                <ul>
                    <div class="alert alert-danger" role="alert">
                      <span>Recuerda:</span>
                      <li>Es necesario adjuntar el archivo de soporte de la factura que deseas radicar</li>
                      <li>El archivo cargado no debe tener un tamaño superior a 1.5 MB</li>
                    </div>
                </ul>
                <div class="card-body">
                    <form action="{{url('invoice')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="flow_id">Compañia:</label>
                            <select class="form-control" id="company_id" name="company_id" required>
                                <option value="">Seleccionar Compañia...</option>   
                                @foreach($companies as $company)
                                    <option value="{{$company->id}}">{{$company->code}} - {{$company->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="flow_id">Flujo:</label>
                            <select class="form-control" id="flow_id" name="flow_id" required>
                                <option value="">Seleccionar Flujo...</option>   
                                @foreach($flows as $flow)
                                    <option value="{{$flow->id}}">{{$flow->name}}</option>  
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-6">
                                <label for="supplier_id">Proveedor:</label>
                                <select class="form-control" id="supplier_id" name="supplier_id" required>
                                    <option value="">Seleccionar Proveedor...</option>   
                                    @foreach($suppliers as $sup)
                                        <option value="{{$sup->id}}">{{$sup->name}} | {{$sup->nit}}</option>  
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="number">Número de Factura:</label>
                                <input type="text" class="form-control" id="number" name="number" placeholder="" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-sm-6">
                                <label for="create_date">Fecha de creación:</label>
                                <input type="text" class="form-control" id="create_date" name="create_date" placeholder="" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="due_date">Fecha de vencimiento:</label>
                                <input type="text" class="form-control" id="due_date" name="due_date" placeholder="" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="concept">Concepto(s)</label>
                            <textarea class="form-control" id="concept" name="concept" rows="3" required></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="subtotal">Subtotal:</label>
                                <input type="text" class="form-control format-number" id="subtotal" name="subtotal" placeholder="" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="iva">IVA:</label>
                                <input type="text" class="form-control format-number" id="iva" name="iva" placeholder="" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="total">Total:</label>
                                <input type="text" class="form-control format-number" id="total" name="total" placeholder="" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="priority">Factura Prioritaria:</label>
                            <select class="form-control" id="priority" name="priority" required>
                                <option value="">Seleccionar prioridad...</option>
                                <option value="1">Si</option>
                                <option value="0">No</option>         
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="file">Archivo Factura:</label>
                            <input type="file" class="form-control" id="file" name="file" placeholder="" required>
                        </div>
                        <div class="form-group">
                            <label for="state_id">Estado Factura:</label>
                            <select class="form-control" id="state_id" name="state_id" required>
                                <option value="">Seleccionar Estado...</option>
                                <option value="1">Radicada</option>
                                <option value="2">Rechazada</option>         
                            </select>
                        </div>



                        <div class="form-group">
                            <label for="description">Observación</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>Factura en proceso...</textarea>
                        </div>

                        <div class="row justify-content-center">
                            <input type="submit" class="btn btn-success" name="radicar" value="Radicar">
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
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
            return value.replace(/\D/g, "")
                        //.replace(/([0-9])([0-9]{2})$/, '$1,$2')
                        .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
        });
    }
    });

  $('.datepicker').datepicker();
  $('#supplier_id').select2();
   $('#id_supplier').select2();
   $('#cuenta').select2();

});
</script>
@endsection