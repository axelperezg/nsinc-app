<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StrategyDraft extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'draft_data',
        'last_saved_at',
    ];

    protected $casts = [
        'draft_data' => 'array',
        'last_saved_at' => 'datetime',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
