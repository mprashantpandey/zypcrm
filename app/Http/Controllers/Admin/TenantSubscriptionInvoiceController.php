<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TenantInvoiceReceiptMail;
use App\Models\Tenant;
use App\Models\TenantSubscriptionInvoice;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TenantSubscriptionInvoiceController extends Controller
{
    public function downloadPdf(Tenant $tenant, TenantSubscriptionInvoice $invoice): Response
    {
        abort_unless($invoice->tenant_id === $tenant->id, 404);

        $invoice->loadMissing(['tenant.users', 'plan', 'subscription']);
        $pdf = Pdf::loadView('pdf.tenant-subscription-invoice', [
            'invoice' => $invoice,
            'tenant' => $tenant,
        ]);

        AuditLogger::log(
            action: 'tenant.subscription.invoice.pdf_downloaded',
            tenantId: $tenant->id,
            entityType: TenantSubscriptionInvoice::class,
            entityId: $invoice->id,
            metadata: ['invoice_no' => $invoice->invoice_no]
        );

        return $pdf->download('invoice-'.$invoice->invoice_no.'.pdf');
    }

    public function sendReceipt(Tenant $tenant, TenantSubscriptionInvoice $invoice): RedirectResponse
    {
        abort_unless($invoice->tenant_id === $tenant->id, 404);

        $invoice->loadMissing(['tenant.users', 'plan', 'subscription']);

        $recipient = (string) ($tenant->email ?: $tenant->users()
            ->where('role', 'library_owner')
            ->whereNotNull('email')
            ->orderBy('id')
            ->value('email'));

        if ($recipient === '') {
            return back()->with('message', 'No recipient email found for this library.');
        }

        try {
            Mail::to($recipient)->queue(new TenantInvoiceReceiptMail($invoice));

            $invoice->increment('receipt_email_attempts');
            $invoice->forceFill(['receipt_emailed_at' => now()])->save();

            AuditLogger::log(
                action: 'tenant.subscription.invoice.email_queued',
                tenantId: $tenant->id,
                entityType: TenantSubscriptionInvoice::class,
                entityId: $invoice->id,
                metadata: ['invoice_no' => $invoice->invoice_no, 'recipient' => $recipient]
            );

            return back()->with('message', 'Invoice receipt email queued for '.$recipient.'.');
        } catch (\Throwable $e) {
            Log::warning('Failed to send tenant invoice receipt email', [
                'tenant_id' => $tenant->id,
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            $invoice->increment('receipt_email_attempts');

            return back()->with('message', 'Could not send email receipt. Please verify mail settings.');
        }
    }
}
