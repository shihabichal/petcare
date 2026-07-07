<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #2D3250; }

    .header {
        background: linear-gradient(135deg, #7C6FF7, #5A52D5);
        color: white;
        padding: 16px 24px;
        margin-bottom: 18px;
    }
    .header h1 { font-size: 17px; font-weight: 700; margin-bottom: 3px; }
    .header p  { font-size: 10px; opacity: 0.85; }
    .header-meta { margin-top: 10px; font-size: 10px; opacity: 0.9; }

    .content { padding: 0 20px 20px; }

    table { width: 100%; border-collapse: collapse; }
    thead th {
        background: #2D3250;
        color: white;
        padding: 9px 10px;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        text-align: left;
    }
    tbody td {
        padding: 8px 10px;
        border-bottom: 1px solid #EAECF0;
        vertical-align: top;
        font-size: 9px;
    }
    tbody tr:nth-child(even) td { background: #FAFBFF; }
    tbody tr:last-child td { border-bottom: none; }

    .code { font-family: monospace; font-weight: 700; color: #7C6FF7; font-size: 9px; }
    .badge {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 20px;
        font-size: 8px;
        font-weight: 700;
    }
    .badge-success { background: #E8F7F2; color: #4CAF8C; }
    .badge-warning { background: #FFFAEE; color: #C8860A; }
    .badge-primary { background: #EEF0FF; color: #7C6FF7; }
    .badge-danger  { background: #FFF0F0; color: #F76F6F; }
    .badge-muted   { background: #F5F6FA; color: #8A95A5; }

    .summary-box {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
        padding: 14px 16px;
        background: #F5F6FA;
        border-radius: 10px;
        border: 1px solid #EAECF0;
    }
    .summary-item { text-align: center; }
    .summary-value { font-size: 16px; font-weight: 700; color: #2D3250; }
    .summary-label { font-size: 9px; color: #8A95A5; margin-top: 2px; }

    .footer {
        margin-top: 18px;
        padding-top: 10px;
        border-top: 1px solid #EAECF0;
        text-align: center;
        font-size: 8px;
        color: #8A95A5;
    }
</style>
</head>
<body>

<div class="header">
    <h1>🧾 Laporan Data Transaksi — PetCare</h1>
    <p>Rekap seluruh transaksi layanan PetCare</p>
    <div class="header-meta">
        📅 Dicetak: {{ now()->format('d F Y, H:i') }} WIB &bull;
        Total: {{ $transactions->count() }} transaksi &bull;
        Pendapatan (Lunas): Rp {{ number_format($transactions->where('payment_status','paid')->sum('total_price'), 0, ',', '.') }}
    </div>
</div>

<div class="content">
    <table>
        <thead>
            <tr>
                <th style="width:14%;">Kode Transaksi</th>
                <th style="width:14%;">Customer</th>
                <th style="width:10%;">Hewan</th>
                <th style="width:13%;">Layanan</th>
                <th style="width:9%;">Mulai</th>
                <th style="width:9%;">Selesai</th>
                <th style="width:4%;">Hari</th>
                <th style="width:10%;">Total Harga</th>
                <th style="width:8%;">Pembayaran</th>
                <th style="width:9%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trx)
            <tr>
                <td>
                    <div class="code">{{ $trx->transaction_code }}</div>
                    <div style="color:#8A95A5;font-size:8px;margin-top:2px;">{{ $trx->created_at->format('d M Y') }}</div>
                </td>
                <td>
                    <div style="font-weight:600;">{{ $trx->customer->name }}</div>
                    <div style="color:#8A95A5;font-size:8px;">{{ $trx->customer->phone_number }}</div>
                </td>
                <td>
                    <div style="font-weight:500;">{{ $trx->pet->name ?? '-' }}</div>
                    <div style="color:#8A95A5;font-size:8px;">{{ $trx->pet->type ?? '-' }}</div>
                </td>
                <td>
                    <div style="font-weight:500;">{{ $trx->service->name }}</div>
                    <div style="color:#8A95A5;font-size:8px;">{{ ucfirst(str_replace('_',' ',$trx->service->type)) }}</div>
                    @if($trx->pickup_required)<div style="margin-top:2px;"><span class="badge badge-warning">Antar Jemput</span></div>@endif
                </td>
                <td>{{ $trx->start_date ? $trx->start_date->format('d/m/Y') : '-' }}</td>
                <td>{{ $trx->end_date ? $trx->end_date->format('d/m/Y') : '-' }}</td>
                <td style="text-align:center;">{{ $trx->service->type === 'penitipan' ? $trx->days : '-' }}</td>
                <td style="font-weight:700;">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                <td>
                    @if($trx->payment_status==='paid') <span class="badge badge-success">Lunas</span>
                    @elseif($trx->payment_status==='refunded') <span class="badge badge-danger">Refunded</span>
                    @else <span class="badge badge-warning">Belum Bayar</span>
                    @endif
                </td>
                <td>
                    @php $sc=['pending'=>'badge-muted','confirmed'=>'badge-primary','ongoing'=>'badge-warning','completed'=>'badge-success','cancelled'=>'badge-danger']; @endphp
                    <span class="badge {{ $sc[$trx->status]??'badge-muted' }}">{{ ucfirst($trx->status) }}</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;padding:20px;color:#8A95A5;">Tidak ada data transaksi</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dibuat otomatis oleh Sistem PetCare Admin &bull; {{ now()->format('d/m/Y H:i') }}
    </div>
</div>
</body>
</html>
