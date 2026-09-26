<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }

        .button {
            display: inline-block;
            background: #0ea5e9;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Verify Your Email</h1>
        </div>
        <div class="content">
            <p>Hi {{ $user->name }},</p>
            <p>Thank you for registering! Please verify your email address to complete your account setup.</p>

            <center>
                <a href="{{ $verificationUrl }}" class="button">Verify Email</a>
            </center>

            <p>Or copy and paste this link:</p>
            <p><code>{{ $verificationUrl }}</code></p>

            <p style="color: #9ca3af; font-size: 12px;">
                This link expires in 24 hours.
            </p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Your App Name</p>
        </div>
    </div>
</body>

</html>