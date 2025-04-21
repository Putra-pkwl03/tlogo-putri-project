<h2>Terima kasih {{ $emailData['name'] }}</h2>
<p>Pembayaran Anda berhasil.</p>

<p><strong>Order ID:</strong> {{ $emailData['order_id'] }}</p>
<p><strong>Jumlah Dibayar:</strong> Rp{{ number_format($emailData['amount'], 0, ',', '.') }}</p>
<p><strong>Tanggal Tour:</strong> {{ $emailData['tour_date'] }}</p>

@if ($emailData['is_dp'])
    <p>Ini adalah pembayaran DP. Silakan selesaikan pelunasan Anda sebelum tanggal tour.</p>
    <p><a href="{{ $emailData['remaining_url'] }}">Klik di sini untuk melunasi</a></p>
@endif

<p>Salam,</p>
<p>Admin Jeep Tour</p>
