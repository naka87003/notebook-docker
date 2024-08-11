<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function selectItems()
    {
        $items = Category::select('name AS title', 'id AS value')->get();

        return $items;
    }
}