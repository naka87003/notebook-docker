<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * Tagに所属するNoteをまとめて取得
     */
    public function Notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
