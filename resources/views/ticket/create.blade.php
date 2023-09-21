@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Crear Caso</div>
                <div class="card-body">
                    <form action="{{ url('ticket') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" value="{{$user->id}}">
                        <div class="form-group">
                            <label for="tool_id">Herramienta:</label>
                            <select class="form-control" id="tool_id" name="tool_id" required>
                                <option value="">Selecciona donde esta tu solicitud...</option>   
                                @foreach($tools as $tool)
                                    <option value="{{$tool->id}}">{{$tool->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="concept">Cuentanos que necesitas:</label>
                            <textarea class="form-control" id="text" name="text" rows="3" required></textarea>
                        </div>

                        
                        <div class="form-group">
                            <label for="file">Archivo de soporte:</label>
                            <input type="file" class="form-control" id="file" name="file" placeholder="">
                        </div>

                        <div class="row justify-content-center">
                            <input type="submit" class="btn btn-success" name="radicar" value="Crear Caso" onclick="ocultar()" id="radicar">
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

function ocultar(){
    $("#radicar").hide('slow');
}
</script>
@endsection