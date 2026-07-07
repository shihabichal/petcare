@extends('layouts.app')
@section('title', 'Manajemen Transaksi')
@section('subtitle', 'Kelola transaksi, pembayaran, dan catatan customer')

@section('topbar-action')
    <button class="btn btn-primary" onclick="openModal('addTransactionModal')">
        <i class="bi bi-plus-lg"></i> Buat Transaksi
    </button>
@endsection

@section('content')
{{-- Search & Filter Bar --}}
<div class="card" style="margin-bottom:20px;padding:16px 20px;">
    <form method="GET" action="{{ route('admin.transactions') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
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
            <a href="{{ route('admin.transactions') }}" class="btn btn-outline">Reset</a>
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
                    <th>Catatan & WA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td>
                        <div style="font-family:monospace;font-weight:700;color:var(--primary);font-size:13px;">{{ $trx->transaction_code }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $trx->created_at->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600;">{{ $trx->customer->name }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $trx->pet->emoji ?? '🐾' }} {{ $trx->pet->name }} ({{ $trx->pet->type }})</div>
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
                            <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td style="font-weight:700;white-space:nowrap;">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
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
                            <select name="status" class="form-select" style="padding:5px 8px;font-size:12px;border-radius:8px;width:130px;" onchange="this.form.submit()">
                                @foreach(['pending','confirmed','ongoing','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ $trx->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:6px;min-width:200px;">
                            @if($trx->notes)
                                <div style="font-size:11px;background:var(--primary-light);color:var(--primary);padding:5px 8px;border-radius:7px;">
                                    👤 {{ Str::limit($trx->notes, 50) }}
                                </div>
                            @endif
                            @if($trx->notes_internal)
                                <div style="font-size:11px;background:var(--bg);color:var(--text-muted);padding:5px 8px;border-radius:7px;border:1px dashed var(--border);">
                                    🔒 {{ Str::limit($trx->notes_internal, 50) }}
                                </div>
                            @endif
                            <div style="display:flex;gap:6px;">
                                <button class="btn btn-outline btn-sm" onclick="openModal('noteModal{{ $trx->id }}')">
                                    <i class="bi bi-pencil"></i> Catatan
                                </button>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $trx->customer->phone_number) }}?text={{ urlencode('Halo ' . $trx->customer->name . '! Update untuk ' . ($trx->pet->name ?? '') . ' layanan ' . $trx->service->name . ':' . ($trx->notes ? ' ' . $trx->notes : '')) }}" target="_blank" class="btn btn-whatsapp btn-sm">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
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
                        <form action="{{ route('admin.transactions.notes', $trx->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body" style="display:grid;gap:14px;">
                                <div>
                                    <label class="form-label">Catatan untuk Customer</label>
                                    <textarea name="notes" class="form-control" rows="3" style="resize:none;" placeholder="Update kondisi hewan, info pengambilan, dll...">{{ $trx->notes }}</textarea>
                                </div>
                                <div>
                                    <label class="form-label">Catatan Internal <span style="color:var(--text-muted);font-weight:400;">(tidak tampil ke customer)</span></label>
                                    <textarea name="notes_internal" class="form-control" rows="3" style="resize:none;" placeholder="Catatan khusus tim...">{{ $trx->notes_internal }}</textarea>
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

    @if($transactions->hasPages())
    <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--border);">
        <div style="font-size:13px;color:var(--text-muted);">Menampilkan {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} dari {{ $transactions->total() }}</div>
        <div style="display:flex;gap:6px;">
            @if($transactions->onFirstPage())
                <span class="btn btn-outline btn-sm" style="opacity:0.4;cursor:not-allowed;"><i class="bi bi-chevron-left"></i></span>
            @else
                <a href="{{ $transactions->previousPageUrl() }}" class="btn btn-outline btn-sm"><i class="bi bi-chevron-left"></i></a>
            @endif
            @foreach($transactions->getUrlRange(max(1,$transactions->currentPage()-2),min($transactions->lastPage(),$transactions->currentPage()+2)) as $page => $url)
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
        <form action="{{ route('admin.transactions.store') }}" method="POST">
            @csrf
            <div class="modal-body" style="display:grid;gap:0;max-height:65vh;overflow-y:auto;padding-right:4px;">
                <p class="form-section-label">Customer & Hewan</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Customer</label>
                        <select name="customer_id" id="adminCustomerSelect" class="form-select" required onchange="adminLoadPets(this.value)">
                            <option value="">— Pilih Customer —</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->customer_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Hewan Peliharaan</label>
                        <select name="pet_id" id="adminPetSelect" class="form-select" required disabled>
                            <option value="">— Pilih customer dulu —</option>
                        </select>
                    </div>
                </div>

                <p class="form-section-label">Layanan</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Paket Layanan</label>
                        <select name="service_id" id="adminServiceSelect" class="form-select" required onchange="adminServiceChange(this)">
                            <option value="">— Pilih Layanan —</option>
                            @foreach($services as $s)
                                <option value="{{ $s->id }}" data-type="{{ $s->type }}" data-price="{{ $s->price }}">
                                    {{ $s->name }} — Rp {{ number_format($s->price, 0, ',', '.') }}{{ $s->type === 'penitipan' ? ' (per hari)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <p class="form-section-label">Jadwal</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="adminStartDate" class="form-control" required onchange="adminCalcDays()">
                    </div>
                    <div id="adminEndDateField" style="display:none;">
                        <label class="form-label">Tanggal Selesai <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                        <input type="date" name="end_date" id="adminEndDate" class="form-control" onchange="adminCalcDays()">
                    </div>
                    <div id="adminDaysField" style="display:none;">
                        <label class="form-label">Jumlah Hari</label>
                        <div style="position:relative;">
                            <input type="number" name="days" id="adminDaysInput" class="form-control" min="1" value="1" onchange="adminCalcTotal()">
                            <span style="position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;">hari</span>
                        </div>
                    </div>
                </div>

                <p class="form-section-label">Antar Jemput</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Butuh Antar Jemput?</label>
                        <div style="display:flex;gap:16px;margin-top:4px;">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="radio" name="pickup_required" value="0" checked onchange="adminTogglePickup(false)" style="accent-color:var(--primary);"> Tidak</label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="radio" name="pickup_required" value="1" onchange="adminTogglePickup(true)" style="accent-color:var(--primary);"> Ya</label>
                        </div>
                    </div>
                    <div id="adminPickupAddress" style="display:none;">
                        <label class="form-label">Alamat Penjemputan</label>
                        <textarea name="pickup_address" class="form-control" rows="2" style="resize:none;" placeholder="Alamat lengkap..."></textarea>
                    </div>
                    <div id="adminPickupTime" style="display:none;">
                        <label class="form-label">Jam Penjemputan</label>
                        <input type="datetime-local" name="pickup_time" class="form-control">
                    </div>
                </div>

                <p class="form-section-label">Total Harga</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Total Harga</label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-weight:600;">Rp</span>
                            <input type="number" name="total_price" id="adminTotalPrice" class="form-control" style="padding-left:42px;" min="0" placeholder="0">
                        </div>
                    </div>
                </div>

                <p class="form-section-label">Status Layanan</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Status Awal</label>
                        <select name="status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="ongoing">Ongoing</option>
                        </select>
                    </div>
                </div>

                <p class="form-section-label">Catatan</p>
                <div style="display:grid;gap:14px;">
                    <div>
                        <label class="form-label">Catatan untuk Pelanggan <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                        <textarea name="notes" class="form-control" rows="2" style="resize:none;" placeholder="Pesan atau instruksi dari customer..."></textarea>
                    </div>
                    <div>
                        <label class="form-label">Catatan Internal <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                        <textarea name="notes_internal" class="form-control" rows="2" style="resize:none;" placeholder="Catatan internal tim..."></textarea>
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
<style>.form-section-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-muted);margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);}</style>
@endpush

