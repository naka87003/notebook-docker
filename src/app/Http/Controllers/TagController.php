<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function selectItems()
    {
        $items = Tag::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();

        return $items;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|max:20',
            'color' => 'nullable|string',
        ]);

        Tag::create([
            'name' => $request->name,
            'hex_color' => $request->color,
            'user_id' => Auth::id()
        ]);

        return back();
    }
}
