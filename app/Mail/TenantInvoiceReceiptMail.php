<?php

namespace App\Mail;

use App\Models\TenantSubscriptionInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantInvoiceReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public TenantSubscriptionInvoice $invoice)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Receipt - '.$this->invoice->invoice_no
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-invoice-receipt',
            with: [
                'invoice' => $this->invoice,
                'tenant' => $this->invoice->tenant,
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.tenant-subscription-invoice', [
            'invoice' => $this->invoice->loadMissing(['tenant.users', 'plan', 'subscription']),
            'tenant' => $this->invoice->tenant,
        ]);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(),
                'invoice-'.$this->invoice->invoice_no.'.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
