<?php

#FIXME: Hardcoded url
$url = 'https://qa.tierragro.com:3001/api/membresia/obtener-membresias-reporte';

# Param only requires appCode.
$array_datos['ShopId'] = 'tierragro-dev.myshopify.com';
$array_datos['MembershipId'] = null;
$array_datos['ClientId'] = '6933039382690';
$array_datos['ClientName'] = 'Jonathan Steven Franco Gonzalez';
$array_datos['ClientEmail'] = 'jonathan.francog+782134@gmail.com';
$array_datos['ClientPhone'] = '3007851580';
$array_datos['ClientCity'] = 'Envigado';
$array_datos['ClientAddress'] = 'calle 36 sur #27 - 10 Entreparques Apto 912 calle 36 sur #27 - 10 Entreparques Apto 912';
$array_datos['ClientDoc'] = '1035920336';
$array_datos['Name'] = 'TIERRAGRO CLUB';
$array_datos['Status'] = 'Activa';
$array_datos['Description'] = 'MEMBRESIA TIERRAGRO';
$array_datos['Type'] = '1';
$array_datos['PetType'] = 'Perro';
$array_datos['PetWeight'] = '11-20KG';
$array_datos['PetSize'] = 'M';
$array_datos['OwnerSize'] = 'M';
$array_datos['QuantityOrders'] = 0;
$array_datos['Discount'] = 0.0;
$array_datos['InitialDate'] = '2023-03-16T15:28:31.268395-05:00';
$array_datos['FinalDate'] = '2024-03-16T15:28:31.268556-05:00';
$array_datos['BirthdatePet'] = null;
$array_datos['CreationDate'] = '2023-03-16T15:28:32.890091-05:00';
$array_datos['PartitionKey'] = '6933039382690';
$array_datos['RowKey'] = '2517233022856176869092342';
$array_datos['Timestamp'] = '0001-01-01T00:00:00+00:00';
$array_datos['ETag'] = 'W/\datetime 2023-03-16T20%3A28%3A41.4486111Z\ ';






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