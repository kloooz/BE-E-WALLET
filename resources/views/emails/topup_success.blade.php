<!DOCTYPE html>
<html>
<head>
    <title>Top Up Successful</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #4CAF50;">Top Up Successful</h2>
        <p>Hello <strong>{{ $user->name }}</strong>,</p>
        <p>Your wallet top-up was successful. Here are the details:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <!-- reference id not needed for top up as per service logic -->
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Description:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $transaction->description }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Amount Added:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd; color: #5cb85c;">+ Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Date:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $transaction->created_at->format('j M Y, H:i') }}</td>
            </tr>
        </table>
        
        <p style="margin-top: 30px;">Your new balance is now available to use.</p>
        <p>Thank you for using our service!</p>
    </div>
</body>
</html>
