<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

 	public function flow()
    {
        return $this->belongsTo('App\Flow');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Supplier');
    }

    public function method()
    {
        return $this->belongsTo('App\Method');
    }

    public function log()
    {
        return $this->hasMany('App\Log')->orderby('created_at','desc');
    }

    public function distribution()
    {
        return $this->belongsToMany('App\CostCenter', 'distributions', 'invoice_id', 'cost_center_id')->withPivot('percentage', 'value')->wherePivot('active', 1);
    }

    public function getActives($user_id)
    {
    	$invoices = DB::select('SELECT L.id log_id,I.id invoice_id ,I.revision as revision,I.concept,I.number,I.currency,I.subtotal,I.iva,I.total,I.priority,L.next_user_id,S.name supplier,I.due_date,
                                (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) LOG
                            FROM invoices I
                            INNER JOIN invoice_logg L ON L.invoice_id = I.id AND L.id = (SELECT MAX(id) FROM invoice_logg L WHERE L.invoice_id = I.id) AND next_user_id = ? AND L.state_id <> 6
                            INNER JOIN suppliers S ON S.id = I.supplier_id
                            ORDER BY I.due_date asc', [$user_id]);

    	return $invoices;
    }

    public function getPendingInvoices()
    {
        $invoices = Invoice::select(
            'invoices.id as invoice_id',
            'suppliers.name as supplier',
            'invoices.revision',
            'invoices.number',
            'invoices.due_date',
            'invoices.subtotal',
            'invoices.total',
            'invoices.iva',
            'invoices.priority',
            'invoices.file',
            'invoices.due_date',
            DB::raw('(SELECT users.name FROM invoice_logg L INNER JOIN users ON users.id = L.next_user_id WHERE L.invoice_id = invoices.id ORDER BY L.created_at DESC LIMIT 1) as user')
        )
        ->join('suppliers', 'suppliers.id', '=', 'invoices.supplier_id')
        ->whereIn(
            DB::raw('(SELECT STATE_ID FROM invoice_logg WHERE invoice_id = invoices.id ORDER BY created_at DESC LIMIT 1)'),
            [1, 3, 4, 5]
        )
        ->orderBy('invoices.due_date', 'asc')
        ->get();          
        return $invoices;
    }

  public function getInvoicesList(){
        $information_datas=DB::SELECT("SELECT    i.id AS id,
                                            i.file AS file,
                                            i.number AS number,
                                            s.name AS supplier,
                                            s.nit AS supplier_nit,
                                            DATE_FORMAT(i.created_at,'%Y-%m-%d') AS created_at,
                                            FORMAT(i.total,2) AS total,
                                            st.name AS state,
                                            i.currency AS currency,
                                            i.concept AS concept,
                                            i.egress AS egress,
                                            i.revision AS revision,
                                            u.name AS name
                                            FROM invoices i
                                            INNER JOIN suppliers s
                                            ON s.id=i.supplier_id
                                            INNER JOIN invoice_logg l 
                                            ON l.invoice_id = i.id
                                            INNER JOIN users u
                                            ON u.id= l.next_user_id AND l.id = (SELECT MAX(id) FROM invoice_logg l WHERE l.invoice_id = i.id)
                                            INNER JOIN invoice_states st
                                            ON st.id=l.state_id
                                            ORDER BY i.created_at DESC LIMIT 10");
        return $information_datas;
  }
}
