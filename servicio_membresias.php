<?php

#FIXME: Hardcoded url
$url = 'https://qa.tierragro.com:3001/api/membresia/obtener-membresias-reporte';

# Param only requires appCode.
$array_datos['ShopId'] = 'tierragro-dev.myshopify.com';
$array_datos['MembershipId'] = 'null';
$array_datos['ClientId'] = '6933039382690';





$datos=json_encode($array_datos);
$url = 'https://qa.tierragro.com:3001/api/membresia/obtener-membresias-reporte';
$ch = curl_init($url);
curl_setopt_array($ch, array(
CURLOPT_CUSTOMREQUEST => "POST",
CURLOPT_POSTFIELDS => $datos,
CURLOPT_HTTPHEADER => array(
'Content-Type: application/json',
'x-token: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiZ29tb25rZXkiLCJpYXQiOjE2Nzg5NzY2ODQsImV4cCI6MTY3OTAxOTg4NH0.6GrM_K_KCFTlTIMFwH4SIme6X71HzhbL9fidy6hjIPM',
),
CURLOPT_RETURNTRANSFER => true,
));
$resultado = curl_exec($ch);
$json_data=json_decode($resultado, true);

var_dump($resultado);








/*$topup_data = array("appCode" => "KantoKrasKing");

#JSON encode the params
$topup_str   = json_encode($topup_data);

#Curl init
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

#FIXME: Hardcoded Access Token
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'X-Access-Token: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiZ29tb25rZXkiLCJpYXQiOjE2Nzg5NzY2ODQsImV4cCI6MTY3OTAxOTg4NH0.6GrM_K_KCFTlTIMFwH4SIme6X71HzhbL9fidy6hjIPM',
        'Content-Length: '.strlen($topup_str)
    )
);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $topup_str);
$result = curl_exec($ch);

#Ensure to close curl
curl_close($ch);

#For verbosity
print_r($result);*/

?>