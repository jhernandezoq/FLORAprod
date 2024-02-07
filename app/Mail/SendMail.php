<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $info;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->info = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       switch ($this->info[1]) {
           case 'caso':
            return $this->subject('Caso '.$this->info[2].' registrado en flora para tu gestión')->view('mail.notificationTicketCreate');
               break;
           case 'casor':
            return $this->subject('Caso '.$this->info[2].' solucionado')->view('mail.notificationTicketSolve');
               break;
            case 'updatepassword':
            return $this->subject('Solicitud de cambio de contraseña registrado en flora')->view('mail.Updatepasswords');
               break;
            case 'Factura equivalente':
            return $this->subject('Solicitud de gestión de documento equivalente')->view('mail.notificationEquivalent');
               break;
            case 'reasignacion':
            return $this->subject('Caso '.$this->info[2].' te fue reasignado en flora para tu gestión')->view('mail.notificationTicketReasignacion');
               break;
            case 'actualizacion':
            return $this->subject('Caso '.$this->info[2].' fue actualizado en flora')->view('mail.notificationTicketUpdate');
               break;
            case 'anticipo':
            return $this->subject('Solicitud de gestión de anticipo')->view('mail.notificationAnticipo');
               break;
            case 'anticipogestion':
            return $this->subject('Solicitud de anticipo en proceso de pago')->view('mail.notificationAnticipoProceso');
               break;
            case 'pagoanticipocorreo':
            return $this->subject('Solicitud de gestión de anticipo')->view('mail.notificationAnticipoPagoCorreo');
               break;
            case 'anticipoppago':
            return $this->subject('Solicitud de gestión de anticipo')->view('mail.notificationAnticipoPPago');
               break;
            case 'anticipopago':
            return $this->subject('Solicitud de anticipo pagada')->view('mail.notificationAnticipoPago');
               break;
            case 'legalizacion':
            return $this->subject('Solicitud de gestón de legalización')->view('mail.notificationLegalizacion');
               break;
            case 'anticiporechazo':
            return $this->subject('Solicitud de anticipo rechazada')->view('mail.notificationAnticipoRechazo');
               break;
            case 'legalizacionrechazo':
            return $this->subject('Solicitud de legalización rechazada')->view('mail.notificationLegalizacionRechazo');
               break;
            case 'gastos':
               return $this->subject('Solicitud de gestión de legalización de gastos')->view('mail.notificationLegalizacionGastos');
               break;
            case 'gastosrechazo':
               return $this->subject('Solicitud de legalización de gastos rechazada')->view('mail.notificationLegalizacionGastosRechazo');
               break;
            case 'gastospago':
               return $this->subject('Solicitud de pago realizada')->view('mail.notificationLegalizacionGastosPagada');
               break;
            case 'devoluciones':
               return $this->subject('Solicitud de gestión de devolución')->view('mail.notificationDevolucion');
                  break;
            case 'devolucionrechazo':
                  return $this->subject('Solicitud de devolución rechazada')->view('mail.notificationDevolucionRechazo');
                  break;
            case 'devolucionespago':
                  return $this->subject('Solicitud de devolución aprobada')->view('mail.notificationDevolucionPago');
                  break;
            case 'devolucionupdate':
               return $this->subject('Datos de devolución actualizados')->view('mail.notificationDevolucionActualizacion');
                  break;
            case 'InvoiceReject':
               return $this->subject('Factura::'.$this->info[0]." " . $this->info[3])->view('mail.notificationEmailInvoiceReject');
                  break;
            default:
               # code...
               break;
       }
    }
}
