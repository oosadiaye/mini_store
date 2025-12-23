<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
    <meta http-equiv="refresh" content="0;url={{ route('superadmin.dashboard') }}">
    <script>window.location.href = "{{ route('superadmin.dashboard') }}";</script>
</head>
<body>
    <p>Redirecting to admin dashboard...</p>
    <p>If you are not redirected, <a href="/admin/dashboard">click here</a>.</p>
</body>
</html>
