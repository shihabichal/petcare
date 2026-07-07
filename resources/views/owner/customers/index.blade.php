@extends('layouts.app')
@section('title', 'Manajemen Customer')
@section('subtitle', 'Kelola data pelanggan dan hewan peliharaan mereka')

@section('topbar-action')
    <button class="btn btn-primary" onclick="openModal('addCustomerModal')">
        <i class="bi bi-plus-lg"></i> Tambah Customer
    </button>
@endsection

@section('content')
{{-- Search Bar --}}
<div class="card" style="margin-bottom:20px;padding:16px 20px;">
    <form method="GET" action="{{ route('owner.customers') }}" style="display:flex;gap:10px;align-items:center;">
        <div style="position:relative;flex:1;">
            <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);"></i>
            <input type="text" name="search" class="form-control" style="padding-left:36px;" placeholder="Cari nama, nomor WA, atau kode customer..." value="{{ $search ?? '' }}">
        </div>
        <button type="submit" class="btn btn-primary">Cari</button>
        @if($search)
            <a href="{{ route('owner.customers') }}" class="btn btn-outline">Reset</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Customer <span class="badge badge-primary" style="font-size:12px;">{{ $customers->total() }} total</span></span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Customer</th>
                    <th>No. WhatsApp</th>
                    <th>Alamat</th>
                    <th>Hewan Peliharaan</th>
                    <th>Transaksi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td><span class="badge badge-muted" style="font-family:monospace;">{{ $customer->customer_code }}</span></td>
                    <td>
                        <div style="font-weight:600;">{{ $customer->name }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">Bergabung {{ $customer->created_at->format('d M Y') }}</div>
                    </td>
                    <td>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone_number) }}" target="_blank" class="btn btn-whatsapp btn-sm">
                            <i class="bi bi-whatsapp"></i> {{ $customer->phone_number }}
                        </a>
                    </td>
                    <td style="font-size:13px;color:var(--text-muted);max-width:140px;">{{ $customer->address ?? '-' }}</td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:4px;">
                            @forelse($customer->pets as $pet)
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:16px;">{{ $pet->emoji }}</span>
                                    <div>
                                        <div style="font-size:13px;font-weight:600;">{{ $pet->name }}</div>
                                        <div style="font-size:11px;color:var(--text-muted);">{{ $pet->type }}{{ $pet->breed ? ' · ' . $pet->breed : '' }}</div>
                                    </div>
                                    <form action="{{ route('owner.pets.destroy', $pet->id) }}" method="POST" onsubmit="return confirm('Hapus data hewan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding:3px 7px;"><i class="bi bi-x"></i></button>
                                    </form>
                                </div>
                            @empty
                                <span style="font-size:12px;color:var(--text-muted);">Belum ada hewan</span>
                            @endforelse
                            <button class="btn btn-outline btn-sm" style="margin-top:4px;" onclick="openModal('addPetModal{{ $customer->id }}')">
                                <i class="bi bi-plus"></i> Tambah Hewan
                            </button>
                        </div>
                    </td>
                    <td><span class="badge badge-primary">{{ $customer->transactions_count }}</span></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="btn btn-outline btn-sm" onclick="openModal('editCustomer{{ $customer->id }}')"><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('owner.customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Hapus customer ini? Semua data pet & transaksi terkait akan ikut terhapus.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- Edit Customer Modal --}}
                <div class="modal-overlay" id="editCustomer{{ $customer->id }}">
                    <div class="modal-box">
                        <div class="modal-header">
                            <span class="modal-title">Edit Customer — {{ $customer->name }}</span>
                            <button class="modal-close" onclick="closeModal('editCustomer{{ $customer->id }}')"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <form action="{{ route('owner.customers.update', $customer->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body" style="display:grid;gap:14px;">
                                <div>
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                                </div>
                                <div>
                                    <label class="form-label">No. WhatsApp</label>
                                    <input type="text" name="phone_number" class="form-control" value="{{ $customer->phone_number }}" required>
                                </div>
                                <div>
                                    <label class="form-label">Alamat</label>
                                    <textarea name="address" class="form-control" rows="3" style="resize:none;">{{ $customer->address }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline" onclick="closeModal('editCustomer{{ $customer->id }}')">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Add Pet Modal --}}
                <div class="modal-overlay" id="addPetModal{{ $customer->id }}">
                    <div class="modal-box">
                        <div class="modal-header">
                            <span class="modal-title">Tambah Hewan — {{ $customer->name }}</span>
                            <button class="modal-close" onclick="closeModal('addPetModal{{ $customer->id }}')"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <form action="{{ route('owner.pets.store', $customer->id) }}" method="POST">
                            @csrf
                            <div class="modal-body" style="display:grid;gap:14px;">
                                <div>
                                    <label class="form-label">Nama Hewan</label>
                                    <input type="text" name="name" class="form-control" placeholder="cth: Mochi" required>
                                </div>
                                <div>
                                    <label class="form-label">Jenis Hewan</label>
                                    <select name="type" class="form-select" required>
                                        <option value="">Pilih...</option>
                                        <option value="Kucing">🐱 Kucing</option>
                                        <option value="Anjing">🐶 Anjing</option>
                                        <option value="Kelinci">🐰 Kelinci</option>
                                        <option value="Hamster">🐹 Hamster</option>
                                        <option value="Burung">🐦 Burung</option>
                                        <option value="Lainnya">🐾 Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Ras / Breed <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                                    <input type="text" name="breed" class="form-control" placeholder="cth: Persian, Shih Tzu">
                                </div>
                                <div>
                                    <label class="form-label">Umur (tahun) <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                                    <input type="number" name="age_years" class="form-control" min="0" placeholder="cth: 2">
                                </div>
                                <div>
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="gender" class="form-select">
                                        <option value="">Pilih...</option>
                                        <option value="Jantan">Jantan</option>
                                        <option value="Betina">Betina</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline" onclick="closeModal('addPetModal{{ $customer->id }}')">Batal</button>
                                <button type="submit" class="btn btn-primary">Tambah Hewan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-people"></i><p>Belum ada customer yang terdaftar</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($customers->hasPages())
    <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--border);">
        <div style="font-size:13px;color:var(--text-muted);">
            Menampilkan {{ $customers->firstItem() }}–{{ $customers->lastItem() }} dari {{ $customers->total() }} customer
        </div>
        <div style="display:flex;gap:6px;">
            @if($customers->onFirstPage())
                <span class="btn btn-outline btn-sm" style="opacity:0.4;cursor:not-allowed;"><i class="bi bi-chevron-left"></i></span>
            @else
                <a href="{{ $customers->previousPageUrl() }}" class="btn btn-outline btn-sm"><i class="bi bi-chevron-left"></i></a>
            @endif

            @foreach($customers->getUrlRange(max(1, $customers->currentPage()-2), min($customers->lastPage(), $customers->currentPage()+2)) as $page => $url)
                @if($page == $customers->currentPage())
                    <span class="btn btn-primary btn-sm">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="btn btn-outline btn-sm">{{ $page }}</a>
                @endif
            @endforeach

            @if($customers->hasMorePages())
                <a href="{{ $customers->nextPageUrl() }}" class="btn btn-outline btn-sm"><i class="bi bi-chevron-right"></i></a>
            @else
                <span class="btn btn-outline btn-sm" style="opacity:0.4;cursor:not-allowed;"><i class="bi bi-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Add Customer Modal --}}
<div class="modal-overlay" id="addCustomerModal">
    <div class="modal-box" style="max-width:520px;">
        <div class="modal-header">
            <span class="modal-title">Tambah Customer & Hewan Baru</span>
            <button class="modal-close" onclick="closeModal('addCustomerModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="{{ route('owner.customers.store') }}" method="POST">
            @csrf
            <div class="modal-body" style="display:grid;gap:0;">
                <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);">Data Customer</p>
                <div style="display:grid;gap:14px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama lengkap customer" required>
                    </div>
                    <div>
                        <label class="form-label">No. WhatsApp</label>
                        <input type="text" name="phone_number" class="form-control" placeholder="628123456789" required>
                    </div>
                    <div>
                        <label class="form-label">Alamat <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                        <textarea name="address" class="form-control" rows="2" style="resize:none;" placeholder="Alamat lengkap..."></textarea>
                    </div>
                </div>

                <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);">Data Hewan Peliharaan</p>
                <div style="display:grid;gap:14px;">
                    <div>
                        <label class="form-label">Nama Hewan</label>
                        <input type="text" name="pet_name" class="form-control" placeholder="cth: Mochi" required>
                    </div>
                    <div>
                        <label class="form-label">Jenis Hewan</label>
                        <select name="pet_type" class="form-select" required>
                            <option value="">Pilih jenis hewan...</option>
                            <option value="Kucing">🐱 Kucing</option>
                            <option value="Anjing">🐶 Anjing</option>
                            <option value="Kelinci">🐰 Kelinci</option>
                            <option value="Hamster">🐹 Hamster</option>
                            <option value="Burung">🐦 Burung</option>
                            <option value="Lainnya">🐾 Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Ras / Breed <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                        <input type="text" name="pet_breed" class="form-control" placeholder="cth: Persian, Golden Retriever">
                    </div>
                    <div>
                        <label class="form-label">Umur (tahun) <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                        <input type="number" name="pet_age" class="form-control" min="0" placeholder="cth: 2">
                    </div>
                    <div>
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="pet_gender" class="form-select">
                            <option value="">Pilih...</option>
                            <option value="Jantan">Jantan</option>
                            <option value="Betina">Betina</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addCustomerModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
