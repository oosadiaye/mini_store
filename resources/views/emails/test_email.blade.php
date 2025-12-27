<!DOCTYPE html>
<html>
<head>
    <title>Test Email</title>
</head>
<body>
    <h1>Test Email from {{ config('app.name') }}</h1>
    <p>If you are reading this, your email configuration is working correctly!</p>
    <p>Sent at: {{ now() }}</p>
</body>
</html>
