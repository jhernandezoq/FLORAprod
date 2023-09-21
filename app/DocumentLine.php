<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DocumentLine extends Model
{
    protected $table = 'documents_lines';
    public $timestamps = false;

    protected $fillable = [
        'document_id', 
        'quantity_unit_id', 
        'line',
        'note',
        'quantity', 
        'unit_measurement', 
        'total',
        'date_purchase',
        'gen_transmission',
        'gen_transmission_code',
        'item_description',
        'item_quantity',
        'item_brand',
        'item_model',
        'product_code',
        'product_scheme_id',
        'price',
        'perc_charge',
        'perc_discount',
        'perc_tax',
        'perc_reterenta',
        'code_discounts_charge_tax',
    ];

    public function getAllByDocumentId( $document_id ){
        $rows = DB::select("SELECT d.*,
            concat(qu.code, ' - ', qu.description) as quantity_unit
        FROM documents_lines d
        JOIN quantity_units qu ON qu.id = d.quantity_unit_id 
        WHERE 
            d.document_id = " . $document_id . "
        ORDER BY d.id ASC");
        
        //$TaxScheme_Name = config('app.equivalenDocument')['TaxScheme_Name'];
        foreach ($rows as $key => $value ) {
            /*
            $tax_code = config('app.getArrayLabelByValue')(
                $TaxScheme_Name, 
                $value->code,
            );
            $rows[ $key ]->tax_code = $tax_code.' - '.$value->code; */

            $rows[ $key ] = $this->proccessData( $rows, $key, $value );
        }
        return $rows;
    }

    public function getOneById( $id ){
        $rows = DB::select("SELECT d.*,
            concat(qu.code, ' - ', qu.description) as quantity_unit
        FROM documents_lines d
        JOIN quantity_units qu ON qu.id = d.quantity_unit_id 
        WHERE 
            d.id = " . $id . "");
        foreach ($rows as $key => $value ) {
            $rows[ $key ] = $this->proccessData( $rows, $key, $value );
        }
        return $rows[ 0 ];
    }

    public function getMaxLineByDocumentId( $document_id ){
        $rows = DB::select("SELECT MAX(d.line) as max_line
        FROM documents_lines d
        WHERE d.document_id = " . $document_id . "");
        return $rows[ 0 ]->max_line;
    }

    public function getInfoQuantityUnitById( $id ){
        $rows = DB::select("SELECT * 
        FROM quantity_units
        WHERE id = " . $id . "");
        return $rows[ 0 ];
    }


    // Info to generate XML
    public function getAllByDocumentIdToXML( $document_id ){
        $rows = DB::select("SELECT d.*,
            concat(qu.code, ' - ', qu.description) as quantity_unit
        FROM documents_lines d
        JOIN quantity_units qu ON qu.id = d.quantity_unit_id 
        WHERE 
            d.document_id = " . $document_id . "
        ORDER BY d.id ASC");
        return $rows;
    }


    public function proccessData ( $rows, $key, $value ){
        $rows[ $key ]->quantity = $this->clearZero( $value->quantity );
        $rows[ $key ]->total = $this->clearZero( $value->total );
        $rows[ $key ]->item_quantity = $this->clearZero( $value->item_quantity );
        $rows[ $key ]->price = $this->clearZero( $value->price );
        $rows[ $key ]->perc_charge = $this->clearZero( $value->perc_charge );
        $rows[ $key ]->perc_discount = $this->clearZero( $value->perc_discount );
        $rows[ $key ]->perc_tax = $this->clearZero( $value->perc_tax );
        $rows[ $key ]->perc_reterenta = $this->clearZero( $value->perc_reterenta );
        return $rows[ $key ];
    }

    public function clearZero( $value ){
        $result = str_replace('.00', '', $value);
        return $result;
    }
}
