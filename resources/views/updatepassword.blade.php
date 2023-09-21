@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Cambio de contraseña') }}</div>
                       @if($error == 1)
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                  <span>Verifica tu correo:</span>
                                  <li>No existen registros de tu correo en nuestra base de datos</li>
                            </ul>
                        </div>
                        @elseif($error == 0)
                        <div class="alert alert-success" role="alert">
                            <ul>
                                  <span>Requerimiento exitoso!!:</span>
                                  <li>A tu correo fue enviado un link para que puedas realizar el cambio de contraseña</li>
                            </ul>
                        </div>
                       @endif
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.change') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Correo Electrónico') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Enviar link para cambio de contraseña') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
