<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'type',
        'breed',
        'age_years',
        'gender',
        'notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getEmojiAttribute(): string
    {
        return match(strtolower($this->type ?? '')) {
            'kucing'  => '🐱',
            'anjing'  => '🐶',
            'kelinci' => '🐰',
            'hamster' => '🐹',
            'burung'  => '🐦',
            default   => '🐾',
        };
    }
}
