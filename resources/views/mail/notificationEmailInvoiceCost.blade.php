<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Notificación de Factura</title>
</head>
<body>
	<h2>!Hola! {{$name}}</h2>

	<p>La factura número: {{$invoice->number}} del proveedor {{$invoice->supplier->name}}, está pendiente por tu gestión. Por favor realiza la acción necesaria para darle continuidad al flujo. Te recuerdo los datos básicos de la factura:</p>

	<strong>Subtotal : </strong>${{number_format($invoice->subtotal,0)}} {{$invoice->currency}}<br>
 	<strong>Total : </strong>${{number_format($invoice->total,0)}} {{$invoice->currency}}<br>
 	<strong>Vencimiento : </strong>{{$invoice->due_date}}<br>
 	
 	<p>
 		flora, <br>
 		<i>Haciendo la vida de nuestros usuarios mas fácil.</i>
 	</p>
 	
</body>