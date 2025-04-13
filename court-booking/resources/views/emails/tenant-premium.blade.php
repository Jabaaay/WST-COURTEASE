<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Premium Account Upgrade Notification</title>
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
        .premium-badge {
            display: inline-block;
            background-color: #ffd700;
            color: #000;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            margin: 10px 0;
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
        <h2>Premium Account Upgrade</h2>
    </div>
    
    <div class="content">
        <p>Dear {{ $tenant->name }},</p>
        
        <p>We are excited to inform you that your account has been upgraded to <span class="premium-badge">PREMIUM</span> status!</p>
        
        <p>As a premium member, you now have access to:</p>
        <ul>
            <li>Advanced booking features</li>
            <li>Priority customer support</li>
            <li>Extended booking hours</li>
            <li>Additional court management tools</li>
            <li>And much more!</li>
        </ul>
        
        <p>Account Details:</p>
        <ul>
            <li>Tenant Name: {{ $tenant->name }}</li>
            <li>Email: {{ $tenant->email }}</li>
            <li>Domain: {{ $tenant->domain }}</li>
        </ul>
        
        <p>You can start enjoying these premium features immediately by logging into your account.</p>
        
        <p>If you have any questions about your premium features or need assistance, our support team is here to help.</p>
        
        <p>Best regards,<br>
        The CourtEase Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html> 