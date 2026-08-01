<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance | {{ setting('site_name', 'BeeG Events') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            margin: 0;
        }
        .maintenance-card {
            text-align: center;
            max-width: 520px;
            padding: 48px 32px;
        }
        .maintenance-icon {
            width: 88px;
            height: 88px;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.15);
            color: #d4af37;
            font-size: 40px;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.06); }
        }
        h1 { font-size: 28px; font-weight: 700; margin-bottom: 12px; }
        p { color: rgba(255,255,255,0.7); font-size: 15px; line-height: 1.6; margin-bottom: 28px; }
        .brand { font-size: 14px; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.4); margin-bottom: 16px; }
        .contact a { color: #d4af37; text-decoration: none; }
        .btn-gold-custom {
            background: #d4af37;
            color: #1a1a2e;
            border: none;
            padding: 10px 28px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn-gold-custom:hover { background: #e0bc4e; color: #1a1a2e; }
        .contact-info { margin-top: 24px; font-size: 13px; color: rgba(255,255,255,0.5); }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <div class="brand">{{ setting('site_name', 'BeeG Events') }}</div>
        <div class="maintenance-icon"><i class="ti ti-tool"></i></div>
        <h1>We'll be back shortly</h1>
        <p>{{ setting('site_tagline', 'We are currently performing scheduled maintenance on our website.') }}</p>
        <a class="btn-gold-custom" href="{{ url('/') }}">Retry</a>
        <div class="contact-info">
            @if(setting('contact_email'))
                Need help? <a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a>
            @endif
        </div>
    </div>
</body>
</html>
