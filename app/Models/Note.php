<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Note extends Model
{
    protected $fillable = [
        'subject_id', 'topic_id', 'title', 'slug', 'description', 'price',
        'is_free', 'file_path', 'cover_image', 'downloads_count',
        'status', 'is_active', 'order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'is_active' => 'boolean',
        'downloads_count' => 'integer',
        'order' => 'integer',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getFinalPriceAttribute(): float
    {
        return $this->is_free ? 0 : (float) $this->price;
    }

    public function getCoverImageUrlAttribute(): string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : asset('images/default-note-cover.svg');
    }

    public function getFileUrlAttribute(): string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : '#';
    }
}
