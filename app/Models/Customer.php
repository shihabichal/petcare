<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'name',
        'phone_number',
        'address',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            $customer->customer_code = self::generateCode();
        });
    }

    public static function generateCode(): string
    {
        $date  = now()->format('Ymd');
        $prefix = "CST-{$date}-";
        $last  = self::where('customer_code', 'like', $prefix . '%')
                     ->orderByDesc('customer_code')
                     ->value('customer_code');

        $seq = $last ? (intval(substr($last, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
