@extends('layouts.app')
@section('title', 'Manajemen Admin')
@section('subtitle', 'Kelola akun admin yang memiliki akses operasional')

@section('topbar-action')
    <button class="btn btn-primary" onclick="openModal('addAdminModal')">
        <i class="bi bi-plus-lg"></i> Tambah Admin
    </button>
@endsection

@section('content')
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Admin</th>
                    <th>Email</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="user-avatar" style="width:38px;height:38px;font-size:13px;border-radius:10px;background:linear-gradient(135deg,#F7A87C,#F7876F);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;">
                                {{ substr($admin->name, 0, 1) }}
                            </div>
                            <span style="font-weight:600;">{{ $admin->name }}</span>
                        </div>
                    </td>
                    <td>{{ $admin->email }}</td>
                    <td style="color:var(--text-muted);font-size:13px;">{{ $admin->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:8px;">
                            <button class="btn btn-warning btn-sm" onclick="openModal('pwdModal{{ $admin->id }}')">
                                <i class="bi bi-key"></i> Reset Password
                            </button>
                            <form action="{{ route('owner.admins.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Hapus admin ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Reset Password Modal -->
                <div class="modal-overlay" id="pwdModal{{ $admin->id }}">
                    <div class="modal-box">
                        <div class="modal-header">
                            <span class="modal-title">Reset Password — {{ $admin->name }}</span>
                            <button class="modal-close" onclick="closeModal('pwdModal{{ $admin->id }}')"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <form action="{{ route('owner.admins.password', $admin->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body">
                                <div style="margin-bottom:14px;">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
                                </div>
                                <div>
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline" onclick="closeModal('pwdModal{{ $admin->id }}')">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Password</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr><td colspan="4"><div class="empty-state"><i class="bi bi-shield-person"></i><p>Belum ada admin terdaftar</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal-overlay" id="addAdminModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Tambah Admin Baru</span>
            <button class="modal-close" onclick="closeModal('addAdminModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="{{ route('owner.admins.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div style="display:grid;gap:14px;">
                    <div>
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama admin" required>
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@petcare.com" required>
                    </div>
                    <div>
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
                    </div>
                    <div>
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addAdminModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Admin</button>
            </div>
        </form>
    </div>
</div>
@endsection
