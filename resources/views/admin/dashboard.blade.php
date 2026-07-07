@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('subtitle', 'Manajemen operasional harian PetCare')

@section('content')
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFFAEE;color:#C8860A;"><i class="bi bi-hourglass-split"></i></div>
        <div><div class="stat-value">{{ $stats['pending'] }}</div><div class="stat-label">Menunggu Diproses</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#EEF0FF;color:#7C6FF7;"><i class="bi bi-arrow-clockwise"></i></div>
        <div><div class="stat-value">{{ $stats['in_progress'] }}</div><div class="stat-label">Sedang Berjalan</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFF0F0;color:#F76F6F;"><i class="bi bi-cash"></i></div>
        <div><div class="stat-value">{{ $stats['unpaid'] }}</div><div class="stat-label">Belum Dibayar</div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Transaksi Aktif</span>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.customers') }}" class="btn btn-outline btn-sm"><i class="bi bi-people"></i> Customer</a>
            <a href="{{ route('admin.transactions') }}" class="btn btn-primary btn-sm"><i class="bi bi-receipt"></i> Semua Transaksi</a>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Customer / Hewan</th>
                    <th>Layanan</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Catatan & WA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td><span style="font-family:monospace;font-weight:700;color:var(--primary);font-size:12px;">{{ $trx->transaction_code }}</span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="pet-avatar">{{ $trx->pet->emoji ?? '🐾' }}</div>
                            <div>
                                <div style="font-weight:600;">{{ $trx->customer->name }}</div>
                                <div style="font-size:12px;color:var(--text-muted);">{{ $trx->pet->name ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:500;">{{ $trx->service->name }}</div>
                        @if($trx->pickup_required)
                            <span class="badge badge-warning"><i class="bi bi-truck"></i> Antar Jemput</span>
                        @endif
                    </td>
                    <td style="font-weight:700;">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                    <td>
                        @if($trx->payment_status === 'paid')
                            <span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Lunas</span>
                        @else
                            <form action="{{ route('admin.transactions.verify', $trx->id) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-check2"></i> Verifikasi</button>
                            </form>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.transactions.status', $trx->id) }}" method="POST">
                            @csrf @method('PUT')
                            <select name="status" class="form-select" style="padding:5px 8px;font-size:12px;border-radius:8px;width:120px;" onchange="this.form.submit()">
                                @foreach(['pending','confirmed','ongoing','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ $trx->status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:5px;min-width:180px;">
                            @if($trx->notes)<div style="font-size:11px;background:var(--primary-light);color:var(--primary);padding:4px 8px;border-radius:6px;">👤 {{ Str::limit($trx->notes,40) }}</div>@endif
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.transactions') }}" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$trx->customer->phone_number) }}?text={{ urlencode('Halo '.$trx->customer->name.'! Update peliharaan Anda:'.($trx->notes?' '.$trx->notes:'')) }}" target="_blank" class="btn btn-whatsapp btn-sm"><i class="bi bi-whatsapp"></i></a>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-receipt"></i><p>Belum ada transaksi</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
