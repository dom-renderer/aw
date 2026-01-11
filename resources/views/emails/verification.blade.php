<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email Address</title>
</head>
<body>
    <h1>Hello, {{ $user->name }}</h1>
    <p>Please click the link below to verify your email address:</p>
    <a href="{{ route('verification.verify', ['token' => $token]) }}">Verify Email</a>
    <p>This link will expire in 30 minutes.</p>
    <p>If you did not create an account, no further action is required.</p>
</body>
</html>
