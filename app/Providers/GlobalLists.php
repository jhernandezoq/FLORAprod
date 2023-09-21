<?php

require_once 'Countries.php';

$globalLists = [
	/*
	En BD solo tablas: departments, cities, quantity_units,
	En Archivo: currencies, languages, payment_methods.
	Temporalmentre se configura en archivo 
	""quantity_units" y por ahora No se usa "quantity_units desde BD" (JuanPablo)
	*/

	// Unidades de Medida (Desde BD: quantity_units)
	'quantity_units' => [
		'94 - Unidad' => 70,
	],

	'yesNo' => [
		'Seleccione..' => '',
		'Si' => 1,
		'No' => 0,
	],

	// 16.3.3 Moneda (ISO 4217): @currencyID
	// COP - USD - EURO
	'currencies' => [
		'No aplica',
		'COP',
		//'USD',
		//'EUR',
	],

	// 16.4.3 Municipios: cbc:CityName
	// BD => "departments"/"cities" Tabla de Departamentos / Municipios

	// 16.4.1 Países ==> IdentificationCode
	// Parametrizar los países necesarios
	'countries' => $countries_,

	// En los atributos languageID deberán ser utilizados los códigos de 2 letras de la ISO 639-1.
	// Parametrizar "es"/"en"
	'languages' => [
		'Español' => 'es',
		//'Ingles' => 'en',
	],

	// 

];
?>