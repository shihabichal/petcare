@extends('layouts.app')
@section('title', 'Manajemen Transaksi')
@section('subtitle', 'Kelola seluruh transaksi layanan PetCare')

@section('topbar-action')
    <button class="btn btn-primary" onclick="openModal('addTransactionModal')">
        <i class="bi bi-plus-lg"></i> Buat Transaksi
    </button>
@endsection

@section('content')
{{-- Search & Filter Bar --}}
<div class="card" style="margin-bottom:20px;padding:16px 20px;">
    <form method="GET" action="{{ route('owner.transactions') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <div style="position:relative;flex:1;min-width:200px;">
            <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);"></i>
            <input type="text" name="search" class="form-control" style="padding-left:36px;" placeholder="Cari kode transaksi atau nama customer..." value="{{ $search ?? '' }}">
        </div>
        <select name="status" class="form-select" style="width:auto;">
            <option value="">Semua Status</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="ongoing" {{ $status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if($search || $status)
            <a href="{{ route('owner.transactions') }}" class="btn btn-outline">Reset</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Transaksi <span class="badge badge-primary" style="font-size:12px;">{{ $transactions->total() }} total</span></span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Kode Transaksi</th>
                    <th>Customer / Hewan</th>
                    <th>Layanan</th>
                    <th>Jadwal</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                @php
                    $statusMap = [
                        'pending'   => ['badge-muted','Pending'],
                        'confirmed' => ['badge-primary','Confirmed'],
                        'ongoing'   => ['badge-warning','Ongoing'],
                        'completed' => ['badge-success','Completed'],
                        'cancelled' => ['badge-danger','Cancelled'],
                    ];
                    [$badge, $label] = $statusMap[$trx->status] ?? ['badge-muted', $trx->status];
                @endphp
                <tr>
                    <td>
                        <div style="font-family:monospace;font-weight:700;color:var(--primary);font-size:13px;">{{ $trx->transaction_code }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $trx->created_at->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600;">{{ $trx->customer->name }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">
                            {{ $trx->pet->emoji ?? '🐾' }} {{ $trx->pet->name }} ({{ $trx->pet->type }})
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:500;">{{ $trx->service->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ ucfirst(str_replace('_',' ',$trx->service->type)) }}</div>
                        @if($trx->pickup_required)
                            <span class="badge badge-warning" style="margin-top:3px;"><i class="bi bi-truck"></i> Antar Jemput</span>
                        @endif
                    </td>
                    <td>
                        @if($trx->start_date)
                            <div style="font-size:13px;">{{ $trx->start_date->format('d M Y') }}</div>
                            @if($trx->end_date)
                                <div style="font-size:12px;color:var(--text-muted);">s/d {{ $trx->end_date->format('d M Y') }}</div>
                                <span class="badge badge-primary">{{ $trx->days }} hari</span>
                            @endif
                        @else
                            <span style="color:var(--text-muted);font-size:13px;">—</span>
                        @endif
                    </td>
                    <td style="font-weight:700;white-space:nowrap;">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                    <td>
                        @if($trx->payment_status === 'paid')
                            <span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Lunas</span>
                        @elseif($trx->payment_status === 'refunded')
                            <span class="badge badge-danger">Refunded</span>
                        @else
                            <form action="{{ route('owner.transactions.verify', $trx->id) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-warning btn-sm">Verifikasi</button>
                            </form>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('owner.transactions.status', $trx->id) }}" method="POST">
                            @csrf @method('PUT')
                            <select name="status" class="form-select" style="padding:5px 8px;font-size:12px;border-radius:8px;width:130px;" onchange="this.form.submit()">
                                @foreach(['pending','confirmed','ongoing','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ $trx->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            <button class="btn btn-outline btn-sm" onclick="openModal('noteModal{{ $trx->id }}')" title="Catatan">
                                <i class="bi bi-chat-left-text"></i>
                            </button>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $trx->customer->phone_number) }}?text={{ urlencode('Halo ' . $trx->customer->name . '! Update untuk ' . $trx->pet->name . ' layanan ' . $trx->service->name . ':') }}" target="_blank" class="btn btn-whatsapp btn-sm" title="Kirim WA">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            <form action="{{ route('owner.transactions.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- Notes Modal --}}
                <div class="modal-overlay" id="noteModal{{ $trx->id }}">
                    <div class="modal-box">
                        <div class="modal-header">
                            <span class="modal-title">Catatan — {{ $trx->transaction_code }}</span>
                            <button class="modal-close" onclick="closeModal('noteModal{{ $trx->id }}')"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <form action="{{ route('owner.transactions.notes', $trx->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body" style="display:grid;gap:14px;">
                                <div>
                                    <label class="form-label">Catatan untuk Customer</label>
                                    <textarea name="notes" class="form-control" rows="3" style="resize:none;" placeholder="Catatan yang bisa disampaikan ke customer via WA...">{{ $trx->notes }}</textarea>
                                </div>
                                <div>
                                    <label class="form-label">Catatan Internal <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                                    <textarea name="notes_internal" class="form-control" rows="3" style="resize:none;" placeholder="Catatan khusus internal tim...">{{ $trx->notes_internal }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline" onclick="closeModal('noteModal{{ $trx->id }}')">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Catatan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr><td colspan="8"><div class="empty-state"><i class="bi bi-receipt"></i><p>Belum ada transaksi</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($transactions->hasPages())
    <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--border);">
        <div style="font-size:13px;color:var(--text-muted);">
            Menampilkan {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi
        </div>
        <div style="display:flex;gap:6px;">
            @if($transactions->onFirstPage())
                <span class="btn btn-outline btn-sm" style="opacity:0.4;cursor:not-allowed;"><i class="bi bi-chevron-left"></i></span>
            @else
                <a href="{{ $transactions->previousPageUrl() }}" class="btn btn-outline btn-sm"><i class="bi bi-chevron-left"></i></a>
            @endif
            @foreach($transactions->getUrlRange(max(1,$transactions->currentPage()-2), min($transactions->lastPage(),$transactions->currentPage()+2)) as $page => $url)
                @if($page == $transactions->currentPage())
                    <span class="btn btn-primary btn-sm">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="btn btn-outline btn-sm">{{ $page }}</a>
                @endif
            @endforeach
            @if($transactions->hasMorePages())
                <a href="{{ $transactions->nextPageUrl() }}" class="btn btn-outline btn-sm"><i class="bi bi-chevron-right"></i></a>
            @else
                <span class="btn btn-outline btn-sm" style="opacity:0.4;cursor:not-allowed;"><i class="bi bi-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Add Transaction Modal --}}
<div class="modal-overlay" id="addTransactionModal">
    <div class="modal-box" style="max-width:580px;">
        <div class="modal-header">
            <span class="modal-title">Buat Transaksi Baru</span>
            <button class="modal-close" onclick="closeModal('addTransactionModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="{{ route('owner.transactions.store') }}" method="POST" id="trxForm">
            @csrf
            <div class="modal-body" style="display:grid;gap:0;max-height:65vh;overflow-y:auto;padding-right:4px;">

                {{-- Customer & Pet --}}
                <p class="form-section-label">Customer & Hewan</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Customer</label>
                        <select name="customer_id" id="customerSelect" class="form-select" required onchange="loadPets(this.value)">
                            <option value="">— Pilih Customer —</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->customer_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Hewan Peliharaan <span style="color:var(--text-muted);font-weight:400;">(otomatis sesuai customer)</span></label>
                        <select name="pet_id" id="petSelect" class="form-select" required disabled>
                            <option value="">— Pilih customer dulu —</option>
                        </select>
                    </div>
                </div>

                {{-- Layanan --}}
                <p class="form-section-label">Layanan</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Paket Layanan</label>
                        <select name="service_id" id="serviceSelect" class="form-select" required onchange="handleServiceChange(this)">
                            <option value="">— Pilih Layanan —</option>
                            @foreach($services as $s)
                                <option value="{{ $s->id }}" data-type="{{ $s->type }}" data-price="{{ $s->price }}">
                                    {{ $s->name }} — Rp {{ number_format($s->price, 0, ',', '.') }}{{ $s->type === 'penitipan' ? ' (per hari)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Jadwal --}}
                <p class="form-section-label">Jadwal</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="startDate" class="form-control" required onchange="calcDays()">
                    </div>
                    <div id="endDateField" style="display:none;">
                        <label class="form-label">Tanggal Selesai <span style="color:var(--text-muted);font-weight:400;">(opsional, untuk penitipan)</span></label>
                        <input type="date" name="end_date" id="endDate" class="form-control" onchange="calcDays()">
                    </div>
                    <div id="daysField" style="display:none;">
                        <label class="form-label">Jumlah Hari <span style="color:var(--text-muted);font-weight:400;">(otomatis, bisa diedit)</span></label>
                        <div style="position:relative;">
                            <input type="number" name="days" id="daysInput" class="form-control" min="1" value="1" onchange="calcTotal()">
                            <span style="position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;">hari</span>
                        </div>
                    </div>
                </div>

                {{-- Antar Jemput --}}
                <p class="form-section-label">Antar Jemput</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Butuh Antar Jemput?</label>
                        <div style="display:flex;gap:16px;margin-top:4px;">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                <input type="radio" name="pickup_required" value="0" checked onchange="togglePickup(false)" style="accent-color:var(--primary);"> Tidak
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                <input type="radio" name="pickup_required" value="1" onchange="togglePickup(true)" style="accent-color:var(--primary);"> Ya
                            </label>
                        </div>
                    </div>
                    <div id="pickupFields" style="display:none;display:grid;gap:14px;">
                        <div id="pickupAddressField" style="display:none;">
                            <label class="form-label">Alamat Penjemputan</label>
                            <textarea name="pickup_address" class="form-control" rows="2" style="resize:none;" placeholder="Alamat lengkap penjemputan..."></textarea>
                        </div>
                        <div id="pickupTimeField" style="display:none;">
                            <label class="form-label">Jam Penjemputan</label>
                            <input type="datetime-local" name="pickup_time" class="form-control">
                        </div>
                    </div>
                </div>

                {{-- Total --}}
                <p class="form-section-label">Total Harga</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Total Harga <span style="color:var(--text-muted);font-weight:400;">(otomatis, bisa diedit)</span></label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-weight:600;">Rp</span>
                            <input type="number" name="total_price" id="totalPrice" class="form-control" style="padding-left:42px;" min="0" placeholder="0">
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <p class="form-section-label">Status Layanan</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Status Awal</label>
                        <select name="status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                {{-- Catatan --}}
                <p class="form-section-label">Catatan</p>
                <div style="display:grid;gap:14px;">
                    <div>
                        <label class="form-label">Catatan untuk Pelanggan <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                        <textarea name="notes" class="form-control" rows="2" style="resize:none;" placeholder="Pesan atau instruksi khusus dari customer..."></textarea>
                    </div>
                    <div>
                        <label class="form-label">Catatan Internal <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                        <textarea name="notes_internal" class="form-control" rows="2" style="resize:none;" placeholder="Catatan internal tim PetCare..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addTransactionModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Buat Transaksi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-section-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-muted);
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--border);
}
</style>
@endpush

@push('scripts')
<script>
let currentServicePrice = 0;
let currentServiceType  = '';

function loadPets(customerId) {
    const select = document.getElementById('petSelect');
    select.disabled = true;
    select.innerHTML = '<option>Memuat...</option>';

    if (!customerId) {
        select.innerHTML = '<option>— Pilih customer dulu —</option>';
        return;
    }

    fetch(`/api/customers/${customerId}/pets`)
        .then(r => r.json())
        .then(pets => {
            select.disabled = false;
            if (pets.length === 0) {
                select.innerHTML = '<option value="">Customer belum punya hewan terdaftar</option>';
            } else {
                select.innerHTML = '<option value="">— Pilih Hewan —</option>' +
                    pets.map(p => `<option value="${p.id}">${p.label}</option>`).join('');
            }
        });
}

function handleServiceChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    currentServiceType  = opt?.getAttribute('data-type') || '';
    currentServicePrice = parseFloat(opt?.getAttribute('data-price') || 0);

    const isBoarding = currentServiceType === 'penitipan';
    document.getElementById('endDateField').style.display = isBoarding ? 'block' : 'none';
    document.getElementById('daysField').style.display    = isBoarding ? 'block' : 'none';
    calcTotal();
}

function calcDays() {
    const start = document.getElementById('startDate').value;
    const end   = document.getElementById('endDate').value;
    if (start && end && currentServiceType === 'penitipan') {
        const diff = Math.max(1, Math.round((new Date(end) - new Date(start)) / 86400000));
        document.getElementById('daysInput').value = diff;
    }
    calcTotal();
}

function calcTotal() {
    const days  = parseInt(document.getElementById('daysInput')?.value || 1);
    const total = currentServiceType === 'penitipan' ? currentServicePrice * days : currentServicePrice;
    if (total > 0) document.getElementById('totalPrice').value = total;
}

function togglePickup(show) {
    document.getElementById('pickupAddressField').style.display = show ? 'block' : 'none';
    document.getElementById('pickupTimeField').style.display    = show ? 'block' : 'none';
}
</script>
@endpush
