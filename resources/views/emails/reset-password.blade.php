<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Your Password</title>
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
            background: #ffc107;
            color: #333;
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
            background: #ffc107;
            color: #333;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
        }
        .button:hover {
            background: #e0a800;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 40px;
            text-align: center;
            color: #999;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px 16px;
            margin: 20px 0;
            border-radius: 4px;
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
            <h1>🔑 Reset Your Password</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Hello {{ $user->name }}!</h2>
            
            <p>We received a request to reset your password for <strong>{{ config('app.name') }}</strong>.</p>
            
            <div class="warning">
                <strong>⚠️ Important:</strong> This link will expire in <strong>{{ $expire ?? 60 }}</strong> minutes.
            </div>
            
            <p>Click the button below to set a new password:</p>
            
            <p style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">🔐 Reset Password</a>
            </p>
            
            <p style="color: #999; font-size: 14px; border-top: 1px solid #eee; padding-top: 20px;">
                If you didn't request a password reset, you can safely ignore this email and your account will remain secure.
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