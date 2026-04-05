<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        /* Reset & Base */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #1e293b;
            background: #fff;
            position: relative;
        }

        /* Page */
        .page {
            padding: 40px 50px;
            position: relative;
        }

        /* PAID Watermark */
        .watermark {
            position: fixed;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 120px;
            font-weight: 900;
            color: rgba(16, 185, 129, 0.10);
            letter-spacing: 12px;
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }
        .header-left {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .header-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            text-align: right;
        }
        .company-logo {
            max-height: 60px;
            max-width: 180px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .company-info {
            font-size: 11px;
            color: #64748b;
            line-height: 1.7;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: 800;
            color: #f97316;
            letter-spacing: -0.5px;
        }
        .invoice-number {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            margin-top: 4px;
        }
        .invoice-date {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }
        .status-paid {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .status-partial {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .status-unpaid {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 2px solid #f1f5f9;
            margin: 28px 0;
        }

        /* Client & Project Info */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-col {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .info-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            margin-bottom: 6px;
        }
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
        }
        .info-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Amount Table */
        .amount-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }
        .amount-table th {
            background: #f8fafc;
            padding: 12px 16px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }
        .amount-table th:last-child {
            text-align: right;
        }
        .amount-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }
        .amount-table td:last-child {
            text-align: right;
            font-weight: 700;
            font-size: 15px;
        }
        .amount-table .total-row td {
            border-top: 2px solid #0f172a;
            border-bottom: none;
            font-weight: 800;
            font-size: 16px;
            padding-top: 16px;
        }
        .amount-table .converted {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
        }
        .amount-table .rate-info {
            font-size: 9px;
            color: #94a3b8;
        }

        /* Validity */
        .validity-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 28px;
        }
        .validity-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #92400e;
        }
        .validity-date {
            font-size: 14px;
            font-weight: 700;
            color: #78350f;
            margin-top: 2px;
        }

        /* Bank Details */
        .bank-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 28px;
        }
        .bank-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            margin-bottom: 8px;
        }
        .bank-text {
            font-size: 12px;
            color: #334155;
            line-height: 1.8;
            white-space: pre-line;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    {{-- PAID Watermark --}}
    @if ($invoice->status === 'paid')
        <div class="watermark">PAID ✔</div>
    @endif

    <div class="page">
        {{-- Header --}}
        <div class="header">
            <div class="header-left">
                @if ($settings->logoFileExists())
                    <img src="{{ $settings->logoAbsolutePath() }}" class="company-logo" alt="Logo">
                    <br>
                @endif
                @if ($settings->company_name)
                    <div class="company-name">{{ $settings->company_name }}</div>
                @endif
                <div class="company-info">
                    @if ($settings->address){{ $settings->address }}<br>@endif
                    @if ($settings->phone)Phone: {{ $settings->phone }}<br>@endif
                    @if ($settings->email)Email: {{ $settings->email }}@endif
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</div>
                <div class="invoice-date">Issued: {{ $invoice->created_at->format('F d, Y') }}</div>
                <div>
                    <span class="status-badge {{ $invoice->status === 'paid' ? 'status-paid' : ($invoice->status === 'partial' ? 'status-partial' : 'status-unpaid') }}">
                        {{ strtoupper($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        <hr class="divider">

        {{-- Client & Project --}}
        <div class="info-grid">
            <div class="info-col">
                <div class="info-label">Bill To</div>
                <div class="info-value">{{ $invoice->project->client->name ?? 'N/A' }}</div>
                @if ($invoice->project->client->email ?? null)
                    <div class="info-sub">{{ $invoice->project->client->email }}</div>
                @endif
            </div>
            <div class="info-col" style="text-align: right;">
                <div class="info-label">Project</div>
                <div class="info-value">{{ $invoice->project->title }}</div>
                @if ($invoice->project->freelancer)
                    <div class="info-sub">Freelancer: {{ $invoice->project->freelancer->name }}</div>
                @endif
            </div>
        </div>

        {{-- Amount Table --}}
        <table class="amount-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Currency</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->project->title }}</td>
                    <td>{{ $invoice->currency }}</td>
                    <td>{{ $invoice->formattedAmount() }}</td>
                </tr>
                @if ($invoice->converted_amount)
                    <tr>
                        <td class="converted" colspan="2">
                            Converted amount
                            <span class="rate-info">(Rate: 1 USD = {{ number_format($invoice->exchange_rate, 2) }} TZS)</span>
                        </td>
                        <td class="converted">
                            ≈ {{ $invoice->currency === 'USD' ? 'TZS ' . number_format($invoice->converted_amount, 2) : '$' . number_format($invoice->converted_amount, 2) }}
                        </td>
                    </tr>
                @endif
                @if ($invoice->paid_amount > 0)
                    <tr>
                        <td colspan="2" style="color: #16a34a; font-weight: 600;">Amount Paid</td>
                        <td style="color: #16a34a; font-weight: 700;">{{ $invoice->formattedPaidAmount() }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td colspan="2">{{ $invoice->status === 'paid' ? 'Total (Paid in Full)' : 'Balance Due' }}</td>
                    <td {{ $invoice->status !== 'paid' ? 'style=color:#dc2626' : '' }}>
                        {{ $invoice->status === 'paid' ? $invoice->formattedAmount() : $invoice->formattedRemainingAmount() }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Validity / Expiry --}}
        @if ($invoice->expires_at)
            <div class="validity-box">
                <div class="validity-label">Valid Until</div>
                <div class="validity-date">{{ $invoice->expires_at->format('F d, Y') }}</div>
            </div>
        @endif

        {{-- Bank Details --}}
        @if ($settings->bank_details)
            <div class="bank-box">
                <div class="bank-title">Payment Information</div>
                <div class="bank-text">{{ $settings->bank_details }}</div>
            </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            @if ($settings->company_name)
                <strong>{{ $settings->company_name }}</strong><br>
            @endif
            Thank you for your business. This invoice was generated automatically.
        </div>
    </div>
</body>
</html>
