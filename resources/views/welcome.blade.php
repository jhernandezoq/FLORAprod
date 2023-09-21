<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/c6fb22ad47.js" crossorigin="anonymous"></script>
    
        <!-- Styles -->

        <style>
            html, body {
                background-color: #90d3c1;
                color: #636b6f;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }
            .full-height {
                height: 85vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-family: 'Kalam', cursive;
                color: #483820;
                font-weight: 200;
                font-size: 96px;
                letter-spacing: 5px;
            }

            .d-block {
                display: block;
            }
            
            .links > li > a {
                font-family: 'Montserrat', sans-serif;
                color: #ffffff;
                font-size: 14px;
                font-weight: 500;
                text-decoration: none;
                text-transform: uppercase;
                
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            .orange
            {
                color: #ff701f;
            }

            .proposito
            {
                font-family: 'Montserrat', sans-serif;
                color: #2d6a4f;
                font-size: 16px;
                font-weight: 400;
            }

        </style>
    </head>
    <body>
        @if (Route::has('login'))
                <nav class="navbar navbar-expand-lg">
                    <ul class="navbar-nav links ml-auto">
                        @auth
                        <li class="nav-item">
                            <a class="nav-link">{{ Auth::user()->first_name }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('salidalogin') }}">Cambio contraseña</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                            {{ __('Salir') }}
                            </a>
                        </li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                            </form>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Ingresar</a>
                            </li>

                            @if (Route::has('register'))
                                <!--<a href="{{ route('register') }}">Registro</a>-->
                            @endif
                        @endauth
                    </ul>
                </nav>
            @endif
        <div class="flex-center position-ref full-height">

            <div class="content">
                <div class="title m-b-md">
                    flora<span class="orange">.</span>
                </div>
                <nav class="navbar d-block navbar-expand-lg">
                    @auth
                    <ul class="navbar-nav links mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{url('/yo')}}">Yo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Comunidad</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{url('/servicio')}}">Servicios</a>
                        </li>    
                        <li class="nav-item">    
                            <a class="nav-link" href="{{url('/procesos')}}">Procesos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{url('/autogestion')}}">Herramientas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{url('/informes')}}">Informes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{url('/admin')}}">Permisos</a>
                        </li>
                    </ul>
                    @else
                        <p class="proposito">¡haciendo la vida de los usuarios más fácil!</p>
                    @endauth
                </nav>
            </div>
        </div>
        @section('scripts')
        <script type="text/javascript">

        function prueba(){
            alert("Ingreso aqui");
        }

        $(document).ready(function () {
           console.log("Ingreso aqui");

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
     

        });
        </script>
        @endsection
    </body>
</html>
