<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletPaymentRequest extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'utr_number',
        'screenshot_path',
        'status',
        'admin_note',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve(int $adminId): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);

        // Credit user wallet
        $this->user->wallet->increment('balance', $this->amount);
        $this->user->wallet->increment('lifetime_earnings', $this->amount);
    }

    public function reject(string $note = null): void
    {
        $this->update([
            'status' => 'rejected',
            'admin_note' => $note,
        ]);
    }
}
