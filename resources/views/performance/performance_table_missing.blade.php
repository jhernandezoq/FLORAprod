<table>
    <thead>
    <tr>
      <th>Nombre</th>
      <th>Cédula</th>
      <th>Cargo</th>
      <th>Líder</th>
      <th>Area</th>
    </tr>
    </thead>
                 <tbody>
                    @foreach($data AS $datas)
                    <tr>
                      <th>{{$datas->name}}</th>
                      <td>{{$datas->cedula}}</td>
                      <td>{{$datas->profile}}</td>
                      <td>{{$datas->lider}}</td>
                      <td>{{$datas->ubication}}</td>
                    </tr>
                    @endforeach
                  </tbody>
</table>