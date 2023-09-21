@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Informes') }}</div>
            </div>
        </div>
    </div>
</div><br>

<div class="container">
<i><h4>Selecciona el informe que deseas visualizar</h4></i>
</div>
<div class="container" style="display: none;">
  <div class="container">
    <label for="exampleFormControlSelect1">Selecciona el informe que deseas visualizar:</label>
    <form method="GET" action="{{url('reports/find')}}">
      @csrf
    <select class="form-control" id="report" name="report">
      <option value=0>Seleccione...</option>
      @foreach($reports AS $report)
      <option value={{$report->route}}>{{$report->report}}</option>
      @endforeach
    </select><br>
    <button type="submit" class="btn btn-success" style="margin-left: 50%;">Buscar</button>
  </form>
  </div>
</div>
@endsection
