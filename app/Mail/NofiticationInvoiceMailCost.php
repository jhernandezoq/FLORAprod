<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Invoice;

class NofiticationInvoiceMailCost extends Mailable
{
    use Queueable, SerializesModels;

    public $name;

    public $invoice;

    public $id_user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, Invoice $invoice, $id_user)
    {
        $this->name = $name;
        $this->invoice = $invoice;
        $this->id_user = $id_user;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Factura::'.$this->invoice->number." " . $this->invoice->supplier->name)->view('mail.notificationEmailInvoiceCost');
    }
}