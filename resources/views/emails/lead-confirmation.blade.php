<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>We got your message</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f4; margin: 0; padding: 0; color: #0f172a; }
        .wrapper { max-width: 580px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(15,23,42,0.08); overflow: hidden; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 36px 40px; text-align: center; }
        .header-logo { height: 44px; width: auto; object-fit: contain; margin-bottom: 16px; display: block; margin-left: auto; margin-right: auto; }
        .header h1 { margin: 0; font-size: 24px; color: #fff; font-weight: 700; }
        .header p  { margin: 8px 0 0; font-size: 14px; color: rgba(255,255,255,0.55); }
        .body { padding: 36px 40px; }
        .greeting { font-size: 18px; font-weight: 600; color: #0f172a; margin: 0 0 16px; }
        .body p { font-size: 15px; line-height: 1.75; color: #44403c; margin: 0 0 16px; }
        .summary-box { background: #fafaf9; border: 1px solid #e7e5e4; border-radius: 12px; padding: 20px 24px; margin: 24px 0; }
        .summary-box .row { display: flex; justify-content: space-between; gap: 12px; padding: 8px 0; border-bottom: 1px solid #f0ede9; font-size: 14px; }
        .summary-box .row:last-child { border-bottom: none; padding-bottom: 0; }
        .summary-box .row .key { color: #78716c; font-weight: 600; white-space: nowrap; }
        .summary-box .row .val { color: #0f172a; text-align: right; }
        .steps { list-style: none; margin: 0 0 24px; padding: 0; }
        .steps li { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; font-size: 14px; color: #44403c; line-height: 1.6; }
        .steps li .num { background: #f97316; color: #fff; font-size: 11px; font-weight: 700; border-radius: 50%; height: 22px; width: 22px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
        .cta-btn { display: inline-block; background: #f97316; color: #fff; text-decoration: none; padding: 13px 28px; border-radius: 10px; font-size: 14px; font-weight: 600; }
        .footer { border-top: 1px solid #f0ece8; padding: 20px 40px; font-size: 12px; color: #a8a29e; text-align: center; }
        .accent { color: #f97316; font-weight: 600; }
    </style>
</head>
<body>
    @php
        $settings  = \App\Models\Setting::instance();
        $typeLabel = $lead->project_type
            ? (\App\Models\Lead::projectTypes()[$lead->project_type] ?? $lead->project_type)
            : 'General Inquiry';
        $appName   = config('app.name');
    @endphp
    <div class="wrapper">
        <div class="header">
            @if ($settings->logoUrl())
                <img src="{{ $settings->logoUrl() }}" class="header-logo" alt="{{ $appName }}">
            @endif
            <h1>We've received your inquiry!</h1>
            <p>A real person will review it within 24 hours.</p>
        </div>

        <div class="body">
            <p class="greeting">Hi {{ $lead->name }},</p>
            <p>
                Thank you for reaching out to <span class="accent">{{ $appName }}</span>!
                Your project inquiry has been received and is now in our pipeline.
                We typically respond within <strong>24 hours</strong> on business days.
            </p>

            {{-- Summary of what they sent --}}
            <div class="summary-box">
                <div class="row">
                    <span class="key">Project Type</span>
                    <span class="val">{{ $typeLabel }}</span>
                </div>
                <div class="row">
                    <span class="key">Your Message</span>
                    <span class="val" style="max-width:300px;">{{ Str::limit($lead->message, 100) }}</span>
                </div>
                <div class="row">
                    <span class="key">Submitted</span>
                    <span class="val">{{ $lead->created_at->format('M j, Y \a\t g:i A') }}</span>
                </div>
            </div>

            <p><strong>What happens next?</strong></p>
            <ul class="steps">
                <li><span class="num">1</span>Our team reviews your inquiry and matches you with the right specialists.</li>
                <li><span class="num">2</span>We reach out to schedule a free discovery call to understand your goals.</li>
                <li><span class="num">3</span>We prepare a tailored proposal with timeline and pricing.</li>
            </ul>

            <p style="margin-bottom:24px;">In the meantime, explore our portfolio to see what we've built for clients just like you.</p>

            <a href="{{ url('/portfolio') }}" class="cta-btn">View Our Portfolio →</a>
        </div>

        <div class="footer">
            You're receiving this because you submitted an inquiry at <a href="{{ config('app.url') }}" style="color:#f97316;">{{ config('app.url') }}</a>.
            If you didn't submit this form, you can safely ignore this email.
        </div>
    </div>
</body>
</html>
