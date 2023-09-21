@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Mi Información') }}</div>

                <div class="card-body">
                    <form action="{{url('users')}}/{{$user->id}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <label for="name">Nombre Completo:</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="" value="{{$user->name}}" required disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-6">
                                <label for="first_name">Nombres:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="" value="{{$user->first_name}}"  required disabled>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="last_name">Apellidos:</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="" value="{{$user->last_name}}"  required disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-6">
                                <label for="position">Cargo:</label>
                                <input type="text" class="form-control" id="position" name="position" placeholder="" value="{{$user->profile_name}}"  required disabled>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="ubication">Ubicación:</label>
                                <input type="text" class="form-control" id="ubication" name="ubication" placeholder="" value="{{$user->ubication_name}}"  required disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <label for="leader_id">Líder:</label>
                                <input type="text" class="form-control" id="leader_id" name="leader_id" placeholder="" value="{{$user->leader->name}}" required disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <label for="email">Correo Electrónico Corporativo:</label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="" value="{{$user->email}}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <label for="email_aux">Correo Electrónico Personal:</label>
                                <input type="text" class="form-control" id="email_aux" name="email_aux" placeholder="" value="{{$user->email_aux}}" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-6">
                                <label for="extension">Extensión:</label>
                                <input type="text" class="form-control" id="extension" name="extension" placeholder="" value="{{$user->extension}}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="phone">Celular Corporativo:</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="" value="{{$user->phone}}">
                            </div>
                        </div>
                        <div class="form-group col-sm-12" style="margin-left: -1%;">
                            @if($user->notification == 0)
                              <label class="form-check-label" for="notification">Envío automático de notificaciones:</label>
                                <input type="checkbox" class="form-check-input" name="notification" style="margin-left: 1%;">
                            @else
                                <label class="form-check-label" for="notification">Envío automático de notificaciones:</label>
                                <input type="checkbox" class="form-check-input" name="notification" style="margin-left: 1%;" checked="checked">
                            @endif
                        </div>
                        <div class=" row justify-content-center">
                            <input type="submit" class="btn btn-success" name="Actualizar" value="Actualizar">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
