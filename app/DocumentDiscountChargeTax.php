<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DocumentDiscountChargeTax extends Model
{
    protected $table = 'documents_discounts_charge_tax';
    public $timestamps = false;

    protected $fillable = [
        'document_id', 
        'line',
        'type',
        'code', 
        'description', 
        'percentage',
        'base',
        'calculated_value',
    ];

    public function getAllByDocumentId( $document_id ){
        $rows = DB::select("SELECT d.*
        FROM documents_discounts_charge_tax d
        WHERE 
            d.document_id = " . $document_id . "
        ORDER BY d.id ASC");

        $TaxScheme_Name = config('app.equivalenDocument')['TaxScheme_Name'];
        foreach ($rows as $key => $value ) {
            $tax_code = config('app.getArrayLabelByValue')(
                $TaxScheme_Name, 
                $value->code,
            );
            $rows[ $key ]->tax_code = $tax_code.' - '.$value->code;

            $rows[ $key ]->percentage = $this->clearZero( $value->percentage );
            $rows[ $key ]->base = $this->clearZero( $value->base );
            $rows[ $key ]->calculated_value = $this->clearZero( $value->calculated_value );
        }

        return $rows;
    }

    public function getOneById( $document_id ){
        $rows = DB::select("SELECT d.*
        FROM documents_discounts_charge_tax d
        WHERE 
            d.id = " . $document_id . "");

        $TaxScheme_Name = config('app.equivalenDocument')['TaxScheme_Name'];
        foreach ($rows as $key => $value ) {
            $tax_code = config('app.getArrayLabelByValue')(
                $TaxScheme_Name, 
                $value->code,
            );
            $rows[ $key ]->tax_code = $tax_code.' - '.$value->code;

            $rows[ $key ]->percentage = $this->clearZero( $value->percentage );
            $rows[ $key ]->base = $this->clearZero( $value->base );
            $rows[ $key ]->calculated_value = $this->clearZero( $value->calculated_value );
        }

        return $rows[ 0 ];
    }

    public function clearZero( $value ){
        $result = str_replace('.00', '', $value);
        return $result;
    }


}
