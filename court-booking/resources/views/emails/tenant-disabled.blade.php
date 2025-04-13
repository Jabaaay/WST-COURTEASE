<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Disabled Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Account Disabled Notification</h2>
    </div>
    
    <div class="content">
        <p>Dear {{ $tenant->name }},</p>
        
        <p>We regret to inform you that your tenant account has been disabled. This means you will no longer have access to your account and its associated features.</p>
        
        <p>If you believe this action was taken in error or would like to discuss the matter further, please contact our support team.</p>
        
        <p>Account Details:</p>
        <ul>
            <li>Tenant Name: {{ $tenant->name }}</li>
            <li>Email: {{ $tenant->email }}</li>
            <li>Domain: {{ $tenant->domain }}</li>
        </ul>
        
        <p>Best regards,<br>
        The CourtEase Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html> 