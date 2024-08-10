<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * Tagに所属するItemをまとめて取得
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
