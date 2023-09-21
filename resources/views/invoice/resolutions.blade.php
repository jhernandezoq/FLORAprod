@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Creación y edición de resoluciones</div>
                    @if(($error) > 0)
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                  <span>Lo sentimos:</span>
                                  <li>Ya existe una resolución activa para la empresa seleccionada</li>
                            </ul>
                        </div>
                    @endif
                    @if(($error_fecha) > 0)
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                  <span>Lo sentimos:</span>
                                  <li>La fecha de inicio no puede ser mayor a la fecha de finalización</li>
                            </ul>
                        </div>
                    @endif
                <div class="card-body">
                    <form action="{{url('/invoice/resolutionskeep')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="flow_id">Compañia:</label>
                            <select class="form-control" id="company_id" name="company_id" required>
                                <option value="">Seleccionar Compañia...</option>   
                                @foreach($companies as $company)
                                    <option value="{{$company->code}}">{{$company->code}} - {{$company->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <label for="number">Número de resolución:</label>
                                <input type="text" class="form-control" id="number" name="number" placeholder="" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-sm-6">
                                <label for="create_date">Fecha de inicio:</label>
                                <input type="text" class="form-control" id="begin_date" name="begin_date" placeholder="" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="due_date">Fecha de vencimiento:</label>
                                <input type="text" class="form-control" id="end_date" name="end_date" placeholder="" data-provide="datepicker" data-date-format="yyyy-mm-dd" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-4">
                                <label for="create_date">Prefijo:</label>
                                <input type="text" class="form-control" id="prefijo" name="prefijo" placeholder="" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="create_date">Consecutivo inicial:</label>
                                <input type="text" class="form-control" id="int_number" name="int_number" placeholder="" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="due_date">Consecutivo final:</label>
                                <input type="text" class="form-control" id="end_number" name="end_number" placeholder="" required>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <input type="submit" class="btn btn-success" name="radicar" value="Guardar">
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
            return value.replace(/[^\d\,]/g,"")
                        //.replace(/([0-9])([0-9]{2})$/, '$1,$2')
                        .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
        });
    }
    });

   $('#supplier_id').select2();
   $('#id_supplier').select2();

});

function find(){
    var tfind=$('#supplier_find').val();
    if (tfind == 1) {
        $('#name_proveedor').show(1000);
        $('#id_proveedor').hide(1000);
        document.getElementById("id_supplier").required = false;
    }else{
        $('#name_proveedor').hide(1000);
        $('#id_proveedor').show(1000);
        document.getElementById("supplier_id").required = false;
    }

}
</script>
@endsection