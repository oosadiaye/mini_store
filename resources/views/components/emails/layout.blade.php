<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        body {
            background-color: #f3f4f6;
            font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #374151;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-top: 40px;
            margin-bottom: 40px;
        }
        .header {
            text-align: center;
            background-color: #ffffff;
        }
        .banner {
            width: 100%;
            height: auto;
            display: block;
        }
        .content {
            padding: 32px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Banner/Header -->
        <div class="header">
            @php
                $banner = \App\Models\GlobalSetting::where('key', 'email_banner')->value('value');
            @endphp
            
            @if($banner && \Illuminate\Support\Facades\Storage::disk('public')->exists($banner))
                <img src="{{ \Illuminate\Support\Facades\Storage::url($banner) }}" alt="Welcome" class="banner">
            @else
                <!-- Fallback to App Name if no banner -->
                <div style="padding: 20px;">
                    <h1 style="margin: 0; color: #111827; font-size: 24px;">{{ config('app.name') }}</h1>
                </div>
            @endif
        </div>

        <!-- content -->
        <div class="content">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
