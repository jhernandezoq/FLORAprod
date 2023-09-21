@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Cambio contraseña') }}</div>
                       @if($error == 1)
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                  <span>Verifica tu contraseña:</span>
                                  <li>Por favor verifica que la contraseña ingresada y su confirmación coincidan</li>
                            </ul>
                        </div>
                        @elseif($error == 2)
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                  <span>Verifica tu correo:</span>
                                  <li>No existen registros de tu correo en nuestra base de datos</li>
                            </ul>
                        </div>
                        @elseif($error == 3)
                        <div class="alert alert-success" role="alert">
                            <ul>
                                  <span>Cambio exitoso!!:</span>
                                  <li>Tu contraseña fue actualizada de forma exitosa</li>
                            </ul>
                        </div>
                       @endif
                       @if($error != 3)
                <div class="card-body">
                    <form method="POST" action="{{url('/updatepasswordfinal')}}">
                        @csrf
                        <input type="text" name="id_usuario" value="{{$id}}" style="display: none;">
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Contraseña') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirmar contraseña') }}</label>

                            <div class="col-md-6">
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Guardar') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
             <div class="card-body">
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <a class="nav-link" href="{{ route('login') }}"><button type="button" class="btn btn-primary" style="margin-left: 25%; width: 90px;">
                                    {{ __('Salir') }}
                                </button></a>
                            </div>
                        </div>
             </div>
            @endif
            </div>
        </div>
    </div>
</div>
@endsection
