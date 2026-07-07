<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'customer_id',
        'pet_id',
        'service_id',
        'total_price',
        'payment_status',
        'status',
        'start_date',
        'end_date',
        'days',
        'pickup_required',
        'pickup_address',
        'pickup_time',
        'notes',
        'notes_internal',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'pickup_required'  => 'boolean',
        'pickup_time'      => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->transaction_code = self::generateCode();
        });
    }

    public static function generateCode(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "TRX-{$date}-";
        $last   = self::where('transaction_code', 'like', $prefix . '%')
                      ->orderByDesc('transaction_code')
                      ->value('transaction_code');

        $seq = $last ? (intval(substr($last, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getPaymentBadgeAttribute(): string
    {
        return match($this->payment_status) {
            'paid'     => '<span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Lunas</span>',
            'refunded' => '<span class="badge badge-danger">Refunded</span>',
            default    => '<span class="badge badge-warning"><i class="bi bi-clock-fill"></i> Belum Bayar</span>',
        };
    }
}
