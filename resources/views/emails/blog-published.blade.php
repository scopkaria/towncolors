<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $post->title }}</title>
    <!--[if mso]>
    <noscript>
      <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">

{{-- Preheader (hidden preview text) --}}
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">
    {{ $excerpt }}
</div>

{{-- Outer wrapper --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background-color:#f4f4f5;border-collapse:collapse;">
    <tr>
        <td align="center" style="padding:32px 16px;">

            {{-- Email card --}}
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"
                   style="max-width:600px;width:100%;border-collapse:collapse;">

                {{-- ── HEADER ── --}}
                <tr>
                    <td align="center"
                        style="background-color:{{ $setting->primary_color ?: '#f97316' }};
                               padding:28px 40px;
                               border-radius:12px 12px 0 0;">
                        @if($setting->logoUrl())
                            <img src="{{ $setting->logoUrl() }}"
                                 alt="{{ $setting->company_name ?: config('app.name') }}"
                                 width="140"
                                 style="display:block;max-height:56px;width:auto;max-width:140px;
                                        object-fit:contain;">
                        @else
                            <p style="margin:0;font-size:22px;font-weight:700;color:#ffffff;
                                      letter-spacing:-0.5px;">
                                {{ $setting->company_name ?: config('app.name') }}
                            </p>
                        @endif
                    </td>
                </tr>

                {{-- ── BODY ── --}}
                <tr>
                    <td style="background-color:#ffffff;padding:40px 40px 32px;">

                        {{-- Label --}}
                        <p style="margin:0 0 16px;font-size:12px;font-weight:600;
                                  letter-spacing:0.08em;text-transform:uppercase;
                                  color:{{ $setting->primary_color ?: '#f97316' }};">
                            New Article
                        </p>

                        {{-- Title --}}
                        <h1 style="margin:0 0 20px;font-size:26px;font-weight:700;line-height:1.3;
                                   color:#0f172a;letter-spacing:-0.5px;">
                            {{ $post->title }}
                        </h1>

                        {{-- Divider --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="border-collapse:collapse;margin-bottom:24px;">
                            <tr>
                                <td style="border-top:2px solid #f1f5f9;font-size:0;line-height:0;">&nbsp;</td>
                            </tr>
                        </table>

                        {{-- Featured image --}}
                        @if($post->featured_image)
                            <img src="{{ Storage::url($post->featured_image) }}"
                                 alt="{{ $post->title }}"
                                 width="520"
                                 style="display:block;width:100%;max-width:520px;height:auto;
                                        border-radius:8px;margin-bottom:28px;object-fit:cover;">
                        @endif

                        {{-- Excerpt --}}
                        @if($excerpt)
                            <p style="margin:0 0 32px;font-size:16px;line-height:1.7;color:#475569;">
                                {{ $excerpt }}
                            </p>
                        @endif

                        {{-- CTA Button --}}
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                               style="border-collapse:collapse;">
                            <tr>
                                <td align="center"
                                    style="background-color:{{ $setting->primary_color ?: '#f97316' }};
                                           border-radius:8px;">
                                    <a href="{{ $postUrl }}"
                                       style="display:inline-block;padding:14px 32px;
                                              font-size:15px;font-weight:600;color:#ffffff;
                                              text-decoration:none;letter-spacing:0.01em;">
                                        Read Full Article &rarr;
                                    </a>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                {{-- ── META BAR ── --}}
                <tr>
                    <td style="background-color:#f8fafc;padding:20px 40px;
                               border-top:1px solid #e2e8f0;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="border-collapse:collapse;">
                            <tr>
                                <td>
                                    <p style="margin:0;font-size:13px;color:#94a3b8;">
                                        Published
                                        {{ $post->published_at ? $post->published_at->format('F j, Y') : now()->format('F j, Y') }}
                                    </p>
                                </td>
                                <td align="right">
                                    <a href="{{ $postUrl }}"
                                       style="font-size:13px;color:{{ $setting->primary_color ?: '#f97316' }};
                                              text-decoration:none;font-weight:600;">
                                        View online
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- ── FOOTER ── --}}
                <tr>
                    <td align="center"
                        style="background-color:#0f172a;padding:28px 40px;
                               border-radius:0 0 12px 12px;">
                        <p style="margin:0 0 8px;font-size:14px;font-weight:600;color:#f8fafc;">
                            {{ $setting->company_name ?: config('app.name') }}
                        </p>
                        @if($setting->address)
                            <p style="margin:0 0 12px;font-size:12px;color:#64748b;">
                                {{ $setting->address }}
                            </p>
                        @endif
                        <p style="margin:0;font-size:12px;color:#475569;line-height:1.6;">
                            You received this email because you subscribed to updates from
                            {{ $setting->company_name ?: config('app.name') }}.
                        </p>
                    </td>
                </tr>

            </table>
            {{-- end card --}}

        </td>
    </tr>
</table>

</body>
</html>
