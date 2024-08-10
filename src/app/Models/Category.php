<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * Categoryに所属するNoteをまとめて取得
     */
    public function Notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
