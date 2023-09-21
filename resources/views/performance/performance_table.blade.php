<table>
    <thead>
    <tr>
      <th>Nombre</th>
      <th>Lider</th>
      <th>Area</th>
      <th>Año</th>
      <th>Promedio Ser</th>
      <th>Objetivo Ser</th>
      <th>Accion Ser</th>
      <th>Fecha Ser</th>
      <th>Promedio Saber</th>
      <th>Objetivo Saber</th>
      <th>Accion Saber</th>
      <th>Fecha Saber</th>
      <th>Promedio Hacer</th>
      <th>Objetivo Hacer</th>
      <th>Accion Hacer</th>
      <th>Fecha Hacer</th>
      <th>Promedio Total</th>
    </tr>
    </thead>
                 <tbody>
                    @foreach($data AS $datas)
                    <tr>
                      <th>{{$datas->name}}</th>
                      <td>{{$datas->jefe}}</td>
                      <td>{{$datas->area}}</td>
                       @foreach($datapdiser AS $ser)
                        @if(($ser->id == $datas->id) && ($ser->year == $datas->year))
                           <td>{{$ser->year}}</td>
                           <td>{{$ser->average}}</td>
                           <td>{{$ser->objective}}</td>
                           <td>{{$ser->action}}</td>
                           <td>{{$ser->followupdate}}</td>
                        @endif
                       @endforeach
                       @foreach($datapdisaber AS $saber)
                        @if(($saber->id == $datas->id) && ($saber->year == $datas->year))
                           <td>{{$saber->average}}</td>
                           <td>{{$saber->objective}}</td>
                           <td>{{$saber->action}}</td>
                           <td>{{$saber->followupdate}}</td>
                        @endif
                       @endforeach
                       @foreach($datapdihacer AS $hacer)
                        @if(($hacer->id == $datas->id) && ($hacer->year == $datas->year))
                           <td>{{$hacer->average}}</td>
                           <td>{{$hacer->objective}}</td>
                           <td>{{$hacer->action}}</td>
                           <td>{{$hacer->followupdate}}</td>
                        @endif
                       @endforeach
                       @foreach($datapditotal AS $total)
                        @if(($total->id == $datas->id) && ($total->year == $datas->year))
                           <td>{{$total->average}}</td>
                        @endif
                       @endforeach
                    </tr>
                    @endforeach
                  </tbody>
</table>