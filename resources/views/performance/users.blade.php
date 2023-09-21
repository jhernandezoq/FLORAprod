@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Actualización empleados') }}</div>
            </div>
        </div>
    </div>
</div><br>
<form action="{{url('performance/editUser')}}" method="POST">
@csrf
<div class="container">
    <label for="name">Seleccione el usuario a editar:</label>
    <select class="form-control" id="id" name="id">
      @foreach($users as $user)
      <option value={{$user->id}}>{{$user->name}}</option>
      @endforeach
    </select>
<div><br><br>

<button type="submit" class="btn btn-success" style="margin-left: 45%;">Editar</button>
</form>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function () {

   $('#id').select2();

});

</script>
@endsection
