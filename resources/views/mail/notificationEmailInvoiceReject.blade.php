<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Notificación de Factura</title>
</head>
<body>
	<h2>!Hola! {{$info[2]}}</h2>

	<p>La factura número: {{$info[0]}} del proveedor {{$info[3]}}, Ha sido rechazada. Te recuerdo los datos básicos de la factura para su pronta gestion:</p>

	<strong>Subtotal : </strong>${{number_format($info[5],0)}} {{$info[7]}}<br>
	<strong>Iva : </strong>${{number_format($info[6],0)}} {{$info[7]}}<br>
 	<strong>Total : </strong>${{number_format($info[4],0)}} {{$info[7]}}<br>
 	<strong>Vencimiento : </strong>{{$info[8]}}<br>
 	<strong>Motivo Rechazo : </strong>{{$info[9]}}<br>

 	<p>
 		flora, <br>
 		<i>Haciendo la vida de nuestros usuarios mas fácil.</i>
 	</p>
 	
</body>