<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory;

    /**
     *  Itemを作成したUserを取得
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     *  Itemの属するCategoryを取得
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     *  ItemのTagを取得
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    /**
     *  ItemのStatusを取得
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
