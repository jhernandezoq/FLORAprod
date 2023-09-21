@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center performance-area">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Historial</div>
            </div>
        </div>
    </div><br><br>
    <ul class="list-inline">
      <li class="list-inline-item"><strong><h5>NOMBRE:</strong>&nbsp;{{$data[0]->name}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h5></li>
      <li class="list-inline-item"><strong><h5>LIDER:</strong>&nbsp;{{$data[0]->jefe}}</h5></li>
    </ul>
<table class="table-responsive-md table-bordered table-striped table-sm" cellspacing="0" width="100%">
                  <thead style="background-color: #90d3c1; color:white;">
                    <tr>
                      <th scope="col">Año</th>
                      <th scope="col">Promedio Ser</th>
                      <th scope="col">Promedio Saber</th>
                      <th scope="col">Promedio Hacer</th>
                      <th scope="col">Promedio Total</th>
                      <th scope="col">Adjunto</th>
                      <th scope="col">Seleccionar</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($data AS $datas)
                    <tr>
                      <td style="text-align: center;">{{$datas->year}}</td>
                       @foreach($datapdiser AS $ser)
                        @if($ser->date == $datas->date)
                           <td style="text-align: center;">{{$ser->average}}</td>
                        @endif
                       @endforeach
                       @foreach($datapdisaber AS $saber)
                        @if($saber->date == $datas->date)
                           <td style="text-align: center;">{{$saber->average}}</td>
                        @endif
                       @endforeach
                       @foreach($datapdihacer AS $hacer)
                        @if($hacer->date == $datas->date)
                           <td style="text-align: center;">{{$hacer->average}}</td>
                        @endif
                       @endforeach
                       @foreach($datapditotal AS $total)
                        @if($total->date == $datas->date)
                           <td style="text-align: center;">{{$total->average}}</td>
                        @endif
                       @endforeach
                       @foreach($datapditotal AS $total)
                         @if($total->date == $datas->date)
                          <td><a href="{{url('files/'.$total->file)}}">{{$total->file}}</a></td>
                         @endif
                       @endforeach
                          <td style="width: 40%;"><form method="POST" action="{{url('performance/files')}}" enctype="multipart/form-data">
                            @csrf
                          <input type="text" name="id_user" value="{{$datas->id}}" style="display: none;">
                          <input type="text" name="year" value="{{$datas->year}}" style="display: none;">
                          <div class="row">
                            <div class="col-sm">
                              <input type="file" class="form-control" id="file" name="file" placeholder="" style="margin-bottom: 2%; width: 300px;">
                            </div>
                            <div class="col-sm">  
                              <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                          </div>    
                          </form>
                        </td>
                    </tr>
                    @endforeach

                  </tbody>
                </table>
</div>
@endsection