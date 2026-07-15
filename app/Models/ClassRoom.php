<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassRoom extends Model
{
    protected $fillable = ['level_id', 'sub_level_id', 'name', 'description', 'order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function subLevel(): BelongsTo
    {
        return $this->belongsTo(SubLevel::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class)->orderBy('order');
    }

    public function notes(): HasMany
    {
        return $this->hasManyThrough(Note::class, Subject::class);
    }
}
