<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Document extends Model
{
    protected $table = 'documents';
    public $timestamps = false;

    protected $fillable = [
        'resolution_id', 
        'department_id',
        'city_id',
        'company_id', 
        'supplier_id', 
        'document_id', 
        'type',
        'type_code',
        'resident',
        'status',
        'environment',
        'document_number',
        'document_number_note',
        'cuds',
        'date_transaction',
        'time_transaction',
        'date_send_ds',
        'date_due',
        'note',
        'value_letters',
        'observations',
        'note1',
        'note2',
        'note3',
        'note4',
        'note5',
        'currency',
        'total_lines',
        'have_purchase_order',
        'order_number',
        'order_date',
        'have_advance',
        'advance_number',
        'advance_date',
        'language',
        'supplier_reason',
        'supplier_document_type',
        'supplier_document',
        'supplier_verification_digit',
        'supplier_legal_organization',
        'supplier_country',
        'supplier_postal_code',
        'supplier_address',
        'supplier_fiscal_obligations_code',
        'supplier_tribute',
        'reason',
        'document_type',
        'document_prefix',
        'document',
        'verification_digit',
        'legal_organization',
        'fiscal_obligations_code',
        'tribute',
        'way_pay',
        'payment_method',
        'date_due_payment',
        'base_currency_init',
        'monetary_base_init',
        'base_currency_end',
        'monetary_base_end',
        'exchange_rate_value',
        'date_exchange_payment',
        'base_currency_init2',
        'monetary_base_init2',
        'base_currency_end2',
        'monetary_base_end2',
        'exchange_rate_value2',
        'date_exchange_payment2',
        'perc_reteiva',
        'gross_total_base_lines',
        'gross_total_minus_discounts',
        'gross_total_and_tribute',
        'discounts_total_and_detail',
        'charges_total_and_detail',
        'pay_total',
    ];

    public function initRow( $data = NULL ){
        $row = new \stdClass();
        $row->id = 0;
        $row->resolution_id = '';
        $row->resolution = '';
        $row->department_id = '';
        $row->city_id = '';
        $row->company_id = '';
        $row->company = '';
        $row->supplier_id = '';
        $row->document_id = '';
        $row->type = '';
        $row->type_code = '';
        $row->resident = '';
        $row->status = '';
        $row->environment = '';
        $row->document_number = '';
        $row->document_number_note = '';
        $row->cuds = '';
        $row->date_transaction = '';
        $row->time_transaction = '';
        $row->date_send_ds = '';
        $row->date_due = '';
        $row->note = '';
        $row->value_letters = '';
        $row->observations = '';
        $row->note1 = '';
        $row->note2 = '';
        $row->note3 = '';
        $row->note4 = '';
        $row->note5 = '';
        $row->currency = '';
        $row->total_lines = '';
        $row->have_purchase_order = '';
        $row->order_number = '';
        $row->order_date = '';
        $row->have_advance = '';
        $row->advance_number = '';
        $row->advance_date = '';
        $row->language = '';
        $row->supplier_reason = '';
        $row->supplier_document_type = '';
        $row->supplier_document = '';
        $row->supplier_verification_digit = '';
        $row->supplier_legal_organization = '';
        $row->supplier_country = '';
        $row->supplier_postal_code = '';
        $row->supplier_address = '';
        $row->supplier_fiscal_obligations_code = '';
        $row->supplier_tribute = '';
        $row->reason = '';
        $row->document_type = '';
        $row->document_prefix = '';
        $row->document = '';
        $row->verification_digit = '';
        $row->legal_organization = '';
        $row->fiscal_obligations_code = '';
        $row->tribute = '';
        $row->way_pay = '';
        $row->payment_method = '';
        $row->date_due_payment = '';
        $row->base_currency_init = '';
        $row->monetary_base_init = '';
        $row->base_currency_end = '';
        $row->monetary_base_end = '';
        $row->exchange_rate_value = '';
        $row->date_exchange_payment = '';
        $row->base_currency_init2 = '';
        $row->monetary_base_init2 = '';
        $row->base_currency_end2 = '';
        $row->monetary_base_end2 = '';
        $row->exchange_rate_value2 = '';
        $row->date_exchange_payment2 = '';
        $row->perc_reteiva = '';
        $row->gross_total_base_lines = '';
        $row->gross_total_minus_discounts = '';
        $row->gross_total_and_tribute = '';
        $row->discounts_total_and_detail = '';
        $row->charges_total_and_detail = '';
        $row->pay_total = '';
    
        if( $data !== NULL ){
            $row->id = $data->id;
            $row->resolution_id = $data->resolution_id;
            $row->resolution = $data->resolution;
            $row->department_id = $data->department_id;
            $row->city_id = $data->city_id;
            $row->company_id = $data->company_id;
            $row->company = $data->company;
            $row->supplier_id = $data->supplier_id;
            $row->document_id = $data->document_id;
            $row->type = $data->type;
            $row->type_code = $data->type_code;
            $row->resident = $data->resident;
            $row->status = $data->status;
            $row->environment = $data->environment;
            $row->document_number = $data->document_number;
            $row->document_number_note = $data->document_number_note;
            $row->cuds = $data->cuds;
            $row->date_transaction = $data->date_transaction;
            $row->time_transaction = $data->time_transaction;
            $row->date_send_ds = $data->date_send_ds;
            $row->date_due = $data->date_due;
            $row->note = $data->note;
            $row->value_letters = $data->value_letters;
            $row->observations = $data->observations;
            $row->note1 = $data->note1;
            $row->note2 = $data->note2;
            $row->note3 = $data->note3;
            $row->note4 = $data->note4;
            $row->note5 = $data->note5;
            $row->currency = $data->currency;
            $row->total_lines = $data->total_lines;
            $row->have_purchase_order = $data->have_purchase_order;
            $row->order_number = $data->order_number;
            $row->order_date = $data->order_date;
            $row->have_advance = $data->have_advance;
            $row->advance_number = $data->advance_number;
            $row->advance_date = $data->advance_date;
            $row->language = $data->language;
            $row->supplier_reason = $data->supplier_reason;
            $row->supplier_document_type = $data->supplier_document_type;
            $row->supplier_document = $data->supplier_document;
            $row->supplier_verification_digit = $data->supplier_verification_digit;
            $row->supplier_legal_organization = $data->supplier_legal_organization;
            $row->supplier_country = $data->supplier_country;
            $row->supplier_postal_code = $data->supplier_postal_code;
            $row->supplier_address = $data->supplier_address;
            $row->supplier_fiscal_obligations_code = $data->supplier_fiscal_obligations_code;
            $row->supplier_tribute = $data->supplier_tribute;
            $row->reason = $data->reason;
            $row->document_type = $data->document_type;
            $row->document_prefix = $data->document_prefix;
            $row->document = $data->document;
            $row->verification_digit = $data->verification_digit;
            $row->legal_organization = $data->legal_organization;
            $row->fiscal_obligations_code = $data->fiscal_obligations_code;
            $row->tribute = $data->tribute;
            $row->way_pay = $data->way_pay;
            $row->payment_method = $data->payment_method;
            $row->date_due_payment = $data->date_due_payment;
            $row->base_currency_init = $data->base_currency_init;
            $row->monetary_base_init = $this->clearZero($data->monetary_base_init);
            $row->base_currency_end = $data->base_currency_end;
            $row->monetary_base_end = $this->clearZero($data->monetary_base_end);
            $row->exchange_rate_value = $this->clearZero($data->exchange_rate_value);
            $row->date_exchange_payment = $data->date_exchange_payment;
            $row->base_currency_init2 = $data->base_currency_init2;
            $row->monetary_base_init2 = $this->clearZero($data->monetary_base_init2);
            $row->base_currency_end2 = $data->base_currency_end2;
            $row->monetary_base_end2 = $this->clearZero($data->monetary_base_end2);
            $row->exchange_rate_value2 = $this->clearZero($data->exchange_rate_value2);
            $row->date_exchange_payment2 = $data->date_exchange_payment2;

            $row->perc_reteiva = $this->clearZero($data->perc_reteiva);
            $row->gross_total_base_lines = $this->clearZero($data->gross_total_base_lines);
            $row->gross_total_minus_discounts = $this->clearZero($data->gross_total_minus_discounts);
            $row->gross_total_and_tribute = $this->clearZero($data->gross_total_and_tribute);
            $row->discounts_total_and_detail = $this->clearZero($data->discounts_total_and_detail);
            $row->charges_total_and_detail = $this->clearZero($data->charges_total_and_detail);
            $row->pay_total = $this->clearZero($data->pay_total);
            // Complementos
            $row->department = $data->department;
            $row->city = $data->city;
        }
        return $row;
    }

    public function getAll( $type = '' ){
        $rows = DB::select("SELECT d.*,
            r.resolution,
            com.name AS company,
            com.nit AS company_nit,
            c.name AS city,
            c.code AS city_code,
            dep.name AS department,
            dep.code AS department_code
        FROM documents d
        JOIN resolutions_dian r ON r.id = d.resolution_id 
        JOIN companies com ON com.id = r.company_id 
        JOIN cities c ON c.id = d.city_id 
        JOIN departments dep ON dep.id = c.department_id 
        WHERE 
            c.active = 1
        ORDER BY r.id DESC");
        return $rows;
    }

    public function getOne( $id ){
        $rows = DB::select("SELECT d.*,
            r.resolution,
            com.name AS company,
            com.nit AS company_nit,
            c.name AS city,
            c.code AS city_code,
            dep.name AS department,
            dep.code AS department_code
        FROM documents d
        JOIN resolutions_dian r ON r.id = d.resolution_id 
        JOIN companies com ON com.id = r.company_id 
        JOIN cities c ON c.id = d.city_id 
        JOIN departments dep ON dep.id = c.department_id 
        WHERE 
            c.active = 1 AND d.id = ".$id."
        ORDER BY r.id DESC");
        return $rows;
    }

    public function clearZero( $value ){
        $result = str_replace('.00', '', $value);
        return $result;
    }

    public function getAllNote( $type = ''){

        $rows = DB::select("SELECT d.*,
                            r.resolution,
                            com.name AS company,
                            com.nit AS company_nit,
                            c.name AS city,
                            c.code AS city_code,
                            dep.name AS department,
                            dep.code AS department_code
                        FROM documents d
                        JOIN resolutions_dian r ON r.id = d.resolution_id 
                        JOIN companies com ON com.id = r.company_id 
                        JOIN cities c ON c.id = d.city_id 
                        JOIN departments dep ON dep.id = c.department_id 
                        WHERE 
                            c.active = 1 AND
                            d.id_replace_document is null AND
                            d.document_number is not null AND
                            d.document_number <> ''
                        ORDER BY r.id DESC");
        return $rows;
    }

    public function maxId( $type = '' ){
        $rows = DB::select("SELECT max(id) AS max_id
        FROM documents");
        return $rows;
    }

}
