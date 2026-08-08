<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'course_id', 'amount', 'status',
        'payment_method', 'xendit_invoice_id', 'xendit_invoice_url',
        'paid_at', 'expires_at', 'xendit_payload',
    ];

    protected $casts = [
        'paid_at'        => 'datetime',
        'expires_at'     => 'datetime',
        'xendit_payload' => 'array',
        'amount'         => 'decimal:2',
    ];

    public function user()   { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }

    public function isPaid()    { return $this->status === 'paid'; }
    public function isPending() { return $this->status === 'pending'; }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public static function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . date('Ymd');
    }
}
