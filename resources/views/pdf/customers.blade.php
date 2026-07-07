<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #2D3250; background: white; }

    .header {
        background: linear-gradient(135deg, #7C6FF7, #5A52D5);
        color: white;
        padding: 20px 28px;
        margin-bottom: 24px;
        border-radius: 0 0 12px 12px;
    }
    .header h1 { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
    .header p  { font-size: 11px; opacity: 0.85; }
    .header-meta { margin-top: 12px; display: flex; gap: 24px; font-size: 11px; }
    .header-meta span { opacity: 0.9; }

    .content { padding: 0 28px 28px; }

    .customer-card {
        border: 1px solid #EAECF0;
        border-radius: 10px;
        margin-bottom: 16px;
        overflow: hidden;
        page-break-inside: avoid;
    }
    .customer-header {
        background: #F5F6FA;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #EAECF0;
    }
    .customer-name { font-size: 13px; font-weight: 700; color: #2D3250; }
    .customer-code {
        font-size: 10px;
        font-weight: 700;
        background: #EEF0FF;
        color: #7C6FF7;
        padding: 3px 8px;
        border-radius: 20px;
        font-family: monospace;
    }
    .customer-body { padding: 12px 16px; }

    .info-grid { display: flex; gap: 32px; margin-bottom: 12px; }
    .info-item label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #8A95A5; font-weight: 700; display: block; margin-bottom: 2px; }
    .info-item span  { font-size: 11px; font-weight: 500; }

    .pets-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #8A95A5; margin-bottom: 8px; }
    .pet-row {
        display: flex;
        gap: 16px;
        padding: 8px 10px;
        background: #FAFBFF;
        border-radius: 6px;
        margin-bottom: 4px;
        border: 1px solid #EAECF0;
    }
    .pet-row span { font-size: 11px; }
    .pet-label  { color: #8A95A5; font-size: 10px; }

    .trx-badge {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 20px;
        font-size: 9px;
        font-weight: 700;
        background: #E8F7F2;
        color: #4CAF8C;
    }

    .footer {
        margin-top: 24px;
        padding-top: 12px;
        border-top: 1px solid #EAECF0;
        text-align: center;
        font-size: 9px;
        color: #8A95A5;
    }
</style>
</head>
<body>

<div class="header">
    <h1>🐾 Laporan Data Customer — PetCare</h1>
    <p>Dokumen ini berisi seluruh data customer yang terdaftar di sistem PetCare</p>
    <div class="header-meta">
        <span>📅 Tanggal Cetak: {{ now()->format('d F Y, H:i') }} WIB</span>
        <span>👥 Total Customer: {{ $customers->count() }}</span>
    </div>
</div>

<div class="content">
    @foreach($customers as $i => $customer)
    <div class="customer-card">
        <div class="customer-header">
            <div class="customer-name">{{ $i + 1 }}. {{ $customer->name }}</div>
            <span class="customer-code">{{ $customer->customer_code }}</span>
        </div>
        <div class="customer-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>No. WhatsApp</label>
                    <span>{{ $customer->phone_number }}</span>
                </div>
                <div class="info-item">
                    <label>Alamat</label>
                    <span>{{ $customer->address ?: '-' }}</span>
                </div>
                <div class="info-item">
                    <label>Bergabung</label>
                    <span>{{ $customer->created_at->format('d M Y') }}</span>
                </div>
                <div class="info-item">
                    <label>Total Transaksi</label>
                    <span class="trx-badge">{{ $customer->transactions->count() }} transaksi</span>
                </div>
            </div>

            @if($customer->pets->count() > 0)
            <div class="pets-title">🐾 Hewan Peliharaan</div>
            @foreach($customer->pets as $pet)
            <div class="pet-row">
                <div>
                    <div class="pet-label">Nama</div>
                    <span>{{ $pet->name }}</span>
                </div>
                <div>
                    <div class="pet-label">Jenis</div>
                    <span>{{ $pet->type ?: '-' }}</span>
                </div>
                <div>
                    <div class="pet-label">Ras</div>
                    <span>{{ $pet->breed ?: '-' }}</span>
                </div>
                <div>
                    <div class="pet-label">Umur</div>
                    <span>{{ $pet->age_years ? $pet->age_years . ' tahun' : '-' }}</span>
                </div>
                <div>
                    <div class="pet-label">Kelamin</div>
                    <span>{{ $pet->gender ?: '-' }}</span>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
    @endforeach

    <div class="footer">
        Dokumen ini dibuat otomatis oleh Sistem PetCare Admin &bull; {{ now()->format('d/m/Y H:i') }}
    </div>
</div>
</body>
</html>
