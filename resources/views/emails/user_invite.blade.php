<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to Our System</title>
</head>
<body style="background:#f4f6f8; padding:20px; font-family: Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" style="background:#ffffff; padding:20px; border-radius:6px;">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding-bottom:20px;">
                            <h2 style="color:#2563eb;">🚀 Welcome to Our System</h2>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="color:#333;">
                            <p>Hello <strong>{{ $user->name }}</strong>,</p>

                            <p>Your account has been created successfully.</p>

                            <p><strong>Login Details:</strong></p>

                            <table cellpadding="6">
                                <tr>
                                    <td>Email:</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td>Password:</td>
                                    <td>{{ $password }}</td>
                                </tr>
                            </table>

                            <p style="margin-top:20px;">
                                <a href="{{ url('/login') }}"
                                   style="background:#2563eb; color:#fff; padding:10px 20px; text-decoration:none; border-radius:4px;">
                                   Login Now
                                </a>
                            </p>

                            <p style="margin-top:30px;">
                                Thanks,<br>
                                <strong>Team {{ config('app.name') }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding-top:20px; color:#999; font-size:12px;">
                            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
