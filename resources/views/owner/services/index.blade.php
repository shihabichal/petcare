@extends('layouts.app')
@section('title', 'Layanan & Harga')
@section('subtitle', 'Kelola paket layanan dan harga secara dinamis')

@section('topbar-action')
    <button class="btn btn-primary" onclick="openModal('addServiceModal')">
        <i class="bi bi-plus-lg"></i> Tambah Layanan
    </button>
@endsection

@section('content')
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th>Tipe</th>
                    <th>Harga</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                @php
                    $typeIcon  = ['grooming' => '✂️', 'antar_jemput' => '🚗', 'penitipan' => '🏠'];
                    $typeColor = ['grooming' => 'badge-primary', 'antar_jemput' => 'badge-warning', 'penitipan' => 'badge-success'];
                @endphp
                <tr>
                    <td style="font-weight:600;">{{ $service->name }}</td>
                    <td>
                        <span class="badge {{ $typeColor[$service->type] ?? 'badge-muted' }}">
                            {{ $typeIcon[$service->type] ?? '' }} {{ ucfirst(str_replace('_',' ',$service->type)) }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:700;color:var(--primary);">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                        @if($service->type === 'penitipan')
                            <div style="font-size:11px;color:var(--text-muted);">per hari</div>
                        @endif
                    </td>
                    <td style="font-size:13px;color:var(--text-muted);max-width:180px;">{{ $service->description ?? '-' }}</td>
                    <td>
                        @if($service->is_active)
                            <span class="badge badge-success"><i class="bi bi-circle-fill" style="font-size:8px;"></i> Aktif</span>
                        @else
                            <span class="badge badge-muted"><i class="bi bi-circle-fill" style="font-size:8px;"></i> Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:8px;">
                            <button class="btn btn-outline btn-sm" onclick="openModal('editService{{ $service->id }}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('owner.services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Hapus layanan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal-overlay" id="editService{{ $service->id }}">
                    <div class="modal-box">
                        <div class="modal-header">
                            <span class="modal-title">Edit Layanan</span>
                            <button class="modal-close" onclick="closeModal('editService{{ $service->id }}')"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <form action="{{ route('owner.services.update', $service->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body">
                                <div style="display:grid;gap:14px;">
                                    <div>
                                        <label class="form-label">Tipe Layanan</label>
                                        <select name="type" class="form-select">
                                            <option value="grooming" {{ $service->type == 'grooming' ? 'selected' : '' }}>✂️ Grooming</option>
                                            <option value="antar_jemput" {{ $service->type == 'antar_jemput' ? 'selected' : '' }}>🚗 Antar Jemput</option>
                                            <option value="penitipan" {{ $service->type == 'penitipan' ? 'selected' : '' }}>🏠 Penitipan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">Nama Layanan</label>
                                        <input type="text" name="name" class="form-control" value="{{ $service->name }}" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Harga (Rp)</label>
                                        <input type="number" name="price" class="form-control" value="{{ $service->price }}" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="description" class="form-control" rows="2" style="resize:none;">{{ $service->description }}</textarea>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <input type="checkbox" name="is_active" id="active{{ $service->id }}" class="form-check-input" {{ $service->is_active ? 'checked' : '' }} style="width:18px;height:18px;">
                                        <label for="active{{ $service->id }}" class="form-label" style="margin:0;">Layanan Aktif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline" onclick="closeModal('editService{{ $service->id }}')">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-stars"></i><p>Belum ada layanan. Tambahkan paket layanan pertama Anda.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal-overlay" id="addServiceModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Tambah Layanan Baru</span>
            <button class="modal-close" onclick="closeModal('addServiceModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="{{ route('owner.services.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div style="display:grid;gap:14px;">
                    <div>
                        <label class="form-label">Tipe Layanan</label>
                        <select name="type" class="form-select">
                            <option value="grooming">✂️ Grooming</option>
                            <option value="antar_jemput">🚗 Antar Jemput</option>
                            <option value="penitipan">🏠 Penitipan (harga per hari)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Nama Layanan</label>
                        <input type="text" name="name" class="form-control" placeholder="cth: Grooming Medium Size" required>
                    </div>
                    <div>
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="price" class="form-control" placeholder="150000" required>
                    </div>
                    <div>
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2" style="resize:none;" placeholder="Deskripsi singkat layanan..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addServiceModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Layanan</button>
            </div>
        </form>
    </div>
</div>
@endsection
