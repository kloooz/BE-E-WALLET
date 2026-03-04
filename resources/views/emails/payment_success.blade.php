<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #4CAF50;">Payment Successful</h2>
        <p>Hello <strong>{{ $user->name }}</strong>,</p>
        <p>Your payment has been successfully processed. Here are the details of your transaction:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Transaction ID:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $transaction->reference_id }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Description:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $transaction->description }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Amount:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd; color: #d9534f;">- Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Date:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $transaction->created_at->format('j M Y, H:i') }}</td>
            </tr>
        </table>
        
        <p style="margin-top: 30px;">Thank you for using our service!</p>
    </div>
</body>
</html>
