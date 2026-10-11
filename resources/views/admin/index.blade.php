<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin — MCC</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 42rem; margin: 4rem auto; padding: 0 1rem; color: #1e293b; }
        h1 { font-size: 1.5rem; }
        .box { border: 1px solid #cbd5e1; border-radius: .5rem; padding: 1rem 1.25rem; background: #f8fafc; }
        a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <h1>MikroTik Control Center — Area Admin</h1>

    <div class="box">
        <p>Placeholder fondasi <strong>M1</strong>. Halaman manajemen perangkat
        (tambah/ubah metadata/enable-disable monitoring/hapus — FR-002) akan dibangun
        pada milestone berikutnya, setelah keputusan terbuka M0 selesai.</p>

        <p>Jumlah pengguna terdaftar: <strong>{{ \App\Models\User::count() }}</strong> —
        jumlah perangkat: <strong>{{ \App\Models\Device::count() }}</strong></p>
    </div>

    <p><a href="{{ route('dashboard') }}">&larr; Kembali ke dashboard</a></p>
</body>
</html>
