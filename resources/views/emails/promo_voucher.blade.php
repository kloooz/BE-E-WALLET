<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Promo Voucher Purchased</title>
</head>
<body style="font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%); padding: 30px; text-align: center; color: #ffffff;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 700; letter-spacing: 1px;">DANAKU ✨</h1>
        </div>
        
        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #1e1b4b; margin-top: 0; font-size: 24px;">Your Promo Voucher is Here! 🎉</h2>
            <p style="font-size: 16px; line-height: 1.6; color: #555;">Hi <strong>{{ $user->username }}</strong>,</p>
            <p style="font-size: 16px; line-height: 1.6; color: #555;">
                Thank you for your purchase! Here is your exclusive promo voucher code. You can use it right away.
            </p>
            
            <!-- Code Highlight -->
            <div style="text-align: center; margin: 40px 0;">
                <span style="display: inline-block; background: #f0fdf4; color: #16a34a; font-size: 28px; font-weight: bold; padding: 15px 30px; border-radius: 8px; border: 2px dashed #4ade80; letter-spacing: 2px;">
                    {{ $voucherCode }}
                </span>
            </div>

            <div style="background: #f8f9fa; border-radius: 8px; padding: 25px; margin: 30px 0; border: 1px solid #e9ecef;">
                <h3 style="margin-top: 0; color: #4b5563; font-size: 18px; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 15px;">Transaction Details</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Promo Name</td>
                        <td style="padding: 8px 0; color: #111827; text-align: right; font-weight: 600;">{{ $transaction->description }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Amount Paid</td>
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
                Keep enjoying the best deals with <strong>DANAKU</strong>!
            </p>
        </div>
        
        <!-- Footer -->
        <div style="background: #f3f4f6; text-align: center; padding: 20px; font-size: 14px; color: #6b7280; border-top: 1px solid #e5e7eb;">
            &copy; {{ date('Y') }} DANAKU. All rights reserved.
        </div>
    </div>
</body>
</html>
