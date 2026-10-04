<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Procurement extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status'];

    public function criteria(): HasMany
    {
        return $this->hasMany(Criterion::class)->orderBy('order');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class)->latest();
    }

    public function getCriteriaWeightTotalAttribute(): int
    {
        return $this->criteria->sum('weight');
    }
}
