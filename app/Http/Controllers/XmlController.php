<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use DB;
use App\DianResolution;
use App\DocumentLine;


class XmlController extends Controller
{



    public function haveData ( $txt ){
        $have_ = ( $txt != '' && $txt !== NULL && $txt != 0 )? true : false;
        return $have_;
    }

    public function xmlOnly( $tn = 1, $label, $value, $attrType = '', $atts = '', $attVal = '' )
    {
        $attributes = '';
        if( $attrType == 'vector' ){
            foreach( $atts as $key_ => $val ){
                $attributes .= ' '.$val.'="'.$attVal[$key_].'"';
            }
        }
        elseif( $attrType == 'una-linea' ){
            $attributes = ' '.$atts.'="'.$attVal.'"';
        }
        $xml = "\n<".$label.''.$attributes.'>' . $value ."</".$label.'>';
        return $xml;
    }

    public function xml2( $tn = 1, $label, $value, $attrType = '', $atts = '', $attVal = '' )
    {
        $attributes = '';
        if( $attrType == 'vector' ){
            foreach( $atts as $key_ => $val ){
                $attributes .= ' '.$val.'="'.$attVal[$key_].'"';
            }
        }
        elseif( $attrType == 'una-linea' ){
            $attributes = ' '.$atts.'="'.$attVal.'"';
        }

        $t = "\t";
        $tValue = "";
        if( $tn != 1 ){
            for( $i = 2; $i <= $tn; $i++ ){
                $t .= "\t";
                if( $tn >= 4 && $i >= 4 ) $tValue .= "\t";
            }
        }

        $xml = "\n".$t."<".$label.''.$attributes.'>
            ' . $tValue . $value 
        ."\n".$t."</".$label.'>';

        return $xml;
    }

    public function xml( $tn = 1, $label, $value, $attrType = '', $atts = '', $attVal = '' )
    {
        $attributes = '';
        if( $attrType == 'vector' ){
            foreach( $atts as $key_ => $val ){
                $attributes .= ' '.$val.'="'.$attVal[$key_].'"';
            }
        }
        elseif( $attrType == 'una-linea' ){
            $attributes = ' '.$atts.'="'.$attVal.'"';
        }

        $t = "\t";
        $tValue = "";
        if( $tn != 1 ){
            for( $i = 2; $i <= $tn; $i++ ){
                $t .= "\t";
                if( $tn >= 4 && $i >= 4 ) $tValue .= "\t";
            }
        }$t = '';$tValue = '';

        $xml = "\n".$t."<".$label.''.$attributes.'>' . $tValue . $value .$t."</".$label.'>';

        return $xml;
    }


    public function create( $document, $resolutionDian, $documentLines )
    {

        
        $xmlHead = '<?xml version = "1.0" encoding = "ISO-8859-1"?>
        <Invoice xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
        xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
        xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
        xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
        xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
        xmlns:sts="dian:gov:co:facturaelectronica:Structures-2-1"
        xmlns:xades="http://uri.etsi.org/01903/v1.3.2#"
        xmlns:xades141="http://uri.etsi.org/01903/v1.4.1#"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd">';
        $xmlEndData = '<DATA>
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
    </DATA>';
        $xmlHeadEnd = "\n" . $xmlEndData . "\n</Invoice>";
        $xml = '';

        $xml = $this->GeneralDS1( $document, $documentLines, $xml );

        $xml = $xmlHead . $xml . $xmlHeadEnd;

        $nameXML = 'xml1.xml';
        $pathDS = storage_path('app') . '/ds/' . $nameXML;

        
        $file = fopen( $pathDS, 'w');
        fwrite($file, $xml . PHP_EOL);
        fclose($file);

        return $pathDS;
    }


    public function GeneralDS1 ( $doc, $documentLines, $xml )
    {
        /*
        Info a mostrar en el XML
        */
        $xml_general = true;
        $xml_supplier = true;
        $xml_my_company = true;
        $xml_info_payment = true;
        $xml_charge_discount = true;
        $xml_exchage_rate = true;
        $xml_tax_iva = true;
        $xml_retens = true;
        $xml_totals = true;
        $xml_lines = true;

        $xmlGen1 = '';

        // INFO DOC SOPORTE  -----------------------------------
        if( $xml_general )
        {
            
            $xmlGen1 .= $this->xml( 1, 'cbc:CustomizationID', $doc->resident );
            $xmlGen1 .= $this->xml( 1, 'cbc:ProfileExecutionID', $doc->environment );
            $xmlGen1 .= $this->xmlOnly( 1, 'cbc:ID', $doc->document_number );
            $xmlGen1 .= $this->xmlOnly( 1, 'cbc:IssueDate', $doc->date_transaction );
            $xmlGen1 .= $this->xmlOnly( 1, 'cbc:IssueTime', $doc->time_transaction );
            //$xmlGen1 .= $this->xml( 1, 'cbc:DueDate', $doc->date_due );
            $xmlGen1 .= $this->xml( 1, 'cbc:InvoiceTypeCode', $doc->type_code );

            if( $this->haveData( $doc->note ) )
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->note );
            
            
            $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->value_letters, 
                'una-linea','languageLocaleID','ValorLetras',
            );

