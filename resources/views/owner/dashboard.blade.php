@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan performa bisnis PetCare Anda')

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-4" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#EEF0FF;color:#7C6FF7;"><i class="bi bi-people-fill"></i></div>
        <div>
            <div class="stat-value">{{ $totalCustomers }}</div>
            <div class="stat-label">Total Customer</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#E8F7F2;color:#4CAF8C;"><i class="bi bi-receipt"></i></div>
        <div>
            <div class="stat-value">{{ $totalTransactions }}</div>
            <div class="stat-label">Total Transaksi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFF4EE;color:#F7A87C;"><i class="bi bi-cash-stack"></i></div>
        <div>
            <div class="stat-value" style="font-size:16px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="stat-label">Total Pendapatan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFFAEE;color:#C8860A;"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="stat-value">{{ $pendingCount }}</div>
            <div class="stat-label">Transaksi Pending</div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-2" style="margin-bottom:24px;">
    {{-- Revenue Chart --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-bar-chart-fill" style="color:var(--primary);"></i> Pendapatan 6 Bulan Terakhir</span>
        </div>
        <div class="card-body">
            <canvas id="revenueChart" height="220"></canvas>
        </div>
    </div>

    {{-- Transaction Volume --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-graph-up" style="color:var(--success);"></i> Volume Transaksi 6 Bulan</span>
        </div>
        <div class="card-body">
            <canvas id="transactionChart" height="220"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-2" style="margin-bottom:24px;">
    {{-- Service Breakdown --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-pie-chart-fill" style="color:var(--accent);"></i> Distribusi Layanan</span>
        </div>
        <div class="card-body" style="display:flex;align-items:center;gap:24px;">
            <div style="width:180px;height:180px;flex-shrink:0;">
                <canvas id="serviceChart"></canvas>
            </div>
            <div id="serviceLegend" style="flex:1;display:grid;gap:8px;"></div>
        </div>
    </div>

    {{-- Payment Status --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-pie-chart-fill" style="color:var(--success);"></i> Status Pembayaran</span>
        </div>
        <div class="card-body" style="display:flex;align-items:center;gap:24px;">
            <div style="width:180px;height:180px;flex-shrink:0;">
                <canvas id="paymentChart"></canvas>
            </div>
            <div id="paymentLegend" style="flex:1;display:grid;gap:8px;"></div>
        </div>
    </div>
</div>

{{-- Recent Transactions --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Transaksi Terbaru</span>
        <a href="{{ route('owner.transactions') }}" class="btn btn-outline btn-sm">Lihat Semua <i class="bi bi-arrow-right"></i></a>
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
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $trx)
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
                        <div style="font-size:11px;color:var(--text-muted);">{{ ucfirst(str_replace('_',' ',$trx->service->type)) }}</div>
                    </td>
                    <td style="font-weight:600;">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                    <td>
                        @if($trx->payment_status==='paid') <span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Lunas</span>
                        @else <span class="badge badge-warning"><i class="bi bi-clock-fill"></i> Belum Bayar</span>
                        @endif
                    </td>
                    <td>
                        @php $sm=['pending'=>'badge-muted','confirmed'=>'badge-primary','ongoing'=>'badge-warning','completed'=>'badge-success','cancelled'=>'badge-danger']; @endphp
                        <span class="badge {{ $sm[$trx->status]??'badge-muted' }}">{{ ucfirst($trx->status) }}</span>
                    </td>
                    <td style="color:var(--text-muted);font-size:13px;">{{ $trx->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-receipt"></i><p>Belum ada transaksi</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const months  = @json($chartData['months']);
const revenue = @json($chartData['revenue']);
const volume  = @json($chartData['volume']);
const svcLabels= @json($chartData['serviceLabels']);
const svcData  = @json($chartData['serviceData']);
const payLabels= ['Belum Bayar','Lunas'];
const payData  = @json($chartData['paymentData']);

Chart.defaults.font.family = "'Outfit', sans-serif";

// Revenue Chart
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: revenue,
            backgroundColor: 'rgba(124,111,247,0.15)',
            borderColor: '#7C6FF7',
            borderWidth: 2,
            borderRadius: 8,
            hoverBackgroundColor: 'rgba(124,111,247,0.3)',
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: '#EAECF0' }, ticks: { callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k', color: '#8A95A5' } },
            x: { grid: { display: false }, ticks: { color: '#8A95A5' } }
        }
    }
});

// Transaction Volume Chart
new Chart(document.getElementById('transactionChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Transaksi',
            data: volume,
            borderColor: '#4CAF8C',
            backgroundColor: 'rgba(76,175,140,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: '#4CAF8C',
            pointRadius: 5,
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: '#EAECF0' }, ticks: { color: '#8A95A5', stepSize: 1 } },
            x: { grid: { display: false }, ticks: { color: '#8A95A5' } }
        }
    }
});

// Service Doughnut
const serviceColors = ['#7C6FF7','#F7A87C','#4CAF8C','#F7C76F','#F76F6F'];
new Chart(document.getElementById('serviceChart'), {
    type: 'doughnut',
    data: {
        labels: svcLabels,
        datasets: [{ data: svcData, backgroundColor: serviceColors, borderWidth: 0, hoverOffset: 6 }]
    },
    options: {
        cutout: '70%',
        plugins: { legend: { display: false } }
    }
});
const svcLegend = document.getElementById('serviceLegend');
svcLabels.forEach((l, i) => {
    svcLegend.innerHTML += `<div style="display:flex;align-items:center;gap:8px;">
        <div style="width:10px;height:10px;border-radius:3px;background:${serviceColors[i]};flex-shrink:0;"></div>
        <span style="font-size:13px;color:var(--text-dark);">${l}</span>
        <span style="font-size:13px;font-weight:700;margin-left:auto;color:var(--primary);">${svcData[i]}</span>
    </div>`;
});

// Payment Doughnut
const payColors = ['#F7C76F','#4CAF8C'];
new Chart(document.getElementById('paymentChart'), {
    type: 'doughnut',
    data: {
        labels: payLabels,
        datasets: [{ data: payData, backgroundColor: payColors, borderWidth: 0, hoverOffset: 6 }]
    },
    options: {
        cutout: '70%',
        plugins: { legend: { display: false } }
    }
});
const payLegend = document.getElementById('paymentLegend');
payLabels.forEach((l, i) => {
    payLegend.innerHTML += `<div style="display:flex;align-items:center;gap:8px;">
        <div style="width:10px;height:10px;border-radius:3px;background:${payColors[i]};flex-shrink:0;"></div>
        <span style="font-size:13px;color:var(--text-dark);">${l}</span>
        <span style="font-size:13px;font-weight:700;margin-left:auto;">${payData[i]}</span>
    </div>`;
});
</script>
@endpush
