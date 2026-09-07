<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #0d6efd;
            color: white;
            padding: 30px 40px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px;
        }
        .content h2 {
            color: #333;
            margin-top: 0;
        }
        .content p {
            color: #666;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            background: #0d6efd;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
        }
        .button:hover {
            background: #0b5ed7;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 40px;
            text-align: center;
            color: #999;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        .badge {
            display: inline-block;
            background: #e9ecef;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>✉️ Verify Your Email</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Hello {{ $user->name }}!</h2>
            
            <p>Thanks for creating an account with <strong>{{ config('app.name') }}</strong>.</p>
            
            <p>Please verify your email address by clicking the button below:</p>
            
            <p style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">✅ Verify Email Address</a>
            </p>
            
            <p style="color: #999; font-size: 14px; border-top: 1px solid #eee; padding-top: 20px;">
                <strong>⚠️ Important:</strong> This link will expire in 60 minutes.
            </p>
            
            <p style="color: #999; font-size: 14px;">
                If you didn't create an account, you can safely ignore this email.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                <br>
                <span class="badge">🔒 Secure Email</span>
            </p>
        </div>
    </div>
</body>
</html>