            if( $this->haveData( $doc->observations ) ){
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->observations, 
                    'vector',
                    ['languageLocaleID'],
                    ['Observaciones'],
                );
            }

            if( $this->haveData( $doc->note1 ) ){
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->note1, 
                    'una-linea', 'languageLocaleID', 'Nota 1',
                );
            }
            if( $this->haveData( $doc->note2 ) ){
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->note2, 
                    'una-linea', 'languageLocaleID', 'Nota 2',
                );
            }
            if( $this->haveData( $doc->note3 ) ){
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->note3, 
                    'una-linea', 'languageLocaleID', 'Nota 3',
                );
            }
            if( $this->haveData( $doc->note4 ) ){
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->note4, 
                    'una-linea', 'languageLocaleID', 'Nota 4',
                );
            }
            if( $this->haveData( $doc->note5 ) ){
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->note5, 
                    'una-linea', 'languageLocaleID', 'Nota 5',
                );
            }

            $xmlGen1 .= $this->xml( 1, 'cbc:DocumentCurrencyCode', $doc->currency );
            $xmlGen1 .= $this->xml( 1, 'cbc:LineCountNumeric', $doc->total_lines );

            // ORDEN COMPRA y ANTICIPO -----------------------------------
            if( $this->haveData( $doc->have_purchase_order ) ){
                $order_number = $this->xml( 2, 'cbc:ID', $doc->order_number );
                $order_date = $this->xml( 2, 'cbc:IssueDate', $doc->order_date );
                $xmlGen1 .= $this->xml( 1, 'cac:OrderReference', $order_number . $order_date );
            }
            /*
            if( $this->haveData( $doc->have_advance ) ){
                $advance_number = $this->xml( 2, 'cbc:ID', $doc->advance_number );
                $advance_date = $this->xml( 2, 'cbc:IssueDate', $doc->advance_date );
                $xmlGen1 .= $this->xml( 1, 'cac:OrderReference', $advance_number . $advance_date );
            } */
            

            // NOTA DE AJUSTE Relacionada -----------------------------------
            if( $this->haveData( $doc->document_id ) ){
                // 1 -> <cac:BillingReference> -> 2 -> <cbc:InvoiceDocumentReference>
                $cbcID = $this->xml( 3, 'cbc:ID', $doc->document_number_note );
                $cbcUUID = $this->xml( 3, 'cbc:UUID', 
                    $doc->cuds, 
                    'vector',
                    [ 'schemeName', ], 
                    [ config('app.equivalenDocument')['companies_const']['schemeAgencyName'], ],
                );
                $IssueDate = $this->xml( 3, 'cbc:IssueDate', $doc->date_send_ds );

                $InvoiceDocumentReference = $this->xml( 2, 'cbc:InvoiceDocumentReference', $cbcID 
                    . $cbcUUID 
                    . $IssueDate 
                );

                $BillingReference = $this->xml( 1, 'cac:BillingReference', $InvoiceDocumentReference );

                $xmlGen1 .= $BillingReference;
            }
        }

        // INFO PROVEEDOR DS -----------------------------------
        if( $xml_supplier ){
            $xmlGen1 .= $this->infoSupplierDS( $doc, $documentLines, $xml );
        }

        // INFO RECEPTOR (Nosotros) DS -------------------------
        if( $xml_my_company ){
            $xmlGen1 .= $this->infoMyCompanyDS( $doc, $documentLines, $xml );
        }

        // MEDIOS DE PAGO DS ------------------------------------
        if( $xml_info_payment ){
            $xmlGen1 .= $this->infoPaymentDS( $doc, $documentLines, $xml );
        }

        // CARGOS / DESCUENTOS 1 DS -----------------------------
        //$arrayDiscountCharge = [ 'xml_items_discount_cargos' => '' ];
        $arrayDiscountCharge = [
            'charge' => [],
            'discount' => [],
        ];
        $arrayDiscountCharge = $this->infoDiscountChargeDS( $doc, $documentLines, $xml );
        if( $xml_charge_discount ){
            $xmlGen1 .= $arrayDiscountCharge['xml'];
        }

        // TASAS DE CAMBIO DS -----------------------------------
        if( $xml_exchage_rate ){
            $xmlGen1 .= $this->infoExchangesRateDS( $doc, $documentLines, $xml );
        }

        // IVAs DS  ---------------------------------------------
        $arrayIva = [
            'sum_ivas' => 0,
            'xml_iva' => [],
        ];
        $arrayIva = $this->infoTaxIvaDS( $doc, $documentLines, $xml );
        if( $xml_tax_iva ){
            $xmlGen1 .= $arrayIva['xml'];
        }

        // RETENCIONES DS  --------------------------------------
        $arrayRetens = [
            'rete_renta' => [],
            'rete_iva' => '',
        ];
        $arrayRetens = $this->infoRetensDS( $doc, $documentLines, $xml, $arrayIva['sum_ivas'] );
        if( $xml_retens ){
            $xmlGen1 .= $arrayRetens['xml'];
        }

        // TOTALES DS  ------------------------------------------
        if( $xml_totals ){
            $xmlGen1 .= $this->infoTotalsDS( $doc, $documentLines, $xml );
        }

        // LINEA DE DETALLE DS  ---------------------------------
        if( $xml_lines ){
            $xmlGen1 .= $this->infoDetailLinesDS( $doc, $documentLines, $xml, $arrayDiscountCharge, 
                $arrayIva, $arrayRetens 
            );
        }

        return $xmlGen1;
    }


    public function infoSupplierDS ( $doc, $documentLines, $xml )
    {
        $companies_const = array(
            'schemeAgencyID' => 195,
            'schemeAgencyName' => 'CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)'
        );
        //$companies = config('app.equivalenDocument')['companies'];
        //$company = $companies[ $doc->supplier_document ];

        // <cac:AccountingSupplierParty> -> 
        $supplier_legal_organization = $this->xml( 2, 'cbc:AdditionalAccountID', $doc->supplier_legal_organization );

        // <cac:AccountingSupplierParty> -> <cac:Party> <cac:PhysicalLocation> <cac:Address> -> 
        $city_code = $this->xml( 5, 'cbc:ID', $doc->city_code );
        $city = $this->xml( 5, 'cbc:CityName', $doc->city );

        $PostalZone = '';
        if( $this->haveData( $doc->supplier_postal_code ) ){
            $PostalZone = $this->xml( 5, 'cbc:PostalZone', $doc->supplier_postal_code );
        }
    
        $department_code = $this->xml( 5, 'cbc:CountrySubentityCode', $doc->department_code );
        $department = $this->xml( 5, 'cbc:CountrySubentity', $doc->department );

        // 5 -> <cac:AddressLine> ->
        $supplier_address = $this->xml( 6, 'cbc:Line', $doc->supplier_address );
        $AddressLine = $this->xml( 5, 'cac:AddressLine', $supplier_address );

        // 5 -> <cac:Country> ->
        $codigo_alfa_2 = 'CO';
        $IdentificationCode = $this->xml( 6, 'cbc:IdentificationCode', $codigo_alfa_2 );
        $supplier_country = $this->xml( 6, 'cbc:Name', $doc->supplier_country, 
            'una-linea','languageID', $doc->language,
        );
        
        $Country = $this->xml( 5, 'cac:Country', $IdentificationCode . $supplier_country );

        $Address = $this->xml( 4, 'cac:Address', $city_code 
            . $city  
            . $PostalZone  
            . $department  
            . $department_code  
            . $AddressLine  
            . $Country  
        );

        $PhysicalLocation = $this->xml( 3, 'cac:PhysicalLocation', $Address );

        // 3 -> <cac:PartyTaxScheme> ----------------------
        $RegistrationName = $this->xml( 4, 'cbc:RegistrationName', $doc->supplier_reason );

        $CompanyID = $this->xml( 4, 'cbc:CompanyID', 
            $doc->supplier_document, 
            'vector',
            [ 'schemeID','schemeName','schemeAgencyID','schemeAgencyName', ], 
            [
                $doc->supplier_verification_digit,
                $doc->supplier_document_type,
                $companies_const['schemeAgencyID'],
                $companies_const['schemeAgencyName'],
            ],
        );

        $TaxLevelCode = $this->xml( 4, 'cbc:TaxLevelCode', 
            $doc->supplier_fiscal_obligations_code, 
            'una-linea', 'listName', 'No aplica',
        );

        // 4 -> <cac:TaxScheme>
        $tax_code = getArrayLabelByValue(
            array('No aplica' => 'ZZ',
            'IVA' => '01',), 
            $doc->supplier_tribute
        );
        $cbcID = $this->xml( 5, 'cbc:ID', $doc->supplier_tribute );
        $cbcName = $this->xml( 5, 'cbc:Name', $tax_code );
        $TaxScheme = $this->xml( 4, 'cac:TaxScheme', $cbcID . $cbcName );


        $PartyTaxScheme = $this->xml( 3, 'cac:PartyTaxScheme', $RegistrationName 
            . $CompanyID 
            . $TaxLevelCode 
            . $TaxScheme 
        );
        // ----------

        $Party = $this->xml( 2, 'cac:Party', 
            $PhysicalLocation 
            . $PartyTaxScheme
        );

        $AccountingSupplierParty = $this->xml( 1, 'cac:AccountingSupplierParty', 
            $supplier_legal_organization 
            . $Party 
        );

        return $AccountingSupplierParty;
    }

    function getArrayLabelByValue ( $base, $value ){
        if( ! isset( $value ) ) return '';
        $label = '';
        foreach( $base as $key => $val ){
               if( $value == $val ) $label = $key;
        }
        return $label;
    }


    public function infoMyCompanyDS ( $doc, $documentLines, $xml )
    {
        $companies_const = array(
            'schemeAgencyID' => 195,
            'schemeAgencyName' => 'CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)'
        );

        // 1 -> <cac:AccountingCustomerParty>
        $AdditionalAccountID = $this->xml( 2, 'cbc:AdditionalAccountID', $doc->legal_organization );

        // 2 -> <cac:Party> <cac:PartyTaxScheme> 
        $RegistrationName = $this->xml( 4, 'cbc:RegistrationName', $doc->reason );

        $CompanyID = $this->xml( 4, 'cbc:CompanyID', 
            $doc->document, 
            'vector',
            [ 'schemeID','schemeName','schemeAgencyID','schemeAgencyName', ], 
            [
                $doc->verification_digit,
                $doc->document_type,
                $companies_const['schemeAgencyID'],
                $companies_const['schemeAgencyName'],
            ],
        );

        $TaxLevelCode = $this->xml( 4, 'cbc:TaxLevelCode', 
            $doc->fiscal_obligations_code, 
            'una-linea', 'listName', 'No aplica',
        );

        // 4 -> <cac:TaxScheme> 
        $tax_code = getArrayLabelByValue(
            array('No aplica' => 'ZZ',
            'IVA' => '01',), 
            $doc->tribute
        );
        $cbcID = $this->xml( 5, 'cbc:ID', $doc->tribute );
        $cbcName = $this->xml( 5, 'cbc:Name', $tax_code );
        $TaxScheme = $this->xml( 4, 'cac:TaxScheme', $cbcID . $cbcName );

        $PartyTaxScheme = $this->xml( 3, 'cac:PartyTaxScheme', 
            $RegistrationName 
            . $CompanyID 
            . $TaxLevelCode
            . $TaxScheme 
        );

        // 3 -> <cac:PartyLegalEntity> <cac:CorporateRegistrationScheme>
        $cbcID = $this->xmlOnly( 5, 'cbc:ID', $doc->document_prefix );
        $CorporateRegistrationScheme = $this->xml( 4, 'cac:CorporateRegistrationScheme', $cbcID );
        $PartyLegalEntity = $this->xml( 3, 'cac:PartyLegalEntity', $CorporateRegistrationScheme );

        $Party = $this->xml( 2, 'cac:Party', 
            $PartyTaxScheme 
            . $PartyLegalEntity 
        );

        $AccountingCustomerParty = $this->xml( 1, 'cac:AccountingCustomerParty', 
            $AdditionalAccountID 
            . $Party 
        );

        return $AccountingCustomerParty;
    }


    public function infoPaymentDS ( $doc, $documentLines, $xml )
    {
        // 1 -> <cac:PaymentMeans>
        $cbcID = $this->xml( 2, 'cbc:ID', $doc->way_pay );
        $PaymentMeansCode = $this->xml( 2, 'cbc:PaymentMeansCode', $doc->payment_method );

        $PaymentDueDate = '';
        if( $this->haveData( $doc->date_due_payment ) )
            $PaymentDueDate .= $this->xml( 2, 'cbc:PaymentDueDate', $doc->date_due_payment );
        
     /*   $payment_method = config('app.getArrayLabelByValue')(
            config('app.equivalenDocument')['payment_methods'], 
            $doc->payment_method,
        );*/

        $payment_method = getArrayLabelByValue(
            array('Efectivo' => '10',
            'Tarjeta Crédito' => '48',
            'Tarjeta Débito' => '49',
            'Transferencia Débito Bancaria' => '47'), 
            $doc->payment_method
        );
    
        $PaymentID = $this->xml( 2, 'cbc:PaymentID', $payment_method );

        $PaymentMeans = $this->xml( 1, 'cac:PaymentMeans', $cbcID 
            . $PaymentMeansCode 
            . $PaymentDueDate 
            . $PaymentID 
        );

        return $PaymentMeans;
    }



    /*
    * Discriminado por % de Descuentos (No en usar ?)
    */
    private function sumDiscounts( $listDiscounts, $doc )
    {
        $xml_ = '';
        $countId = 0;
        foreach( $listDiscounts as $percentage => $rowItem )
        {
            $countId++;
            $base = $rowItem['base'];
            $perc = $rowItem['perc'];

            // 1 -> <cac:AllowanceCharge>
            $cbcID = $this->xml( 2, 'cbc:ID', $countId );
            $ChargeIndicator = $this->xml( 2, 'cbc:ChargeIndicator', 'false' );
            $AllowanceChargeReasonCode = $this->xml( 2, 'cbc:AllowanceChargeReasonCode', '01' );
            
            $discountDescription = config('app.getArrayLabelByValue')(
                config('app.equivalenDocument')['AllowanceChargeReasonCode'], 
                '01',
            );
            $AllowanceChargeReason = $this->xml( 2, 'cbc:AllowanceChargeReason', $discountDescription );

            $MultiplierFactorNumeric = $this->xml( 2, 'cbc:MultiplierFactorNumeric', $percentage );

            $Amount = $this->xml( 2, 'cbc:Amount', 
                $perc . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ],
            );

            $BaseAmount = $this->xml( 2, 'cbc:BaseAmount', 
                $base . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ],
            );

            $AllowanceCharge = $this->xml( 1, 'cac:AllowanceCharge', $cbcID 
                . $ChargeIndicator 
                . $AllowanceChargeReasonCode 
                . $AllowanceChargeReason 
                . $MultiplierFactorNumeric 
                . $Amount 
                . $BaseAmount 
            );
            $xml_ .= $AllowanceCharge;
        }
        return $xml_;
    }

    public function infoDiscountChargeDS ( $doc, $documentLines, $xml )
    {
        $listDiscounts = [];
        $xml_ = '';
        $xml_items = [
            'charge' => [],
            'discount' => [],
        ];
        $countId = 0;
        foreach ( $documentLines as $index => $dLine )        
        {
            if( $dLine->perc_charge > 0 )
            {
                $countId++;

                // 1 -> <cac:AllowanceCharge>
                $cbcID = $this->xml( 2, 'cbc:ID', $countId );
                $ChargeIndicator = $this->xml( 2, 'cbc:ChargeIndicator', 'true' );
                $AllowanceChargeReasonCode = $this->xml( 2, 'cbc:AllowanceChargeReasonCode', '01' );
                
                $discountDescription = config('app.getArrayLabelByValue')(
                    config('app.equivalenDocument')['AllowanceChargeReasonCode'], 
                    '01',
                );
                $AllowanceChargeReason = $this->xml( 2, 'cbc:AllowanceChargeReason', $discountDescription );

                $MultiplierFactorNumeric = $this->xml( 2, 'cbc:MultiplierFactorNumeric', $dLine->perc_charge );

                $tt_bruto = $dLine->quantity * $dLine->price;
                $tt_charge = $tt_bruto * ( $dLine->perc_charge / 100 );
                
                $Amount = $this->xml( 2, 'cbc:Amount', 
                    $tt_charge . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );
                
                $BaseAmount = $this->xml( 2, 'cbc:BaseAmount', 
                    $tt_bruto . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );
                
                $AllowanceCharge = $this->xml( 1, 'cac:AllowanceCharge', $cbcID 
                    . $ChargeIndicator 
                    . $AllowanceChargeReasonCode 
                    . $AllowanceChargeReason 
                    . $MultiplierFactorNumeric 
                    . $Amount 
                    . $BaseAmount 
                );
                $xml_ .= $AllowanceCharge;


                // Items -----------------------------------------------------------
                // 2 -> <cac:AllowanceCharge>
                $cbcID = $this->xml( 3, 'cbc:ID', $countId );
                $ChargeIndicator = $this->xml( 3, 'cbc:ChargeIndicator', 'true' );
                
                $AllowanceChargeReason = $this->xml( 3, 'cbc:AllowanceChargeReason', $discountDescription );

                $MultiplierFactorNumeric = $this->xml( 3, 'cbc:MultiplierFactorNumeric', $dLine->perc_charge );
                
                $Amount = $this->xml( 3, 'cbc:Amount', 
                    $tt_charge . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );
                
                $BaseAmount = $this->xml( 3, 'cbc:BaseAmount', 
                    $tt_bruto . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );
                $AllowanceCharge = $this->xml( 2, 'cac:AllowanceCharge', $cbcID 
                    . $ChargeIndicator 
                    . $AllowanceChargeReason 
                    . $MultiplierFactorNumeric 
                    . $Amount 
                    . $BaseAmount 
                );
                $xml_items['charge'][ $index ] = $AllowanceCharge;
            }

            if( $dLine->perc_discount > 0 )
            {
                $countId++;
                $tt_bruto = $dLine->quantity * $dLine->price;
                $tt_discount = $tt_bruto * ( $dLine->perc_discount / 100 );

                if( ! isset( $listDiscounts[ $dLine->perc_discount ] ) ){
                    $rowItem = [
                        'base'=> $tt_bruto,
                        'perc'=> $tt_discount,
                    ];
                    $listDiscounts[ $dLine->perc_discount ] = $rowItem;
                }
                else{
                    $listDiscounts[ $dLine->perc_discount ]['base'] = $listDiscounts[ $dLine->perc_discount ]['base'] + $tt_bruto;

                    $listDiscounts[ $dLine->perc_discount ]['perc'] = $listDiscounts[ $dLine->perc_discount ]['perc'] + $tt_discount;
                }

                /** Si debe discriminar comentar hasta prox comentario */
                // 1 -> <cac:AllowanceCharge>
                $cbcID = $this->xml( 2, 'cbc:ID', $countId );
                $ChargeIndicator = $this->xml( 2, 'cbc:ChargeIndicator', 'false' );
                $AllowanceChargeReasonCode = $this->xml( 2, 'cbc:AllowanceChargeReasonCode', '01' );
                
                $discountDescription = config('app.getArrayLabelByValue')(
                    config('app.equivalenDocument')['AllowanceChargeReasonCode'], 
                    '01',
                );
                $AllowanceChargeReason = $this->xml( 2, 'cbc:AllowanceChargeReason', $discountDescription );

                $MultiplierFactorNumeric = $this->xml( 2, 'cbc:MultiplierFactorNumeric', $dLine->perc_discount );

                //$tt_bruto = $dLine->quantity * $dLine->price;
                //$tt_discount = $tt_bruto * ( $dLine->perc_discount / 100 );

                $Amount = $this->xml( 2, 'cbc:Amount', 
                    $tt_discount . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );

                $BaseAmount = $this->xml( 2, 'cbc:BaseAmount', 
                    $tt_bruto . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );

                $AllowanceCharge = $this->xml( 1, 'cac:AllowanceCharge', $cbcID 
                    . $ChargeIndicator 
                    . $AllowanceChargeReasonCode 
                    . $AllowanceChargeReason 
                    . $MultiplierFactorNumeric 
                    . $Amount 
                    . $BaseAmount 
                );
                $xml_ .= $AllowanceCharge;
                /** Comentar hasta aca si debe discriminar */


                // Items -----------------------------------------------------------
                // 2 -> <cac:AllowanceCharge>
                $cbcID = $this->xml( 3, 'cbc:ID', $countId );
                $ChargeIndicator = $this->xml( 3, 'cbc:ChargeIndicator', 'false' );
                
                $discountDescription = config('app.getArrayLabelByValue')(
                    config('app.equivalenDocument')['AllowanceChargeReasonCode'], 
                    '01',
                );
                $AllowanceChargeReason = $this->xml( 3, 'cbc:AllowanceChargeReason', $discountDescription );

                $MultiplierFactorNumeric = $this->xml( 3, 'cbc:MultiplierFactorNumeric', $dLine->perc_discount );

                $Amount = $this->xml( 3, 'cbc:Amount', 
                    $tt_discount . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );

                $BaseAmount = $this->xml( 3, 'cbc:BaseAmount', 
                    $tt_bruto . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );

                $AllowanceCharge = $this->xml( 2, 'cac:AllowanceCharge', $cbcID 
                    . $ChargeIndicator 
                    . $AllowanceChargeReason 
                    . $MultiplierFactorNumeric 
                    . $Amount 
                    . $BaseAmount 
                );
                $xml_items['discount'][ $index ] = $AllowanceCharge;
            }
        }

        /*
        * Discriminado por % de Descuentos
        $xml_ = $this->sumDiscounts( $listDiscounts, $doc ); */

        return [
            'xml' => $xml_, 
            //'xml_items_discount_cargos' => $xml_items,
            'charge' => $xml_items['charge'],
            'discount' => $xml_items['discount'],
        ];
    }


    public function infoExchangesRateDS ( $doc, $documentLines, $xml )
    {
        $xml_ = '';
        if( $doc->base_currency_init != 'No aplica' )
        {
            // 1 -> <cac:PaymentExchangeRate>
            $SourceCurrencyCode = $this->xml( 2, 'cbc:SourceCurrencyCode', $doc->base_currency_init );
            $SourceCurrencyBaseRate = $this->xml( 2, 'cbc:SourceCurrencyBaseRate', $doc->monetary_base_init );
            $TargetCurrencyCode = $this->xml( 2, 'cbc:TargetCurrencyCode', $doc->base_currency_end );
            $TargetCurrencyBaseRate = $this->xml( 2, 'cbc:TargetCurrencyBaseRate', $doc->monetary_base_end );
            $CalculationRate = $this->xml( 2, 'cbc:CalculationRate', $doc->exchange_rate_value );
            $Date = $this->xml( 2, 'cbc:Date', $doc->date_exchange_payment );

            $PaymentExchangeRate = $this->xml( 1, 'cac:PaymentExchangeRate', $SourceCurrencyCode 
                . $SourceCurrencyBaseRate 
                . $TargetCurrencyCode 
                . $TargetCurrencyBaseRate 
                . $CalculationRate 
                . $Date 
            );
            $xml_ = $PaymentExchangeRate;

            if( $doc->base_currency_init2 != 'No aplica' )
            {
                // 1 -> <cac:PaymentAlternativeExchangeRate>
                $SourceCurrencyCode = $this->xml( 2, 'cbc:SourceCurrencyCode', $doc->base_currency_init2 );
                $SourceCurrencyBaseRate = $this->xml( 2, 'cbc:SourceCurrencyBaseRate', $doc->monetary_base_init2 );
                $TargetCurrencyCode = $this->xml( 2, 'cbc:TargetCurrencyCode', $doc->base_currency_end2 );
                $TargetCurrencyBaseRate = $this->xml( 2, 'cbc:TargetCurrencyBaseRate', $doc->monetary_base_end2 );
                $CalculationRate = $this->xml( 2, 'cbc:CalculationRate', $doc->exchange_rate_value2 );
                $Date = $this->xml( 2, 'cbc:Date', $doc->date_exchange_payment2 );

                $PaymentAlternativeExchangeRate = $this->xml( 1, 'cac:PaymentAlternativeExchangeRate', $SourceCurrencyCode 
                    . $SourceCurrencyBaseRate 
                    . $TargetCurrencyCode 
                    . $TargetCurrencyBaseRate 
                    . $CalculationRate 
                    . $Date 
                );
                $xml_ .= $PaymentAlternativeExchangeRate;
            }
        }
        return $xml_;
    }



    /** Discriminacion de IVAs por % */
    private function sumTaxIvas( $listTaxs, $sum_ivas, $doc )
    {
        $xml_ = '';
        $TaxAmount = $this->xml( 2, 'cbc:TaxAmount',
            $sum_ivas . '.00', 
            'vector',
            [ 'currencyID', ], 
            [ $doc->currency, ],
        );
        foreach( $listTaxs as $perc_tax => $rowTax )
        {
            $base = $rowTax['base'];
            $iva = $rowTax['iva'];

            /*
            // 1 -> <cac:TaxTotal>
            $TaxAmount = $this->xml( 2, 'cbc:TaxAmount', // En XML está 2 Veces (Difieren!)
                $iva . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ],
            ); */

            // 2 -> <cac:TaxSubtotal>
            $TaxableAmount = $this->xml( 3, 'cbc:TaxableAmount', 
                $base . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ],
            );

            $TaxAmountIva = $this->xml( 2, 'cbc:TaxAmount',
                $iva . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ],
            );
            
            // 3 -> <cac:TaxCategory> - 
            $Percent = $this->xml( 4, 'cbc:Percent', $perc_tax );

            // 4 -> <cac:TaxScheme> - 
            $IVA_ = 'IVA';
            $iva_code = config('app.equivalenDocument')['TaxScheme_Name'][ $IVA_ ];
            $cbcID = $this->xml( 5, 'cbc:ID', $iva_code );
            $cbcName = $this->xml( 5, 'cbc:Name', $IVA_ );

            $TaxScheme = $this->xml( 4, 'cac:TaxScheme', $cbcID 
                . $cbcName 
            );
            $TaxCategory = $this->xml( 3, 'cac:TaxCategory', $Percent
                . $TaxScheme 
            );
            $TaxSubtotal = $this->xml( 2, 'cac:TaxSubtotal', $TaxableAmount 
                . $TaxAmountIva 
                . $TaxCategory 
            );
            /*
            $TaxTotal = $this->xml( 1, 'cac:TaxTotal', $TaxAmount 
                . $TaxSubtotal 
            );
            $xml_ .= $TaxTotal; */
            $xml_ .= $TaxSubtotal;
        }
        $TaxTotal = $this->xml( 1, 'cac:TaxTotal', $TaxAmount 
            . $xml_ 
        );
        $xml_ = $TaxTotal;

        return $xml_;
    }

    public function infoTaxIvaDS ( $doc, $documentLines, $xml )
    {
        $listTaxs = [];
        $sum_ivas = 0;
        $xml_ = '';
        $xml_items = [
            'xml_iva' => []
        ];
        $countId = 0;
        foreach ( $documentLines as $index => $dLine )        
        {
            if( $dLine->perc_tax > 0 )
            {
                $tt_bruto = $dLine->quantity * $dLine->price;
                $sub_tt = $tt_bruto;
                if( $dLine->perc_discount > 0 )
                    $sub_tt = $tt_bruto - ( $tt_bruto * ( $dLine->perc_discount / 100 ) );
                
                $tt_value = $sub_tt * ( $dLine->perc_tax / 100 );
                $sum_ivas = $sum_ivas + $tt_value;

                if( ! isset( $listTaxs[ $dLine->perc_tax ] ) ){
                    $rowTax = [
                        'base'=> $sub_tt,
                        'iva'=> $tt_value,
                    ];
                    $listTaxs[ $dLine->perc_tax ] = $rowTax;
                }
                else{
                    $listTaxs[ $dLine->perc_tax ]['base'] = $listTaxs[ $dLine->perc_tax ]['base'] + $sub_tt;

                    $listTaxs[ $dLine->perc_tax ]['iva'] = $listTaxs[ $dLine->perc_tax ]['iva'] + $tt_value;
                }
                
                /*
                // 1 -> <cac:TaxTotal>
                $TaxAmount = $this->xml( 2, 'cbc:TaxAmount', // En XML está 2 Veces
                    $tt_value . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );

                // 2 -> <cac:TaxSubtotal>
                $TaxableAmount = $this->xml( 3, 'cbc:TaxableAmount', 
                    $sub_tt . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );
                
                // 3 -> <cac:TaxCategory> - 
                $Percent = $this->xml( 4, 'cbc:Percent', $dLine->perc_tax );

                // 4 -> <cac:TaxScheme> - 
                $IVA = 'IVA';
                $iva_code = config('app.equivalenDocument')['TaxScheme_Name'][ $IVA ];
                $cbcID = $this->xml( 5, 'cbc:ID', $iva_code );
                $cbcName = $this->xml( 5, 'cbc:Name', $IVA );

                $TaxScheme = $this->xml( 4, 'cac:TaxScheme', $cbcID 
                    . $cbcName 
                );
                $TaxCategory = $this->xml( 3, 'cac:TaxCategory', $Percent
                    . $TaxScheme 
                );
                $TaxSubtotal = $this->xml( 2, 'cac:TaxSubtotal', $TaxableAmount 
                    . $TaxAmount 
                    . $TaxCategory 
                );

                $TaxTotal = $this->xml( 1, 'cac:TaxTotal', $TaxAmount 
                    . $TaxSubtotal 
                );
                $xml_ .= $TaxTotal; */


                // Items -----------------------------------------------------------
                // 2 -> <cac:TaxTotal> 
                $TaxAmount = $this->xml( 3, 'cbc:TaxAmount', // En XML está 2 Veces
                    $tt_value . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );

                // 3 -> <cac:TaxSubtotal>
                $TaxableAmount = $this->xml( 4, 'cbc:TaxableAmount', 
                    $sub_tt . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );
                
                // 4 -> <cac:TaxCategory> - 
                $Percent = $this->xml( 5, 'cbc:Percent', $dLine->perc_tax );

                // 5 -> <cac:TaxScheme> - 
                $IVA = 'IVA';
                $iva_code = config('app.equivalenDocument')['TaxScheme_Name'][ $IVA ];
                $cbcID = $this->xml( 6, 'cbc:ID', $iva_code );
                $cbcName = $this->xml( 6, 'cbc:Name', $IVA );

                $TaxScheme = $this->xml( 5, 'cac:TaxScheme', $cbcID 
                    . $cbcName 
                );
                $TaxCategory = $this->xml( 4, 'cac:TaxCategory', $Percent
                    . $TaxScheme 
                );
                $TaxSubtotal = $this->xml( 3, 'cac:TaxSubtotal', $TaxableAmount 
                    . $TaxAmount 
                    . $TaxCategory 
                );
                $TaxTotal = $this->xml( 2, 'cac:TaxTotal', $TaxAmount 
                    . $TaxSubtotal 
                );

                $xml_items['xml_iva'][ $index ] = $TaxTotal;
            }
        }

        if( $sum_ivas != 0 ){
            $xml_ = $this->sumTaxIvas( $listTaxs, $sum_ivas, $doc );
        }

        return [
            'xml' => $xml_, 
            'sum_ivas' => $sum_ivas,
            //'xml_items_ivas' => $xml_items,
            'xml_iva' => $xml_items['xml_iva'],
        ];
    }


    private function sumRetens( $listRetens, $doc )
    {
        $xml_ = '';
        $countId = 0;
        foreach( $listRetens as $percentage => $rowItem )
        {
            $countId++;
            $base = $rowItem['base'];
            $perc = $rowItem['perc'];

            // 1 -> <cac:WithholdingTaxTotal>
            $TaxAmount = $this->xml( 2, 'cbc:TaxAmount', // En XML está 2 Veces
                $perc . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ],
            );

            // 2 -> <cac:TaxSubtotal> - 
            $TaxableAmount = $this->xml( 3, 'cbc:TaxableAmount', 
                $base . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ],
            );
            
            // 3 -> <cac:TaxCategory>
            $Percent = $this->xml( 4, 'cbc:Percent', $percentage );

            // 4 -> <cac:TaxScheme>
            $labelTax = 'ReteRenta';
            $iva_code = config('app.equivalenDocument')['TaxScheme_Name'][ $labelTax ];
            $cbcID = $this->xml( 5, 'cbc:ID', $iva_code );
            $cbcName = $this->xml( 5, 'cbc:Name', $labelTax );

            $TaxScheme = $this->xml( 4, 'cac:TaxScheme', $cbcID 
                . $cbcName 
            );
            $TaxCategory = $this->xml( 3, 'cac:TaxCategory', $Percent
                . $TaxScheme 
            );
            $TaxSubtotal = $this->xml( 2, 'cac:TaxSubtotal', $TaxableAmount 
                . $TaxAmount 
                . $TaxCategory 
            );
            $WithholdingTaxTotal = $this->xml( 1, 'cac:WithholdingTaxTotal', $TaxAmount 
                . $TaxSubtotal 
            );
            $xml_ .= $WithholdingTaxTotal;
        }
        return $xml_;
    }

    public function infoRetensDS ( $doc, $documentLines, $xml, $sum_ivas )
    {
        $listRetens = [];
        $sum_reterentas = 0;
        $xml_ = '';
        $xml_items = [
            'rete_renta' => [],
            'rete_iva' => '',
        ];
        $countId = 0;
        foreach ( $documentLines as $index => $dLine )        
        {
            if( $dLine->perc_reterenta > 0 )
            {
                $countId++;

                $tt_bruto = $dLine->quantity * $dLine->price;
                $sub_tt = $tt_bruto;
                if( $dLine->perc_discount > 0 )
                    $sub_tt = $tt_bruto - ( $tt_bruto * ( $dLine->perc_discount / 100 ) );
                
                $tt_value = $sub_tt * ( $dLine->perc_reterenta / 100 );

                $sum_reterentas = $sum_reterentas + $tt_value;

                if( ! isset( $listRetens[ $dLine->perc_reterenta ] ) ){
                    $rowItem = [
                        'base'=> $sub_tt,
                        'perc'=> $tt_value,
                    ];
                    $listRetens[ $dLine->perc_reterenta ] = $rowItem;
                }
                else{
                    $listRetens[ $dLine->perc_reterenta ]['base'] = $listRetens[ $dLine->perc_reterenta ]['base'] + $sub_tt;

                    $listRetens[ $dLine->perc_reterenta ]['perc'] = $listRetens[ $dLine->perc_reterenta ]['perc'] + $tt_value;
                }

                /*  
                * Para Discriminar por % Comentar esto, y descomentar donde se indica a abajo */
                /*
                // 1 -> <cac:WithholdingTaxTotal>
                $TaxAmount = $this->xml( 2, 'cbc:TaxAmount', // En XML está 2 Veces
                    $tt_value . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                ); */

                // 2 -> <cac:TaxSubtotal> - 
                $TaxableAmount = $this->xml( 3, 'cbc:TaxableAmount', 
                    $sub_tt . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );

                $TaxAmountReteRenta = $this->xml( 2, 'cbc:TaxAmount', // Difiere, es el valor de la Retencion
                    $tt_value . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ],
                );

                // 3 -> <cac:TaxCategory>
                $Percent = $this->xml( 4, 'cbc:Percent', $dLine->perc_reterenta );

                // 4 -> <cac:TaxScheme>
                $labelTax = 'ReteRenta';
                $iva_code = config('app.equivalenDocument')['TaxScheme_Name'][ $labelTax ];
                $cbcID = $this->xml( 5, 'cbc:ID', $iva_code );
                $cbcName = $this->xml( 5, 'cbc:Name', $labelTax );

                $TaxScheme = $this->xml( 4, 'cac:TaxScheme', $cbcID 
                    . $cbcName 
                );
                $TaxCategory = $this->xml( 3, 'cac:TaxCategory', $Percent
                    . $TaxScheme 
                );
                $TaxSubtotal = $this->xml( 2, 'cac:TaxSubtotal', $TaxableAmount 
                    . $TaxAmountReteRenta 
                    . $TaxCategory 
                );
                /*
                $WithholdingTaxTotal = $this->xml( 1, 'cac:WithholdingTaxTotal', $TaxAmount 
                    . $TaxSubtotal 
                );
                $xml_ .= $WithholdingTaxTotal; /*  */
                $xml_ .= $TaxSubtotal;


                // Items -----------------------------------------------------------
                // 2 -> <cac:WithholdingTaxTotal>
                $TaxAmount = $this->xml( 3, 'cbc:TaxAmount', // En XML está 2 Veces
                    $tt_value . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ], 
                );

                // 3 -> <cac:TaxSubtotal> - 
                $TaxableAmount = $this->xml( 4, 'cbc:TaxableAmount', 
                    $sub_tt . '.00', 
                    'vector',
                    [ 'currencyID', ], 
                    [ $doc->currency, ], // 
                );
                
                // 4 -> <cac:TaxCategory>
                $Percent = $this->xml( 5, 'cbc:Percent', $dLine->perc_reterenta );

                // 5 -> <cac:TaxScheme>
                $labelTax = 'ReteRenta';
                $iva_code = config('app.equivalenDocument')['TaxScheme_Name'][ $labelTax ];
                $cbcID = $this->xml( 6, 'cbc:ID', $iva_code );
                $cbcName = $this->xml( 6, 'cbc:Name', $labelTax );

                $TaxScheme = $this->xml( 5, 'cac:TaxScheme', $cbcID 
                    . $cbcName 
                );
                $TaxCategory = $this->xml( 4, 'cac:TaxCategory', $Percent
                    . $TaxScheme 
                );
                $TaxSubtotal = $this->xml( 3, 'cac:TaxSubtotal', $TaxableAmount 
                    . $TaxAmount 
                    . $TaxCategory 
                );
                $WithholdingTaxTotal = $this->xml( 2, 'cac:WithholdingTaxTotal', $TaxAmount 
                    . $TaxSubtotal 
                );
                $xml_items['rete_renta'][ $index ] = $WithholdingTaxTotal;
            }
        }

        /*
        * Para Discriminar por % descomentar esto 
        $xml_ = $this->sumRetens( $listRetens, $doc );*/
        if( $sum_reterentas != 0 ){
            $TaxAmount = $this->xml( 2, 'cbc:TaxAmount',
                $sum_reterentas . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ],
            );
            $WithholdingTaxTotal = $this->xml( 1, 'cac:WithholdingTaxTotal', $TaxAmount 
                . $xml_ 
            );
            $xml_ = $WithholdingTaxTotal;
        }
        

        // ReteIVA
        if( $doc->perc_reteiva > 0 && $sum_ivas > 0 )
        {
            $countId++;
            $tt_value = $sum_ivas * ( $doc->perc_reteiva / 100 );

            // 1 -> <cac:WithholdingTaxTotal>
            $TaxAmount = $this->xml( 2, 'cbc:TaxAmount', // En XML está 2 Veces
                $tt_value . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ],
            );

            // 2 -> <cac:TaxSubtotal>
            $TaxableAmount = $this->xml( 3, 'cbc:TaxableAmount', 
                $sum_ivas . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ],
            );
            
            // 3 -> <cac:TaxCategory>
            $Percent = $this->xml( 4, 'cbc:Percent', $doc->perc_reteiva );

            // 4 -> <cac:TaxScheme>
            $labelTax = 'ReteIVA';
            $iva_code = config('app.equivalenDocument')['TaxScheme_Name'][ $labelTax ];
            $cbcID = $this->xml( 5, 'cbc:ID', $iva_code );
            $cbcName = $this->xml( 5, 'cbc:Name', $labelTax );

            $TaxScheme = $this->xml( 4, 'cac:TaxScheme', $cbcID 
                . $cbcName 
            );
            
            $TaxCategory = $this->xml( 3, 'cac:TaxCategory', $Percent
                . $TaxScheme 
            );

            $TaxSubtotal = $this->xml( 2, 'cac:TaxSubtotal', $TaxableAmount 
                . $TaxAmount 
                . $TaxCategory 
            );

            $WithholdingTaxTotal = $this->xml( 1, 'cac:WithholdingTaxTotal', $TaxAmount 
                . $TaxSubtotal 
            );
            $xml_ .= $WithholdingTaxTotal;

            // Items -----------------------------------------------------------
            // 2 -> <cac:WithholdingTaxTotal>
            $TaxAmount = $this->xml( 3, 'cbc:TaxAmount', // En XML está 2 Veces
                $tt_value . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ], 
            );

            // 3 -> <cac:TaxSubtotal> - 
            $TaxableAmount = $this->xml( 4, 'cbc:TaxableAmount', 
                $sub_tt . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ], // 
            );
            
            // 4 -> <cac:TaxCategory>
            $Percent = $this->xml( 5, 'cbc:Percent', $dLine->perc_reterenta );

            // 5 -> <cac:TaxScheme>
            $cbcID = $this->xml( 6, 'cbc:ID', $iva_code );
            $cbcName = $this->xml( 6, 'cbc:Name', $labelTax );

            $TaxScheme = $this->xml( 5, 'cac:TaxScheme', $cbcID 
                . $cbcName 
            );
            $TaxCategory = $this->xml( 4, 'cac:TaxCategory', $Percent
                . $TaxScheme 
            );
            $TaxSubtotal = $this->xml( 3, 'cac:TaxSubtotal', $TaxableAmount 
                . $TaxAmount 
                . $TaxCategory 
            );
            $WithholdingTaxTotal = $this->xml( 2, 'cac:WithholdingTaxTotal', $TaxAmount 
                . $TaxSubtotal 
            );
            $xml_items['rete_iva'] = $WithholdingTaxTotal;
        }
        return [
            'xml' => $xml_, 
            //'xml_items_retentions' => $xml_items,
            'rete_renta' => $xml_items['rete_renta'],
            'rete_iva' => $xml_items['rete_iva'],
        ];
    }


    public function infoTotalsDS ( $doc, $documentLines, $xml )
    {
        // 1 -> <cac:LegalMonetaryTotal>
        $LineExtensionAmount = $this->xml( 2, 'cbc:LineExtensionAmount', 
            $doc->gross_total_base_lines, 
            'vector',
            [ 'currencyID', ], 
            [ $doc->currency, ],
        );
        $TaxExclusiveAmount = $this->xml( 2, 'cbc:TaxExclusiveAmount', 
            '0.00', 
            'vector',
            [ 'currencyID', ], 
            [ $doc->currency, ],
        );
        $TaxInclusiveAmount = $this->xml( 2, 'cbc:TaxInclusiveAmount', 
            $doc->gross_total_and_tribute, 
            'vector',
            [ 'currencyID', ], 
            [ $doc->currency, ],
        );
        $AllowanceTotalAmount = $this->xml( 2, 'cbc:AllowanceTotalAmount', 
            $doc->discounts_total_and_detail, 
            'vector',
            [ 'currencyID', ], 
            [ $doc->currency, ],
        );
        $ChargeTotalAmount = $this->xml( 2, 'cbc:ChargeTotalAmount', 
            $doc->charges_total_and_detail, 
            'vector',
            [ 'currencyID', ], 
            [ $doc->currency, ],
        );
        $PayableAmount = $this->xml( 2, 'cbc:PayableAmount', 
            $doc->pay_total, 
            'vector',
            [ 'currencyID', ], 
            [ $doc->currency, ],
        );

        $PaymentMeans = $this->xml( 1, 'cac:LegalMonetaryTotal', $LineExtensionAmount 
            . $TaxExclusiveAmount 
            . $TaxInclusiveAmount 
            . $AllowanceTotalAmount 
            . $ChargeTotalAmount 
            . $PayableAmount 
        );

        return $PaymentMeans;
    }


    public function infoDetailLinesDS ( 
        $doc, 
        $documentLines, 
        $xml, 
        $arrayDiscountCharge, 
        $arrayIva, 
        $arrayRetens 
    ){
        $xml_ = '';
        $countId = 0;
        foreach ( $documentLines as $index => $dLine )        
        {
            $countId++;

            $tt_bruto = $dLine->quantity * $dLine->price;
            $sub_tt = $tt_bruto;
            if( $dLine->perc_discount > 0 ){
                $tt_discount = $tt_bruto * ( $dLine->perc_discount / 100 );
                $sub_tt = $tt_bruto - $tt_discount;
            }

            // 1 -> <cac:InvoiceLine>
            $cbcID = $this->xml( 2, 'cbc:ID', $countId );

            $Note = '';
            if( $this->haveData( $dLine->note ) )
                $Note = $this->xml( 2, 'cbc:Note', $dLine->note );

            $InvoicedQuantity = $this->xml( 2, 'cbc:InvoicedQuantity', 
                $dLine->quantity, 
                'vector',
                [ 'unitCode', ], 
                [ $dLine->unit_measurement, ],
            );
            
            $LineExtensionAmount = $this->xml( 2, 'cbc:LineExtensionAmount', 
                $sub_tt . '.00', 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ],
            );

            // 2 -> <cac:InvoicePeriod>
            $InvoicePeriod = $this->xmlInvoicePeriodDS( $doc, $documentLines, 2 );

            // 2 -> <cac:AllowanceCharge>
            //$AllowanceCharge = $arrayDiscountCharge['xml_items_discount_cargos'];
            $AllowanceCharge = '';
            if( isset( $arrayDiscountCharge['discount'][ $index ] ) )
                $AllowanceCharge .= $arrayDiscountCharge['discount'][ $index ];
            if( isset( $arrayDiscountCharge['charge'][ $index ] ) )
                $AllowanceCharge .= $arrayDiscountCharge['charge'][ $index ];

            //if( $AllowanceCharge == '' )
            //    $AllowanceCharge = $this->xml( 2, 'cac:AllowanceCharge', 'MALOOOOO AllowanceCharge' );

            // 2 -> <cac:TaxTotal>
            //$TaxTotal = $arrayIva['xml_items_ivas'];
            $TaxTotal = '';
            if( isset( $arrayIva['xml_iva'][ $index ] ) )
                $TaxTotal = $arrayIva['xml_iva'][ $index ];

            //if( $TaxTotal == '' )
            //    $TaxTotal = $this->xml( 2, 'cac:TaxTotal', 'MALOOOOO TaxTotal' );

            // 2 -> <cac:WithholdingTaxTotal>
            //$WithholdingTaxTotal = $arrayRetens['xml_items_retentions'];
            $WithholdingTaxTotal = '';
            if( isset( $arrayRetens['rete_renta'][ $index ] ) )
                $WithholdingTaxTotal = $arrayRetens['rete_renta'][ $index ];
            if( isset( $arrayRetens['rete_iva'] ) )
                $WithholdingTaxTotal .= $arrayRetens['rete_iva'];

            //if( $WithholdingTaxTotal == '' )
            //    $WithholdingTaxTotal = $this->xml( 2, 'cac:WithholdingTaxTotal', 'MALOOOOO WithholdingTaxTotal' );

            // 2 -> <cac:Item>
            $Item = $this->xmlItemDS( $doc, $dLine, 2, $countId );

            // 2 -> <cac:Price>
            $PriceAmount = $this->xml( 3, 'cbc:PriceAmount', 
                $dLine->price, 
                'vector',
                [ 'currencyID', ], 
                [ $doc->currency, ],
            );
            $BaseQuantity = $this->xml( 3, 'cbc:BaseQuantity', 
                $dLine->quantity, 
                'vector',
                [ 'unitCode', ], 
                [ $dLine->unit_measurement, ],
            );

            $Price = $this->xml( 2, 'cac:Price', $PriceAmount 
                . $BaseQuantity 
            );

            $InvoiceLine = $this->xml( 1, 'cac:InvoiceLine', $cbcID 
                . $Note 
                . $InvoicedQuantity 
                . $LineExtensionAmount 
                . $InvoicePeriod 
                . $AllowanceCharge 
                . $TaxTotal 
                . $WithholdingTaxTotal 
                . $Item 
                . $Price 
            );

            $xml_ .= $InvoiceLine;
        }

        return $xml_;
    }


    public function xmlInvoicePeriodDS( $doc, $documentLines, $level )
    {
        // 2 -> <cac:InvoicePeriod>
        $StartDate = $this->xml( $level+1, 'cbc:StartDate', $doc->date_transaction );

        // Transmisión
        $gen_transmission_code = $documentLines[0]->gen_transmission_code;
        $gen_transmission = $documentLines[0]->gen_transmission;

        $DescriptionCode = $this->xml( $level+1, 'cbc:DescriptionCode', $gen_transmission_code );
        $Description = $this->xml( $level+1, 'cbc:Description', $gen_transmission );

        $InvoicePeriod = $this->xml( $level, 'cac:InvoicePeriod', $StartDate 
            . $DescriptionCode 
            . $Description 
        );
        return $InvoicePeriod;
    }

    public function xmlItemDS( $doc, $dLine, $level, $countId )
    {
        // 2 -> <cac:Item>
        $Description = $this->xml( $level+1, 'cbc:Description', $dLine->item_description );
        
        $PackSizeNumeric = '';
        if( $this->haveData( $dLine->item_quantity ) )
            $PackSizeNumeric = $this->xml( $level+1, 'cbc:PackSizeNumeric', $dLine->item_quantity );

        $BrandName = '';
        if( $this->haveData( $dLine->item_brand ) )
            $BrandName = $this->xml( $level+1, 'cbc:BrandName', $dLine->item_brand );

        $ModelName = '';
        if( $this->haveData( $dLine->item_model ) )
            $ModelName = $this->xml( $level+1, 'cbc:ModelName', $dLine->item_model );

        // 3 -> <cac:SellersItemIdentification>
        $cbcID = $this->xml( $level+2, 'cbc:ID', $countId );
        $ExtendedID = $this->xml( $level+2, 'cbc:ExtendedID', $countId );

        $SellersItemIdentification = $this->xml( $level+1, 'cac:SellersItemIdentification', $cbcID 
            . $ExtendedID 
        );

        // 3 -> <cac:StandardItemIdentification>
        $cbcID = $this->xml( $level+2, 'cbc:ID', 
            $countId, 
            'vector',
            [ 'schemeID', 'schemeName' ], 
            [ '999', 'Estándar de adopción del contribuyente' ],
        );
        $StandardItemIdentification = $this->xml( $level+1, 'cac:StandardItemIdentification', 
            $cbcID 
        );

        $Item = $this->xml( $level, 'cac:Item', $Description 
            . $PackSizeNumeric 
            . $BrandName 
            . $ModelName 
            //. $SellersItemIdentification 
            . $StandardItemIdentification 
        );

        return $Item;
    }


    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function create_xml_note( $document, $resolutionDian, $documentLines )
    {
        $xmlHead = config('app.equivalenDocument')['initSupportDocument'];
        $xmlEndData = config('app.equivalenDocument')['xml_data'];
        $xmlHeadEnd = "\n" . $xmlEndData . "\n</Invoice>";
        $xml = '';

        $document_array=json_decode(json_encode($document), true);

        //var_dump($document_array);

        $xml = $this->GeneralDS2( $document, $documentLines, $xml );

        $xml = $xmlHead . $xml . $xmlHeadEnd;

        $nameXML = 'xml_'.$document_array['id'].'.xml';
        $pathDS = storage_path('app') . '/ds/' . $nameXML;

        $store_xml_route=DB::UPDATE("UPDATE documents
                                     SET xml_route = ?
                                     WHERE id=?",[$nameXML,$document_array['id']]);


        
        $file = fopen( $pathDS, 'w');
        fwrite($file, $xml . PHP_EOL);
        fclose($file);

        return $pathDS;
    }



    public function GeneralDS2 ( $doc, $documentLines, $xml )
    {
        /*
        Info a mostrar en el XML
        */
        $xml_general = true;
        $xml_supplier = true;
        $xml_my_company = true;
        $xml_info_payment = true;
        $xml_charge_discount = true;
        $xml_exchage_rate = true;
        $xml_tax_iva = true;
        $xml_retens = true;
        $xml_totals = true;
        $xml_lines = true;

        $xmlGen1 = '';

        // INFO DOC SOPORTE  -----------------------------------
        if( $xml_general )
        {
            
            $xmlGen1 .= $this->xml( 1, 'cbc:CustomizationID', $doc->resident );
            $xmlGen1 .= $this->xml( 1, 'cbc:ProfileExecutionID', $doc->environment );
            $xmlGen1 .= $this->xmlOnly( 1, 'cbc:ID', $doc->document_number );
            $xmlGen1 .= $this->xmlOnly( 1, 'cbc:IssueDate', $doc->date_transaction );
            $xmlGen1 .= $this->xmlOnly( 1, 'cbc:IssueTime', $doc->time_transaction );
            //$xmlGen1 .= $this->xml( 1, 'cbc:DueDate', $doc->date_due );
            $xmlGen1 .= $this->xml( 1, 'cbc:InvoiceTypeCode', $doc->type_code );

            if( $this->haveData( $doc->note ) )
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->note );
            
            
            $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->value_letters, 
                'una-linea','languageLocaleID','ValorLetras',
            );

            if( $this->haveData( $doc->observations ) ){
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->observations, 
                    'vector',
                    ['languageLocaleID'],
                    ['Observaciones'],
                );
            }

            if( $this->haveData( $doc->note1 ) ){
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->note1, 
                    'una-linea', 'languageLocaleID', 'Nota 1',
                );
            }
            if( $this->haveData( $doc->note2 ) ){
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->note2, 
                    'una-linea', 'languageLocaleID', 'Nota 2',
                );
            }
            if( $this->haveData( $doc->note3 ) ){
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->note3, 
                    'una-linea', 'languageLocaleID', 'Nota 3',
                );
            }
            if( $this->haveData( $doc->note4 ) ){
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->note4, 
                    'una-linea', 'languageLocaleID', 'Nota 4',
                );
            }
            if( $this->haveData( $doc->note5 ) ){
                $xmlGen1 .= $this->xml( 1, 'cbc:Note', $doc->note5, 
                    'una-linea', 'languageLocaleID', 'Nota 5',
                );
            }

            $xmlGen1 .= $this->xml( 1, 'cbc:DocumentCurrencyCode', $doc->currency );
            $xmlGen1 .= $this->xml( 1, 'cbc:LineCountNumeric', $doc->total_lines );

            // ORDEN COMPRA y ANTICIPO -----------------------------------
            if( $this->haveData( $doc->have_purchase_order ) ){
                $order_number = $this->xml( 2, 'cbc:ID', $doc->order_number );
                $order_date = $this->xml( 2, 'cbc:IssueDate', $doc->order_date );
                $xmlGen1 .= $this->xml( 1, 'cac:OrderReference', $order_number . $order_date );
            }
            /*
            if( $this->haveData( $doc->have_advance ) ){
                $advance_number = $this->xml( 2, 'cbc:ID', $doc->advance_number );
                $advance_date = $this->xml( 2, 'cbc:IssueDate', $doc->advance_date );
                $xmlGen1 .= $this->xml( 1, 'cac:OrderReference', $advance_number . $advance_date );
            } */
            

            // NOTA DE AJUSTE Relacionada -----------------------------------
            if( $this->haveData( $doc->document_id ) ){
                // 1 -> <cac:BillingReference> -> 2 -> <cbc:InvoiceDocumentReference>
                $cbcID = $this->xml( 3, 'cbc:ID', $doc->document_number_note );
                $cbcUUID = $this->xml( 3, 'cbc:UUID', 
                    $doc->cuds, 
                    'vector',
                    [ 'schemeName', ], 
                    [ config('app.equivalenDocument')['companies_const']['schemeAgencyName'], ],
                );
                $IssueDate = $this->xml( 3, 'cbc:IssueDate', $doc->date_send_ds );

                $InvoiceDocumentReference = $this->xml( 2, 'cbc:InvoiceDocumentReference', $cbcID 
                    . $cbcUUID 
                    . $IssueDate 
                );

                $BillingReference = $this->xml( 1, 'cac:BillingReference', $InvoiceDocumentReference );

                $xmlGen1 .= $BillingReference;
            }
        }

        // INFO PROVEEDOR DS -----------------------------------
        if( $xml_supplier ){
            $xmlGen1 .= $this->infoSupplierDS( $doc, $documentLines, $xml );
        }

        // INFO RECEPTOR (Nosotros) DS -------------------------
        if( $xml_my_company ){
            $xmlGen1 .= $this->infoMyCompanyDS( $doc, $documentLines, $xml );
        }

        // MEDIOS DE PAGO DS ------------------------------------
        if( $xml_info_payment ){
            $xmlGen1 .= $this->infoPaymentDS( $doc, $documentLines, $xml );
        }

        // CARGOS / DESCUENTOS 1 DS -----------------------------
        //$arrayDiscountCharge = [ 'xml_items_discount_cargos' => '' ];
        $arrayDiscountCharge = [
            'charge' => [],
            'discount' => [],
        ];
        $arrayDiscountCharge = $this->infoDiscountChargeDS( $doc, $documentLines, $xml );
        if( $xml_charge_discount ){
            $xmlGen1 .= $arrayDiscountCharge['xml'];
        }

        // TASAS DE CAMBIO DS -----------------------------------
        if( $xml_exchage_rate ){
            $xmlGen1 .= $this->infoExchangesRateDS( $doc, $documentLines, $xml );
        }

        // IVAs DS  ---------------------------------------------
        $arrayIva = [
            'sum_ivas' => 0,
            'xml_iva' => [],
        ];
        $arrayIva = $this->infoTaxIvaDS( $doc, $documentLines, $xml );
        if( $xml_tax_iva ){
            $xmlGen1 .= $arrayIva['xml'];
        }

        // RETENCIONES DS  --------------------------------------
        $arrayRetens = [
            'rete_renta' => [],
            'rete_iva' => '',
        ];
        $arrayRetens = $this->infoRetensDS( $doc, $documentLines, $xml, $arrayIva['sum_ivas'] );
        if( $xml_retens ){
            $xmlGen1 .= $arrayRetens['xml'];
        }

        // TOTALES DS  ------------------------------------------
        if( $xml_totals ){
            $xmlGen1 .= $this->infoTotalsDS( $doc, $documentLines, $xml );
        }

        // LINEA DE DETALLE DS  ---------------------------------
        if( $xml_lines ){
            $xmlGen1 .= $this->infoDetailLinesDS( $doc, $documentLines, $xml, $arrayDiscountCharge, 
                $arrayIva, $arrayRetens 
            );
        }

        return $xmlGen1;
    }



}

