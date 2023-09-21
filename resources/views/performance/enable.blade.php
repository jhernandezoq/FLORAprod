@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center performance-area">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Habilitar/Deshabilitar</div>
            </div>
        </div>
    </div><br><br>

  <form action="{{url('performance/storeEnable')}}" method="POST">
    @csrf
  <div class="form-group">
    <label for="enable">Habilitar valoración</label>
    <select class="form-control" id="enable" name="enable">
      <option>Si</option>
      <option>No</option>
    </select>
  </div>

  <button type="submit" class="btn btn-success" style="margin-left: 45%;">Guardar</button>
</form>

</div>
@endsection