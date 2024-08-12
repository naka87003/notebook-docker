<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function selectItems()
    {
        $items = Tag::where('user_id', Auth::id())->get();

        return $items;
    }
}
