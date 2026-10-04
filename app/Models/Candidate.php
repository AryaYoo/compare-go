<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_id', 'name', 'images',
        'ai_result', 'total_score', 'ai_status', 'ai_error', 'analyzed_at',
    ];

    protected $casts = [
        'images'      => 'array',
        'ai_result'   => 'array',
        'total_score' => 'float',
        'analyzed_at' => 'datetime',
    ];

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(CandidateScore::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->ai_status) {
            'pending'    => 'Menunggu',
            'processing' => 'Menganalisis',
            'done'       => 'Selesai',
            'failed'     => 'Gagal',
            default      => $this->ai_status,
        };
    }
}
