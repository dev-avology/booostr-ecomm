<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mailData['subject'] ?? 'Welcome to your account' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .header {
            background-color: #528CD7;
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }
        .header img {
            max-height: 55px;
            max-width: 220px;
            margin-bottom: 12px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        .content {
            padding: 35px 30px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #222222;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 18px 20px;
            margin: 25px 0;
        }
        .info-row {
            margin: 6px 0;
            font-size: 14px;
        }
        .info-row strong {
            color: #4a5568;
            display: inline-block;
            width: 110px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 30px 0 20px;
        }
        .btn {
            background-color: #528CD7;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            display: inline-block;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 30px;
            text-align: center;
            font-size: 13px;
            color: #718096;
        }
        .footer a {
            color: #528CD7;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(!empty($mailData['logo']))
                <img src="{{ $mailData['logo'] }}" alt="{{ $mailData['club_name'] ?? 'Store' }}"><br>
            @endif
            <h1>{{ $mailData['club_name'] ?? 'Online Store' }}</h1>
        </div>
        <div class="content">
            <div class="greeting">
                Hello {{ $mailData['first_name'] ?? $mailData['name'] }},
            </div>
            <p>
                Your account for <strong>{{ $mailData['club_name'] ?? 'Online Store' }}</strong> has been successfully created. You can now use your account to place orders, track purchases, and manage your profile.
            </p>

            <div class="info-box">
                <div class="info-row">
                    <strong>Username:</strong> {{ $mailData['username'] ?? '' }}
                </div>
                <div class="info-row">
                    <strong>Email:</strong> {{ $mailData['email'] ?? '' }}
                </div>
                <div class="info-row">
                    <strong>Store:</strong> {{ $mailData['site_url_text'] ?? '' }}
                </div>
            </div>

            <div class="btn-wrapper">
                <a href="{{ $mailData['login_url'] ?? url('/customer/login') }}" class="btn">Log In to Your Account</a>
            </div>

            @if(!empty($mailData['support_email']))
                <p style="font-size: 13px; color: #718096; margin-top: 25px;">
                    If you have any questions or need support, contact us at <a href="mailto:{{ $mailData['support_email'] }}">{{ $mailData['support_email'] }}</a>.
                </p>
            @endif
        </div>
        <div class="footer">
            <p style="margin: 0;">
                &copy; {{ date('Y') }} {{ $mailData['club_name'] ?? 'Store' }}. All rights reserved.
            </p>
            <p style="margin: 5px 0 0;">
                <a href="{{ $mailData['site_url'] ?? url('/') }}">{{ $mailData['site_url_text'] ?? '' }}</a>
            </p>
        </div>
    </div>
</body>
</html>

