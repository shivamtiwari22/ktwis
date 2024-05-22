
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Website</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">

    <table style="max-width: 600px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 10px;">
        <tr>
            <td>
                <h2>Welcome to Our Website!</h2>
                <p>Dear {{$data['name']}},</p>
                <p>Thank you for joining us! We're excited to have you as a member of our community.</p>
                <p>Your login credentials are:</p>
                <ul>
                    <li><strong>Email:</strong> {{$data['email']}}</li>
                    <li><strong>Password:</strong> {{$data['password']}}</li>
                </ul>
                <p>Please click on the button below to log in to your account:</p>
                <p style="text-align: center;">
                    <a href="https://green-spark-backup.vercel.app" style="display: inline-block; background-color: #007bff; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 5px;">Login Now</a>
                </p>
                <p>If you have any questions or need assistance, feel free to contact us at info@Ktwis.com.</p>
                <p>Best Regards,<br>Ktwis</p>
            </td>
        </tr>
    </table>

</body>
</html>