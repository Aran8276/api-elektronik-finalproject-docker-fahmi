<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Halo, {{ $name }} </h2>
    <p>Kami menerima permintaan reset password untuk akun Anda.</p>
    <p>Gunakan token berikut untuk mengatur ulang password Anda:</p>
    <h3>{{ $token }}</h3>
    <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
</body>
</html>
