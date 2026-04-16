<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->invoice_number ?? ('INV-' . str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT)) }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.5;
            margin: 24px;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }
        .subtitle {
            margin: 4px 0 0;
            color: #6b7280;
        }
        .grid {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }
        .grid td {
            padding: 8px 0;
            vertical-align: top;
        }
        .label {
            width: 180px;
            color: #6b7280;
        }
        .amount {
            font-size: 16px;
            font-weight: bold;
        }
        .section {
            margin-top: 18px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin: 0 0 8px;
        }
        .box {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
            background: #f9fafb;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Freelancer Invoice</p>
        <p class="subtitle">{{ $invoice->invoice_number ?? ('INV-' . str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT)) }}</p>
    </div>

    <table class="grid">
        <tr>
            <td class="label">Project</td>
            <td>{{ $invoice->project->title }}</td>
        </tr>
        <tr>
            <td class="label">Freelancer</td>
            <td>{{ $invoice->freelancer->name }}</td>
        </tr>
        <tr>
            <td class="label">Submitted Date</td>
            <td>{{ $invoice->created_at->format('M d, Y H:i') }}</td>
        </tr>
        @if ($invoice->due_date)
            <tr>
                <td class="label">Due Date</td>
                <td>{{ $invoice->due_date->format('M d, Y') }}</td>
            </tr>
        @endif
        <tr>
            <td class="label">Status</td>
            <td>{{ $invoice->statusLabel() }}</td>
        </tr>
        <tr>
            <td class="label">Amount</td>
            <td class="amount">{{ number_format((float) ($invoice->amount ?? 0), 2) }} TZS</td>
        </tr>
    </table>

    <div class="section">
        <p class="section-title">Description</p>
        <div class="box">{{ $invoice->description ?: 'No description provided.' }}</div>
    </div>

    @if ($invoice->rejection_note)
        <div class="section">
            <p class="section-title">Rejection Note</p>
            <div class="box">{{ $invoice->rejection_note }}</div>
        </div>
    @endif
</body>
</html>
