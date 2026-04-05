<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>We received your project request</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f4; margin: 0; padding: 0; color: #0f172a; }
        .wrapper { max-width: 580px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(15,23,42,0.08); overflow: hidden; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 36px 40px; text-align: center; }
        .header-logo { height: 44px; width: auto; object-fit: contain; margin-bottom: 16px; display: block; margin-left: auto; margin-right: auto; }
        .header h1 { margin: 0; font-size: 24px; color: #fff; font-weight: 700; }
        .header p  { margin: 8px 0 0; font-size: 14px; color: rgba(255,255,255,0.55); }
        .badge { display: inline-block; background: #f97316; color: #fff; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; border-radius: 20px; padding: 4px 14px; margin-bottom: 12px; }
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
        .status-pill { display: inline-block; background: #fef3c7; color: #92400e; font-size: 11px; font-weight: 700; border-radius: 20px; padding: 3px 10px; border: 1px solid #fde68a; }
    </style>
</head>
<body>
    @php
        $settings = \App\Models\Setting::instance();
        $appName  = config('app.name');
    @endphp
    <div class="wrapper">
        <div class="header">
            @if ($settings->logoUrl())
                <img src="{{ $settings->logoUrl() }}" class="header-logo" alt="{{ $appName }}">
            @endif
            <span class="badge">Request Received</span>
            <h1>We've got your project!</h1>
            <p>Our team is on it — expect a response within 24 hours.</p>
        </div>

        <div class="body">
            <p class="greeting">Hi {{ $project->client->name }},</p>
            <p>
                Thank you for submitting your project to <span class="accent">{{ $appName }}</span>!
                We've received your request and it's now under review by our team.
                We'll reach out within <strong>24 hours</strong> on business days to discuss next steps.
            </p>

            {{-- Project summary --}}
            <div class="summary-box">
                <div class="row">
                    <span class="key">Project</span>
                    <span class="val">{{ $project->title }}</span>
                </div>
                <div class="row">
                    <span class="key">Status</span>
                    <span class="val"><span class="status-pill">Pending Review</span></span>
                </div>
                <div class="row">
                    <span class="key">Submitted</span>
                    <span class="val">{{ $project->created_at->format('M j, Y \a\t g:i A') }}</span>
                </div>
            </div>

            <p><strong>What happens next?</strong></p>
            <ul class="steps">
                <li><span class="num">1</span>Our project team reviews your requirements and assigns the best specialist for your needs.</li>
                <li><span class="num">2</span>We schedule a discovery call to align on scope, timeline, and budget.</li>
                <li><span class="num">3</span>You'll receive a detailed proposal and we kick off as soon as you approve.</li>
            </ul>

            <p style="margin-bottom:24px;">You can check the status of your project at any time from your dashboard.</p>

            <a href="{{ route('client.projects.index') }}" class="cta-btn">View My Projects →</a>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            <p style="margin-top:6px;">
                Questions? Reply to this email or visit
                <a href="{{ url('/contact') }}" style="color:#f97316;text-decoration:none;">our contact page</a>.
            </p>
        </div>
    </div>
</body>
</html>
