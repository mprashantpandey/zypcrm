<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; }
        .wrap { width: 100%; }
        .header { margin-bottom: 20px; }
        .title { font-size: 22px; font-weight: 700; margin: 0; }
        .muted { color: #475569; }
        .grid { width: 100%; border-collapse: collapse; margin-top: 14px; }
        .grid td, .grid th { border: 1px solid #cbd5e1; padding: 8px; text-align: left; vertical-align: top; }
        .grid th { background: #f8fafc; font-weight: 700; }
        .right { text-align: right; }
        .summary { margin-top: 12px; width: 50%; margin-left: auto; border-collapse: collapse; }
        .summary td { border: 1px solid #cbd5e1; padding: 8px; }
        .summary tr:first-child td { background: #f8fafc; font-weight: 700; }
        .footer { margin-top: 22px; color: #475569; font-size: 11px; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <p class="title">Invoice</p>
            <p class="muted">Invoice No: {{ $invoice->invoice_no }}</p>
        </div>

        <table style="width:100%; border-collapse: collapse;">
            <tr>
                <td style="width:50%; vertical-align: top;">
                    <strong>Bill To</strong><br>
                    {{ $tenant->name }}<br>
                    @if($tenant->email){{ $tenant->email }}<br>@endif
                    @if($tenant->phone){{ $tenant->phone }}<br>@endif
                    @if($tenant->address){{ $tenant->address }}@endif
                </td>
                <td style="width:50%; vertical-align: top;">
                    <strong>Invoice Details</strong><br>
                    Date: {{ $invoice->created_at?->format('M d, Y') }}<br>
                    Due Date: {{ $invoice->due_date?->format('M d, Y') ?? 'N/A' }}<br>
                    Status: {{ ucfirst($invoice->status) }}<br>
                    @if($invoice->paid_at)Paid At: {{ $invoice->paid_at->format('M d, Y h:i A') }}<br>@endif
                    @if($invoice->payment_method)Method: {{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}@endif
                </td>
            </tr>
        </table>

        <table class="grid">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Plan</th>
                    <th class="right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Subscription billing invoice</td>
                    <td>{{ $invoice->plan->name ?? 'Tenant subscription' }}</td>
                    <td class="right">{{ $invoice->currency_code }} {{ number_format((float) $invoice->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="summary">
            <tr>
                <td>Total</td>
                <td class="right">{{ $invoice->currency_code }} {{ number_format((float) $invoice->amount, 2) }}</td>
            </tr>
        </table>

        @if($invoice->notes)
            <p class="footer"><strong>Notes:</strong> {{ $invoice->notes }}</p>
        @endif
    </div>
</body>
</html>