@push('scripts')
<script>
let adminSvcPrice = 0, adminSvcType = '';

function adminLoadPets(id) {
    const s = document.getElementById('adminPetSelect');
    s.disabled = true; s.innerHTML = '<option>Memuat...</option>';
    if (!id) { s.innerHTML = '<option>— Pilih customer dulu —</option>'; return; }
    fetch(`/api/customers/${id}/pets`).then(r=>r.json()).then(pets=>{
        s.disabled = false;
        s.innerHTML = pets.length ? '<option value="">— Pilih Hewan —</option>'+pets.map(p=>`<option value="${p.id}">${p.label}</option>`).join('') : '<option value="">Belum ada hewan terdaftar</option>';
    });
}
function adminServiceChange(sel) {
    const o = sel.options[sel.selectedIndex];
    adminSvcType = o?.getAttribute('data-type')||'';
    adminSvcPrice = parseFloat(o?.getAttribute('data-price')||0);
    const isP = adminSvcType==='penitipan';
    document.getElementById('adminEndDateField').style.display = isP?'block':'none';
    document.getElementById('adminDaysField').style.display = isP?'block':'none';
    adminCalcTotal();
}
function adminCalcDays() {
    const s=document.getElementById('adminStartDate').value, e=document.getElementById('adminEndDate').value;
    if(s&&e&&adminSvcType==='penitipan') document.getElementById('adminDaysInput').value=Math.max(1,Math.round((new Date(e)-new Date(s))/86400000));
    adminCalcTotal();
}
function adminCalcTotal() {
    const d=parseInt(document.getElementById('adminDaysInput')?.value||1);
    const t=adminSvcType==='penitipan'?adminSvcPrice*d:adminSvcPrice;
    if(t>0) document.getElementById('adminTotalPrice').value=t;
}
function adminTogglePickup(show) {
    document.getElementById('adminPickupAddress').style.display=show?'block':'none';
    document.getElementById('adminPickupTime').style.display=show?'block':'none';
}
</script>
@endpush
