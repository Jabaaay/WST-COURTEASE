<!DOCTYPE html>
<html>
<head>
    <title>Tenant Account Approved</title>
</head>
 <body>
    <h2>Your Tenant Account Has Been Approved</h2>
    <p>Hello {{ $tenant->name }},</p>
    <p>Your tenant application has been approved. Below are your login credentials:</p>
    <ul>
        <li><strong>Domain:</strong> {{ $tenant->domain }}</li>
        <li><strong>Email:</strong> {{ $tenant->email }}</li>
        <li><strong>Password:</strong> {{ $password }}</li>
        <li><strong>Database Name:</strong> {{ $database_name }}</li>
        <li><strong>Login URL:</strong> <a href="http://{{ $tenant->domain }}.localhost:8000/tenant/login">http://{{ $tenant->domain }}.localhost:8000/login</a></li>
    </ul>
    <p>Please keep these credentials secure and change your password after your first login.</p>
    <p>Best regards,<br>Your Application Team</p>
</body>

</html>