@extends('layouts.app')

@section('nav')
    @php
        $m = "";
        $cont = 0;
    @endphp
    @foreach($modules as $module)
        @if($module->module_id <> $m)
            @if($cont>0)
                    </div>
                </li>
            @else
                @php
                    $cont = 1;
                @endphp
            @endif
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{$module->module_name}}
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('invoice.create') }}">
                        {{$module->function_name}}
                    </a>
            @php
                $m = $module->module_id;
            @endphp
        @else
                <a class="dropdown-item" href="{{ route('invoice.create') }}">
                    {{$module->function_name}}
                </a>
        @endif
    @endforeach
        @if($cont > 0)
                </div>
            </li>
        @endif
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Mi Información') }}</div>

                <div class="card-body">
                    <h1>{{$user->name}}</h1>
                    <h3>{{$user->position->name}} | {{$user->ubication->name}}</h3>
                    <h5>{{$user->email}}</h5>
                    <h5>{{$user->extension}}</h5>
                    <h5>{{$user->phone}}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection