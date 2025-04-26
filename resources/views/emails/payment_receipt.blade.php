<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email</title>
    <style>

        p {
            font-size: 13px;
        }

        p.success {
            font-size: 14px
        }

        .email-container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }
        .footer-container {
            width: 100%;
            max-width: 100%;
        }

        @media (min-width: 768px) {
            .email-container {
                max-width: 60%;
            }

            .footer-container {
                max-width: 60%;
            }
        }
    </style>
</head>
<body>
    <div style="font-family: sans-serif; font-size: 14px; color: #111827; padding: 20px;">
        <h3 style="font-size: 18px;">Hai, {{ $emailData['name'] }}</h3>

        <p class="success" style="margin-bottom: 40px; margin-top: 50px;">Pemesanan Jeep Kamu Berhasil</p>
    
        <hr >
        <p style="margin-top: 15px;">Pemesanan jeep kamu telah berhasil dibayar.</p>
        <p>Berikut detail pemesanan:</p>
    
        <table class="email-container" cellpadding="0" cellspacing="0" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 20px 0;">
            <tr>
                <td colspan="2" style="font-weight: bold; font-size: 14px; padding-bottom: 8px;">
                    Status Pembayaran
                    <span style="float: right; background-color: {{ strtolower($emailData['payment_status']) == 'unpaid' ? '#ffe4e6' : '#d1fae5' }}; color: {{ strtolower($emailData['payment_status']) == 'unpaid' ? '#f43f5e' : '#10b981' }}; padding: 4px 12px; border-radius: 6px; font-size: 13px; font-weight: bold;">
                        {{ strtolower($emailData['payment_status']) == 'unpaid' ? 'Belum Lunas' : 'Lunas' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="color: #6b7280; padding: 4px 0; font-size: 13px;">Kode Pemesanan</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">{{ $emailData['order_id'] }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280; padding: 4px 0; font-size: 13px;">Metode Pembayaran</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">{{ ucwords($emailData['payment_method']) }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280; padding: 4px 0; font-size: 13px;">Pembayaran</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">{{ strtoupper($emailData['payment_type']) }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280; padding: 4px 0; font-size: 13px;">Total Tagihan</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">Rp {{ number_format($emailData['amount'], 0, ',', '.') }}</td>
            </tr>
        </table>
    
        <table class="email-container" cellpadding="0" cellspacing="0" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 20px 0;">
            <tr>
                <td colspan="1" style="font-weight: bold; font-size: 14px; padding-bottom: 8px;">
                    Detail Pemesanan
                </td>
            </tr>
            <tr>
                <td style="color: #6b7280; padding: 4px 0; font-size: 13px;">Jenis Paket</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">{{ $emailData['package_type'] }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280; padding: 4px 0; font-size: 13px;">Tanggal Tour</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">{{ $emailData['tour_date'] }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280; padding: 4px 0; font-size: 13px;">Waktu</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">{{ $emailData['start_time'] }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280; padding: 4px 0; font-size: 13px;">Jumlah</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">{{ $emailData['qty'] }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280; padding: 4px 0; font-size: 13px;">Harga</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">Rp {{ number_format($emailData['package_price'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280; padding: 4px 0; font-size: 13px;">Total Pembayaran</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">Rp {{ number_format($emailData['total_price'], 0, ',', '.') }}</td>
            </tr>
            @if ($emailData['payment_status'] == 'unpaid')
            <tr>
                <td style="color: #6b7280; padding: 4px 0; font-size: 13px;">Dibayar (DP)</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">Rp {{ number_format($emailData['amount'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280; padding: 4px 0; font-size: 13px;">Sisa Pembayaran</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">Rp {{ number_format($emailData['remain_amount'], 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
    
        @if (strtolower($emailData['payment_status']) == 'unpaid')
            <p style="fonst-size: 13px;">Ini adalah pembayaran DP. Silakan selesaikan pelunasan Anda sebelum  <span style="color: #111827">{{ $emailData['expired_time'] }}</span></p>
            <p style="fonst-size: 13px;"><a href="{{ $emailData['remaining_url'] }}" style="color: #2563eb;">Klik di sini untuk melunasi</a></p>
        @else
            <p>Pembayaran Anda telah lunas. Terima kasih telah menyelesaikan pembayaran.</p>
        @endif
        
        <div class="footer-container">
            <p>E-mail ini dibuat otomatis, mohon untuk tidak membalas, Jika ada pertanyaan, silakan hubungi kami melalui kontak yang tersedia</p>
            <p style="margin-top: 15px;">Have fun and enjoy your journey with Tlogo Putri Jeep</p>
            
            <p>Salam,</p>
            <p><strong>Admin Jeep Tour</strong></p>
        </div>
    </div>

</body>
</html>
