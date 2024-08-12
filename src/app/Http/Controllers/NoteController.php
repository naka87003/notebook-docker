<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Note::where('status_id', 1)->with(['user', 'category', 'status', 'tag'])->orderBy('updated_at', 'desc')->get();
        return response()->json($notes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'description' => 'required_unless:category,3|max:500',
            'title' => 'required_if:category,3|max:50',
            'public' => 'required|boolean',
            'category' => 'required|numeric',
            'tag' => 'numeric|nullable',
            'starts' => 'exclude_unless:category,3|date',
            'ends' => 'exclude_unless:category,3|date|after:starts'
        ]);

        Note::create([
            'title' => $request->title,
            'description' => $request->description,
            'public' => $request->public,
            'category_id' => $request->category,
            'tag_id' => $request->tag,
            'has_image' => false,
            'user_id' => Auth::id(),
            'starts_at' => $request->starts,
            'ends_at' => $request->ends,
            'status_id' => 1
        ]);

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
