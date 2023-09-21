@extends('layouts.app')
@section('content')
<?php


    $serverName="192.168.0.24\MNG";
    $connectionInfo = array("Database"=>"ICGTIERRAGRO2017", "UID"=>"appsread", "PWD"=>"tierragro2021*", "CharacterSet"=>"UTF-8");
    $con = sqlsrv_connect( $serverName, $connectionInfo);       

    if($con){
        echo "conexion exitosa";
    }else{
        echo "fallo conexion";
}


if(isset($_GET['submit'])) 
{ 
$fecha1 = $_GET['fecha1']; 
$fecha2 = $_GET['fecha2']; 
}else{
  $fecha1 = '2000-10-10';
  $fecha2 = '2000-10-10';
}

?>
 
 
 

  <div class="container invoice-area">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Informe Rappi</div>
                      <form method="GET" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                      @csrf  
                      <table class="table">                      
                            <tr>
                              <th>
                                <div class="form-group col-sm-3"><br>
                                <label for="fechai">Fecha inicial:</label>
                                <input type="date" style="width: 200px;" name="fecha1" class="form-control" required="" />  
                                </div>
                              </th>
                              <th>
                                <div class="form-group col-sm-3"><br>
                                <label for="fechaf">Fecha final:</label>
                                <input type="date" style="width: 200px;" name="fecha2" class="form-control" required="" />
                                </div> 
                              </th>
                              <th>
                              <input type="submit" class="btn btn-primary" name="submit" value="Buscar">
                              </th>                                                      
                            </tr>                      
                        </table>
                    <?php
                    if ($fecha1 === "2000-10-10" && $fecha2 === "2000-10-10") {
                      echo "Ingrese las fechas para generar el informe";
                    }else{
                       echo "Informe generado desde <b>$fecha1</b> hasta <b>$fecha2</b>";
                    }
                   
                    ?>
                      </form>
                      <img src="../img/excel.png" style="width: 7%; height: 7%; margin-left: 1%;" onclick="GenerarExcel();">

                <div class="card-body">
                    
                <table class="table table-striped" id="directorio">
                        <thead>
                            <tr>
                              <th>Numero de serie</th>
                              <th>Numero de factura</th>
                              <th>Total neto</th>
                              <th>Descripcion</th>                              
                            </tr>
                        </thead>
                        <?php

                        $sql = "SELECT NUMSERIE, NUMFACTURA, TOTALNETO, C.DESCRIPCION ,FECHA FROM FACTURASVENTA A
                        INNER JOIN TESORERIA B
                        ON A.NUMSERIE = B.SERIE AND A.NUMFACTURA = B.NUMERO
                        INNER JOIN FORMASPAGO C
                        ON B.CODFORMAPAGO = C.CODFORMAPAGO
                        WHERE B.CODFORMAPAGO = 98 AND A.FECHA BETWEEN '{$fecha1}' AND '{$fecha2}'";

                        $stmt = sqlsrv_query( $con, $sql );
                        
                        if( $stmt === false) {
                            die( print_r( sqlsrv_errors(), true) );
                        }

                        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {  

                        ?>
                        <tr>
                              <td><?php echo $row["NUMSERIE"]?></td>
                              <td><?php echo $row["NUMFACTURA"]?></td>
                              <td><?php echo $row["TOTALNETO"]?></td>
                              <td><?php echo $row["DESCRIPCION"]?></td>                       
                        </tr>
                   <?php
                    }
                    sqlsrv_free_stmt( $stmt);
                   ?>
                        <tbody>                  
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">

  $(document).ready(function () {
   $('#user').select2();
   $('#profile').select2();
   $('#leader').select2();
   $('#ubication').select2();
});

function GenerarExcel(){
$(document).ready(function () {
    $("#directorio").table2excel({
        filename: "informeRappi.xls"
    });
});

}

</script>
@endsection
