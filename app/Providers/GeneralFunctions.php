<?php

$URLS_BASE = [
    'production' => 'https://flora.tierragro.com',
    //'dev' => 'http://3.238.161.180/flora/public',
    'dev' => 'http://172.31.113.4/flora/public',
    'local' => 'http://localhost/flora/public',
    //'local' => 'http://localhost/proyectos/flora_document/public',
];

function getUrlBase ( $URLS_BASE ) {
    //global $URLS_BASE;
    $url = $URLS_BASE['production'];

    if( $_SERVER['HTTP_HOST'] == 'localhost' || 
        $_SERVER['HTTP_HOST'] == 'http://localhost' )
    {
        $url = $URLS_BASE[ 'local' ];
    }
    else if( $_SERVER['HTTP_HOST'] == '172.31.113.4' || 
        $_SERVER['HTTP_HOST'] == 'http://172.31.113.4/flora/public' )
    {
        $url = $URLS_BASE[ 'dev' ];
    }
    return $url;
}

$getUrlBase = getUrlBase( $URLS_BASE );


function logg ( $str ) {
    echo '<script>console.log("'.$str.'");</script>';
}
$functionLogg = 'logg';


/**
 * Funcion que por medio del Valor/Value regresa el Label/Clave/Key
 */
function getArrayLabelByValue ( $base, $value ){
	if( ! isset( $value ) ) return '';
	$label = '';
	foreach( $base as $key => $val ){
   		if( $value == $val ) $label = $key;
	}
	return $label;
}
$getArrayLabelByValue = 'getArrayLabelByValue';

?>
