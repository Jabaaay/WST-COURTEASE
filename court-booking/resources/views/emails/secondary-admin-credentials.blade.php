<!DOCTYPE html>
<html>
<head>
    <title>Your Secondary Admin Account Credentials</title>
</head>
<body>
    <h2>Welcome to the Court Booking System!</h2>
    
    <p>Dear {{ $name }},</p>
    
    <p>Your secondary admin account has been created with the following credentials:</p>
    
    <ul>
        <li><strong>Email:</strong> {{ $email }}</li>
        <li><strong>Password:</strong> {{ $password }}</li>
        <li><strong>Role:</strong> {{ ucfirst($role) }}</li>
    </ul>
    
    <p>Please login to the system using these credentials. For security reasons, we recommend changing your password after your first login.</p>
    
    <p>You can access the system at: <a href="{{ url('/tenant/login') }}">{{ url('/tenant/login') }}</a></p>
    
    <p>If you have any questions or need assistance, please contact the system administrator.</p>
    
    <p>Best regards,<br>
    Court Booking System Team</p>
</body>
</html> 