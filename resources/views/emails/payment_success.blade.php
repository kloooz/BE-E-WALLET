<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Successful</title>
</head>
<body style="font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); padding: 30px; text-align: center; color: #ffffff;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 700; letter-spacing: 1px;">DANAKU ✨</h1>
        </div>
        
        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #064e3b; margin-top: 0; font-size: 24px;">Payment Successful! ✅</h2>
            <p style="font-size: 16px; line-height: 1.6; color: #555;">Hi <strong>{{ $user->username }}</strong>,</p>
            <p style="font-size: 16px; line-height: 1.6; color: #555;">
                Your payment has been successfully processed. Here are the details of your transaction:
            </p>
            
            <div style="background: #f8f9fa; border-radius: 8px; padding: 25px; margin: 30px 0; border: 1px solid #e9ecef;">
                <h3 style="margin-top: 0; color: #4b5563; font-size: 18px; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 15px;">Transaction Details</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Description</td>
                        <td style="padding: 8px 0; color: #111827; text-align: right; font-weight: 600;">{{ $transaction->description }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Amount</td>
                        <td style="padding: 8px 0; color: #ef4444; text-align: right; font-weight: 600;">- Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Transaction ID</td>
                        <td style="padding: 8px 0; color: #111827; text-align: right; font-size: 14px;">{{ $transaction->reference_id }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Date</td>
                        <td style="padding: 8px 0; color: #111827; text-align: right; font-size: 14px;">{{ $transaction->created_at->format('j M Y, H:i') }}</td>
                    </tr>
                </table>
            </div>
            
            <p style="font-size: 16px; line-height: 1.6; color: #555; text-align: center; margin-top: 40px;">
                Thank you for using <strong>DANAKU</strong>!
            </p>
        </div>
        
        <!-- Footer -->
        <div style="background: #f3f4f6; text-align: center; padding: 20px; font-size: 14px; color: #6b7280; border-top: 1px solid #e5e7eb;">
            &copy; {{ date('Y') }} DANAKU. All rights reserved.
        </div>
    </div>
</body>
</html>
