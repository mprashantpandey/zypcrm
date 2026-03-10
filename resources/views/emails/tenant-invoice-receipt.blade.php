<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice Receipt</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="padding:20px 24px;border-bottom:1px solid #e2e8f0;">
                            <h2 style="margin:0;font-size:20px;">Invoice Receipt</h2>
                            <p style="margin:6px 0 0;color:#475569;font-size:13px;">Invoice {{ $invoice->invoice_no }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 24px;">
                            <p style="margin:0 0 12px;">Hello {{ $tenant->name }},</p>
                            <p style="margin:0 0 12px;color:#334155;line-height:1.6;">
                                Please find your subscription invoice receipt attached as PDF.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:14px 0;">
                                <tr>
                                    <td style="padding:8px;border:1px solid #e2e8f0;background:#f8fafc;font-weight:600;">Plan</td>
                                    <td style="padding:8px;border:1px solid #e2e8f0;">{{ $invoice->plan->name ?? 'Tenant subscription' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e2e8f0;background:#f8fafc;font-weight:600;">Amount</td>
                                    <td style="padding:8px;border:1px solid #e2e8f0;">{{ $invoice->currency_code }} {{ number_format((float) $invoice->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px;border:1px solid #e2e8f0;background:#f8fafc;font-weight:600;">Status</td>
                                    <td style="padding:8px;border:1px solid #e2e8f0;">{{ ucfirst($invoice->status) }}</td>
                                </tr>
                            </table>

                            <p style="margin:14px 0 0;color:#475569;font-size:12px;">
                                If you have any question regarding this invoice, please reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

