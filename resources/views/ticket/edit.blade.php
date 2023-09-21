@extends('layouts.app')

@section('content')
<div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Re-asignar caso</div>
                <div class="card-body">
                    <form action="{{ url('edit') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($state_mail == 0)
                        <input type="hidden" name="user_id" value="{{$user->id}}">
                        @else
                        <input type="hidden" name="user_id" value="{{$user}}">
                        @endif
                        <div class="form-group">
                            <label for="tool_id">Herramienta:</label>
                            <select class="form-control" id="tool_id" name="tool_id" required onchange="Usersti()">
                                <option value="">Selecciona la herramienta con la cual deseas asociar el caso...</option>   
                                @foreach($tools as $tool)
                                    <option value="{{$tool->id}}">{{$tool->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="usersti">
                            <label for="tool_id">Usuarios TI:</label>
                            <select class="form-control" id="agent_id" name="agent_id">
                         <option value="">Selecciona el usuario de TI al que deseas asociar el caso...</option>   
                                @foreach($assignments as $assignment)
                                    <option value="{{$assignment->id}}">{{$assignment->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="textarea">
                            <label for="concept">Ingresa informacion adicional para respaldar la asignacion:</label>
                            <textarea class="form-control" id="text" name="text" rows="3" required></textarea>
                        </div>

                        
                        <div class="form-group" id="supportfile">
                            <label for="file">Archivo de soporte:</label>
                            <input type="file" class="form-control" id="file" name="file" placeholder="">
                        </div>
                       <input type="text" name="ticket_id" value="{{$ticket_id}}" style="display: none;">
                        <div class="row justify-content-center">
                            <input type="submit" class="btn btn-success" name="radicar" value="Asignar">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

@endsection