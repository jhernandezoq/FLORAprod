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
<i><a href="https://servercloudp-my.sharepoint.com/:f:/g/personal/flora_tierragro_co/EteGxGrHQc9MrNb-Z35gVYABf2VpnUCCJbmP4hnzSYsWUA?e=bFVupq"><h5>Informes de RTC</h5></a></i>
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
