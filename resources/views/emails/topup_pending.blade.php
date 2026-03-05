<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Complete Your Top Up</title>
</head>
<body style="font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); padding: 30px; text-align: center; color: #ffffff;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 700; letter-spacing: 1px;">DANAKU ✨</h1>
        </div>
        
        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #1e3a8a; margin-top: 0; font-size: 24px;">Action Required: Complete Your Payment ⏳</h2>
            <p style="font-size: 16px; line-height: 1.6; color: #555;">Hi <strong>{{ $user->username }}</strong>,</p>
            <p style="font-size: 16px; line-height: 1.6; color: #555;">
                We noticed that you requested a wallet top-up but the payment has not been completed yet. Please complete your payment to continue.
            </p>
            
            <div style="background: #eff6ff; border-radius: 8px; padding: 25px; margin: 30px 0; border: 1px solid #bfdbfe;">
                <h3 style="margin-top: 0; color: #1e3a8a; font-size: 18px; border-bottom: 1px solid #93c5fd; padding-bottom: 10px; margin-bottom: 15px;">Transaction Details</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #1e40af; font-weight: 500;">Amount</td>
                        <td style="padding: 8px 0; color: #059669; text-align: right; font-weight: 600;">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #1e40af; font-weight: 500;">Transaction ID</td>
                        <td style="padding: 8px 0; color: #1e3a8a; text-align: right; font-size: 14px;">{{ $transaction->reference_id }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #1e40af; font-weight: 500;">Payment Deadline</td>
                        <td style="padding: 8px 0; color: #b91c1c; text-align: right; font-size: 14px; font-weight: 600;">{{ $expiryTime }}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Payment Action -->
            <div style="text-align: center; margin: 40px 0;">
                <a href="https://app.sandbox.midtrans.com/snap/v2/vtweb/{{ $snapToken }}" style="display: inline-block; background: #3b82f6; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; padding: 14px 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);">
                    Pay Now
                </a>
            </div>
            <p style="font-size: 14px; line-height: 1.6; color: #888; text-align: center;">
                If the button above does not work, simply copy this link and paste it in your browser:<br>
                <a href="https://app.sandbox.midtrans.com/snap/v2/vtweb/{{ $snapToken }}" style="color: #2563eb; word-break: break-all;">https://app.sandbox.midtrans.com/snap/v2/vtweb/{{ $snapToken }}</a>
            </p>

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
