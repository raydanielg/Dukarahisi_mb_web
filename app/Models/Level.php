<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    protected $fillable = ['name', 'order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function classRooms(): HasMany
    {
        return $this->hasMany(ClassRoom::class)->orderBy('order');
    }

    public function notes(): HasMany
    {
        return $this->hasManyThrough(Note::class, ClassRoom::class);
    }
}
