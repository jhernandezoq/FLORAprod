@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Mi Información') }}</div>

                <div class="card-body">
                    @foreach($userdata as $userdt)
                    <form action="{{url('performance/edition')}}/{{$userdt->id}}" method="GET" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <label for="name">Nombre Completo:</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="" value="{{$userdt->name}}" required disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-6">
                                <label for="first_name">Nombres:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="" value="{{$userdt->first_name}}"  required disabled>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="last_name">Apellidos:</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="" value="{{$userdt->last_name}}"  required disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-6">
                                <label for="position">Cargo:</label>
                                <input type="text" class="form-control" id="position" name="position" placeholder="" value="{{$userdt->cargo}}"  required disabled>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="ubication">Ubicación:</label>
                                <input type="text" class="form-control" id="ubication" name="ubication" placeholder="" value="{{$userdt->ubication}}"  required disabled>
                            </div>
                        </div>
                      <div class="form-row">
                        <div class="form-group col-sm-12">
                           <label for="leader_id">Líder:</label>
                           <select class="form-control" id="leader_id" name="leader_id">
                            @foreach($leader AS $leaderdata)
                              <option selected="selected" value={{$leaderdata->leader_id}}>{{$leaderdata->leader_name}}</option>
                            @endforeach
                             @foreach($users as $user)
                              <option value={{$user->id}}>{{$user->name}}</option>
                            @endforeach
                           </select>
                        </div>
                      </div>
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <label for="email">Correo Electrónico:</label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="" value="{{$userdt->email}}" required disabled="disabled">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <label for="email_aux">Correo Electrónico Personal:</label>
                                <input type="text" class="form-control" id="email_aux" name="email_aux" placeholder="" value="{{$userdt->email_aux}}" required disabled="disabled">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-6">
                                <label for="extension">Extensión:</label>
                                <input type="text" class="form-control" id="extension" name="extension" placeholder="" value="{{$userdt->extension}}"  required disabled="disabled">
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="phone">Celular:</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="" value="{{$userdt->phone}}"  required disabled="disabled">
                            </div>
                        </div>
                        <div class="form-group col-sm-12" style="margin-left: -1%;">
                        <div class=" row justify-content-center">
                            <input type="submit" class="btn btn-success" name="Actualizar" value="Actualizar">
                        </div>
                    </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function () {

   $('#leader_id').select2();

});

</script>
@endsection