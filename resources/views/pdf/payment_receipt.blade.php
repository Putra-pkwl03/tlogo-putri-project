<!DOCTYPE html>
<html>
<head>
    <title>Bukti Pembayaran</title>
</head>
<body>

    <h1>Bukti Pembayaran</h1>
    <p>Nama Customer: {{ $order->customer_name }}</p>
    <p>Jumlah Pembayaran: {{ $transaction->amount }}</p>
    <p>Tanggal Pembayaran: {{ $transaction->paid_at }}</p>
    <p>Status: {{ $transaction->status }}</p>

</body>
</html>
