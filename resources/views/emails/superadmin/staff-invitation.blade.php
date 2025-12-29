<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        body {
            background-color: #f6f9fc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #334155;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #0f172a; /* Slate 900 */
            padding: 40px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px;
        }
        .welcome-text {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 24px;
            line-height: 1.6;
        }
        .details-box {
            background-color: #f1f5f9;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 32px;
            border: 1px solid #e2e8f0;
        }
        .detail-row {
            margin-bottom: 12px;
        }
        .detail-row:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            color: #64748b;
            display: block;
            margin-bottom: 4px;
        }
        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            font-family: monospace;
        }
        .cta-button {
            display: inline-block;
            background-color: #2563eb; /* Blue 600 */
            color: #ffffff;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }
        .cta-button:hover {
            background-color: #1d4ed8;
        }
        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            margin-top: 32px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Welcome to the Team!</h1>
            </div>
            
            <div class="content">
                <p class="welcome-text">
                    Hello {{ $user->name }},<br><br>
                    You have been invited to join <strong>{{ config('app.name') }}</strong> as a <strong>{{ $roleName }}</strong>.
                    We are excited to have you on board.
                </p>

                <div class="details-box">
                    <div class="detail-row">
                        <span class="detail-label">Your Email</span>
                        <span class="detail-value">{{ $user->email }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Temporary Password</span>
                        <span class="detail-value">{{ $password }}</span>
                    </div>
                </div>

                <a href="{{ $loginUrl }}" class="cta-button">Log In to Dashboard</a>
                
                <p style="text-align: center; margin-top: 24px; font-size: 14px; color: #64748b;">
                    Please change your password after your first login.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
