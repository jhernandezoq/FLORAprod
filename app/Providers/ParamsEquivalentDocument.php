<?php
$paramsEquivalenDocument = [

	'types_discounts_charge_tax' => [
		'Impuesto',
		'Cargo',
		'Descuento',
	],

	'resolution_types' => [
		'Resolucion DIAN',
		'Rango interno para Nota',
	],

	'initSupportDocument' => '<?xml version = "1.0" encoding = "ISO-8859-1"?>
	<Invoice xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
	xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
	xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
	xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
	xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
	xmlns:sts="dian:gov:co:facturaelectronica:Structures-2-1"
	xmlns:xades="http://uri.etsi.org/01903/v1.3.2#"
	xmlns:xades141="http://uri.etsi.org/01903/v1.4.1#"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd">
	',

	'note_line' => 'Contrato de servicios AIU por concepto de: ',

	// 16.1.4.1 Procedencia de Vendedor: cbc:CustomizationID
	'CustomizationID' => [
		'Residente' => 10,
		//'No Residente' => 11,
	],

	// 16.1.1 Ambiente de Destino del Documento: cbc:ProfileExecutionID y cbc:UUID.@schemeID
	'ProfileExecutionID' => [
		'prod' => 1,
		'dev' => 2,
	],

	// 16.1.3 Tipo de Documento: cbc:InvoiceTypeCode y cbc:CreditnoteTypeCode
	'DocumentTypeCode' => [
		'Documento de soporte' => '05',
		//'Nota de ajuste' => '95',
	],

	// 16.2.3 Tipo de organización jurídica (Personas): cbc:AdditionalAccountID
	'AdditionalAccountID' => [
		'Persona Jurídica y asimiladas' => 1,
		'Persona Natural y asimiladas' => 2,
	],

	// Fuente Oficial de Codigos Postales: www.codigopostal.gov.co
	'link_codigos_postales' => 'www.codigopostal.gov.co',

	// 16.2.1 Documento de identificación
	'CompanyID_schemeName' => [
		'NIT' => 31,
		'Cédula de extranjería' => 22,
		'Tarjeta de extranjería' => 21,
		'Pasaporte' => 41,
		'Documento de identificación extranjero' => 42,
		'PEP' => 47,
		'NIT de otro país' => 50,
	],

	'sap_document_types' => [
		'NIT' => 31,
		'Cédula de extranjería' => 22,
		'Tarjeta de extranjería' => 21,
		'Pasaporte' => 41,
		'Documento de identificación extranjero' => 42,
		'PEP' => 47,
		'NIT de otro país' => 50,
	],

	// 16.2 Códigos para identificación fiscal ----- 16.2.2 Tributos
	'TaxScheme_Name' => [
		'IVA' => '01',
		'ReteIVA' => '05',
		'ReteRenta' => '06',
	],

	// 16.2.5.2 Para el grupo PartyTaxScheme ----- 16.2.2 Tributos
	'PartyTaxScheme_Name' => [
		'No aplica' => 'ZZ',
		'IVA' => '01',
	],

	// 16.2.5.1 Para el campo: cbc:TaxLevelCode
	'TaxLevelCode' => [
		'Gran contribuyente' => 'O-13',
		'Autorretenedor' => 'O-15',
		'Agente de retención IVA' => 'O-23',
		'Régimen simple de tributación' => 'O-47',
		'Impuesto sobre las ventas' => 'O-48',
		'No responsable de IVA' => 'O-49',
		'No aplica' => 'R-99-PN',
	],

	// 16.3.4.1 Formas de Pago: cbc:PaymentMeans/ID
	'PaymentMeans_ID' => [
		'Contado' => 1,
		'Crédito' => 2,
	],

	// 16.3.4.2 Medios de Pago: cbc:PaymentMeansCode
	// Pendiente Carlos nos confirme cuales seran....
	'payment_methods' => [
		'Efectivo' => '10',
		'Tarjeta Crédito' => '48',
		'Tarjeta Débito' => '49',
		'Transferencia Débito Bancaria' => '47',
	],

	// Cargo o descuento (No PDF)
	'ChargeIndicator' => [
		'Es un cargo' => 'true',
		'Es un descuento' => 'false',
	],

	// 16.3.6 Códigos de descuento
	'AllowanceChargeReasonCode' => [
		'Descuento no condicionado *' => '00',
		'Descuento condicionado **' => '01',
	],

	// 16.1.6 Forma de generación y transmisión
	'formsGenerationTransmission' => [
		'Por operación' => 1,
		//'Acumulado semanal' => 2,
	],

	// Información de las Compañías/Empresas nuestras
	'companies' => [
		'890912426' => [
			'name' => 'PEREZ Y CARDONA SAS',
			'nit' => '890912426',
			'schemeID_digito_verificacion' => 7,
			'AdditionalAccountID_tipoOrganizacionJuridica' => 1,
			'schemeName_tipoDocumento' => 31,
			// 16.2.5.1
			'TaxLevelCodes' => [
				'Autorretenedor' => 'O-15',
				'Impuesto sobre las ventas' => 'O-48',
			],
			'typeTribute' => '01',
			'dev' => [
				'url' => 'https://apivp.efacturacadena.com/staging/vp/documentos/proceso/sincrono',
				'token' => '64a650b0-e1dc-43cc-b90d-4f6caae599de',
			],
			'prod' => [
				'url' => 'https://apivp.efacturacadena.com/v1/vp/documentos/proceso/sincrono',
				'token' => '9f9b4217-81d6-41df-9308-8ebed0235dc7',
			],
		],
		'811046781' => [
			'name' => 'MP GALAGRO',
			'nit' => '811046781',
			'schemeID_digito_verificacion' => 4,
			'AdditionalAccountID_tipoOrganizacionJuridica' => 1,
			'schemeName_tipoDocumento' => 31,
			'TaxLevelCodes' => [
				'Autorretenedor' => 'O-15',
				'Impuesto sobre las ventas' => 'O-48',
			],
			'typeTribute' => '01',
			'dev' => [
				'url' => 'https://apivp.efacturacadena.com/staging/vp/documentos/proceso/sincrono',
				'token' => '64a650b0-e1dc-43cc-b90d-4f6caae599de',
			],
			'prod' => [
				'url' => 'https://apivp.efacturacadena.com/v1/vp/documentos/proceso/sincrono',
				'token' => '9f9b4217-81d6-41df-9308-8ebed0235dc7',
			],
		],
		'3519589' => [
			'name' => 'ANTONIO JOSE PEREZ',
			'nit' => '3519589',
			'schemeID_digito_verificacion' => 6,
			'AdditionalAccountID_tipoOrganizacionJuridica' => 1,
			'schemeName_tipoDocumento' => 31,
			'TaxLevelCodes' => [
				'No aplica' => 'R-99-PN',
			],
			'typeTribute' => 'ZZ',
			'dev' => [
				'url' => 'https://apivp.efacturacadena.com/staging/vp/documentos/proceso/sincrono',
				'token' => '1f234509-1686-4e2c-93a0-ddasewe7de',
			],
			'prod' => [
				'url' => 'https://apivp.efacturacadena.com/v1/vp/documentos/proceso/sincrono',
				'token' => '9f9b4217-81d6-41df-9308-8ebed0235dc7',
			],
		],
		'890938441' => [
			'name' => 'SUPERAGRO',
			'nit' => '890938441',
			'schemeID_digito_verificacion' => 0,
			'AdditionalAccountID_tipoOrganizacionJuridica' => 1,
			'schemeName_tipoDocumento' => 31,
			'TaxLevelCodes' => [
				'Impuesto sobre las ventas' => 'O-48',
			],
			'typeTribute' => '01',
			'dev' => [
				'url' => 'https://apivp.efacturacadena.com/staging/vp/documentos/proceso/sincrono',
				'token' => '9f9b4217-81d6-41df-9308-8ebed0235dc7',
			],
			'prod' => [
				'url' => 'https://apivp.efacturacadena.com/v1/vp/documentos/proceso/sincrono',
				'token' => '9f9b4217-81d6-41df-9308-8ebed0235dc7',
			],
		],
	],

	'percentageIvas' => [
		0, 5, 19,
	],
	'percentageReteRentas' => [
		0, 
		0.1, 
		0.5, 
		1,
		1.5,
		2,
		2.5,
		3,
		3.5,
		4,
		6,
		7,
		10,
		11,
		15,
		20,
		33,
	],

	// Info Constantes Compañías/Empresas nuestras
	'companies_const' => [
		'schemeAgencyID' => 195,
		'schemeAgencyName' => 'CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)',
	],

	// Final XML WebService CADENA
	// Pendiente por CADENA el valor de: "ShortName"
	'xml_data' => '<DATA>
	<UBL21>true</UBL21>
	<Development>
		<ShortName>DocSoporte</ShortName>
	</Development>
	<!--
	<Filters>
		<ReceiverBranch></ReceiverBranch> 
		<Batch></Batch>
		<Product></Product> 
		<SubProduct></SubProduct> 
	</Filters> -->
</DATA>
',

];
?>