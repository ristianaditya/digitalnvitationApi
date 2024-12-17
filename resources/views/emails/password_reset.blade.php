<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 0;
            background-color: #f9f7f6;
            color: #0f172a;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #F4F0FD;
            color: #0f172a;
            text-align: center;
            padding: 25px 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: normal;
        }
        .content {
            padding: 20px 30px;
            line-height: 1.8;
        }
        .content h2 {
            font-size: 22px;
            margin-top: 0;
        }
        .content p {
            margin: 15px 0;
        }
        .otp-box {
            text-align: center;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
            color: #c39a8a;
            background-color: #fdf8f7;
            border: 2px dashed #c39a8a;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .footer {
            background-color: #f9f7f6;
            color: #7d7d7d;
            text-align: center;
            padding: 15px;
            font-size: 12px;
        }
        .footer a {
            color: #c39a8a;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>
        <!-- Content -->
        <div class="content">
            <h2>Hello {{ $email }},</h2>
            <p>We received a request to reset your password for your InvitePlus account. Click the link below to reset your password:</p>
            <p><a href="{{ env('FRONTEND_URL') }}/reset-password?email={{ urlencode($email) }}&token={{ $token }}" style="color: #c39a8a; text-decoration: none;">Reset Password</a></p>
            <p>Your reset link is valid for <strong>10 minutes</strong>. Please ensure to complete your password reset promptly.</p>
            <p>If you didn’t request this email, please secure your account by changing your password.</p>
            <p>Thank you for using InvitePlus!</p>
            <p>Warm regards,</p>
            <p><strong>The InvitePlus Team</strong></p>
        </div>
        <!-- Footer -->
        <div class="footer">
            <p>Need assistance? <a href="mailto:invite.plus.business@gmail.com">Contact Support</a>.</p>
            <p>&copy; 2024 InvitePlus. All rights reserved.</p>
        </div>
    </div>
</body>
</html>