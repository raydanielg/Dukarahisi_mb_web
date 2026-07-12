<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Syllabus extends Model
{
    protected $table = 'syllabi';

    protected $fillable = ['subject_id', 'topic_id', 'title', 'description', 'price', 'is_free', 'file_path', 'order', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function getFinalPriceAttribute(): float
    {
        return $this->is_free ? 0 : (float) $this->price;
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
