<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Lead</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f4; margin: 0; padding: 0; color: #0f172a; }
        .wrapper { max-width: 580px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(15,23,42,0.08); overflow: hidden; }
        .header { background: #0f172a; padding: 28px 40px; display: flex; align-items: center; gap: 16px; }
        .header-logo { height: 40px; width: auto; object-fit: contain; }
        .header-text h1 { margin: 0; font-size: 20px; color: #fff; font-weight: 600; }
        .header-text p  { margin: 4px 0 0; font-size: 13px; color: rgba(255,255,255,0.5); }
        .badge { display: inline-block; background: #f97316; color: #fff; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; padding: 3px 10px; border-radius: 20px; margin-bottom: 20px; }
        .body { padding: 32px 40px; }
        .label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #f97316; margin: 0 0 4px; }
        .value { font-size: 15px; color: #0f172a; margin: 0 0 22px; }
        .message-box { background: #f8f7f6; border-left: 3px solid #f97316; border-radius: 0 8px 8px 0; padding: 16px 20px; font-size: 15px; line-height: 1.7; color: #44403c; margin: 0 0 28px; white-space: pre-wrap; }
        .cta-btn { display: inline-block; background: #f97316; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 600; }
        .footer { border-top: 1px solid #f0ece8; padding: 20px 40px; font-size: 12px; color: #a8a29e; }
    </style>
</head>
<body>
    @php
        $settings   = \App\Models\Setting::instance();
        $typeLabel  = $lead->project_type
            ? (\App\Models\Lead::projectTypes()[$lead->project_type] ?? $lead->project_type)
            : 'Not specified';
    @endphp
    <div class="wrapper">
        <div class="header">
            @if ($settings->logoUrl())
                <img src="{{ $settings->logoUrl() }}" class="header-logo" alt="{{ config('app.name') }}">
            @endif
            <div class="header-text">
                <h1>New Lead Submission</h1>
                <p>{{ config('app.name') }} · {{ now()->format('M j, Y \a\t g:i A') }}</p>
            </div>
        </div>

        <div class="body">
            <span class="badge">🔥 New Lead</span>

            <p class="label">Name</p>
            <p class="value">{{ $lead->name }}</p>

            <p class="label">Email</p>
            <p class="value"><a href="mailto:{{ $lead->email }}" style="color:#f97316;text-decoration:none;">{{ $lead->email }}</a></p>

            <p class="label">Project Type</p>
            <p class="value">{{ $typeLabel }}</p>

            <p class="label">Message</p>
            <div class="message-box">{{ $lead->message }}</div>

            <a href="{{ route('admin.leads.show', $lead) }}" class="cta-btn">View Lead in Dashboard →</a>
        </div>

        <div class="footer">
            This lead was submitted via the quick inquiry form on {{ config('app.url') }}. Reply to respond directly to {{ $lead->name }}.
        </div>
    </div>
</body>
</html>
