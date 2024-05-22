
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body>
    <div style="text-align: center; padding: 20px;">
        <h1>Email Verification</h1>
        <p>Thank you for signing up! To complete your registration, please click the button below to verify your email address:</p>
        <a href="{{ route('vendor.verificationConfirmation',$data)}}" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Verify Email</a>
        <p></p>
        <p></p>
        <p>If you did not sign up for this account, you can ignore this email.</p>
    </div>
</body>
</html>