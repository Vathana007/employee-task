<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .content h2 {
            color: #1f2937;
            font-size: 20px;
            margin-top: 0;
        }
        .message {
            color: #6b7280;
            margin: 20px 0;
            line-height: 1.8;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin: 30px 0;
            text-align: center;
        }
        .reset-button:hover {
            background: linear-gradient(135deg, #0284c7 0%, #0891b2 100%);
        }
        .code-section {
            background-color: #f3f4f6;
            border-left: 4px solid #0ea5e9;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .code-section p {
            margin: 5px 0;
            font-size: 14px;
            color: #6b7280;
        }
        .token {
            font-family: 'Courier New', monospace;
            background: #ffffff;
            padding: 10px;
            border-radius: 4px;
            word-break: break-all;
            color: #1f2937;
            margin: 10px 0;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
        }
        .expiry-warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #92400e;
            font-size: 14px;
        }
        .company-name {
            color: #0ea5e9;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔐 Reset Your Password</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Hello {{ $user->name }},</h2>

            <div class="message">
                <p>We received a request to reset the password for your account. If you didn't make this request, you can safely ignore this email.</p>
                
                <p>To reset your password, click the button below or use the link provided:</p>
            </div>

            <!-- Reset Button -->
            <center>
                <a href="{{ $resetUrl }}" class="reset-button">Reset Your Password</a>
            </center>

            <!-- Alternative Link -->
            <div class="code-section">
                <p><strong>Or copy and paste this link in your browser:</strong></p>
                <div class="token">{{ $resetUrl }}</div>
            </div>

            <!-- Expiry Warning -->
            <div class="expiry-warning">
                ⚠️ <strong>This link will expire in 60 minutes.</strong> If it expires, you can request a new one on the forgot password page.
            </div>

            <!-- Additional Info -->
            <div class="message" style="margin-top: 30px; color: #9ca3af; font-size: 13px;">
                <p><strong>For security reasons:</strong></p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Never share this link with anyone</li>
                    <li>This link is only valid for 60 minutes</li>
                    <li>If you didn't request this, your password remains unchanged</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} <span class="company-name">{{ config('app.name') }}</span>. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>If you need assistance, visit our support page or contact us.</p>
        </div>
    </div>
</body>
</html>