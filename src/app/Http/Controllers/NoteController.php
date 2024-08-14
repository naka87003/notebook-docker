<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Note::where('user_id', Auth::id())
            ->whereIn('category_id', $request->category)
            ->where('status_id', $request->status);
        if ($request->search) {
            $query->where(function (Builder $query) use ($request) {
                $query->whereLike('title', "%{$request->search}%")
                    ->orWhereLike('content', "%{$request->search}%")
                    ->orWhereHas('tag', function (Builder $query) use ($request) {
                        $query->whereLike('name', "%{$request->search}%");
                    });
            });
        }
        if ($request->key === 'starts_at') {
            $query->where('category_id', 3);
        }
        if ($request->tag) {
            $query->whereIn('tag_id', $request->tag);
        }
        if (is_numeric($request->offset)) {
            $query->offset($request->offset);
        }
        $notes = $query->with(['user', 'category', 'status', 'tag'])->orderBy($request->key, $request->order)->limit(20)->get();
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
            'content' => 'required_unless:category,3|max:500',
            'title' => 'required_if:category,3|max:50',
            'public' => 'required|boolean',
            'category' => 'required|numeric',
            'tag' => 'numeric|nullable',
            'starts' => 'exclude_unless:category,3|date',
            'ends' => 'exclude_unless:category,3|date|after:starts'
        ]);

        Note::create([
            'title' => $request->title,
            'content' => $request->content,
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
    public function edit(Note $note)
    {
        return response()->json($note);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        $request->validate([
            'content' => 'required_unless:category,3|max:500',
            'title' => 'required_if:category,3|max:50',
            'public' => 'required|boolean',
            'category' => 'required|numeric',
            'tag' => 'numeric|nullable',
            'starts' => 'exclude_unless:category,3|date',
            'ends' => 'exclude_unless:category,3|date|after:starts'
        ]);

        $note->update([
            'title' => $request->title,
            'content' => $request->content,
            'public' => $request->public,
            'category_id' => $request->category,
            'tag_id' => $request->tag,
            'starts_at' => $request->starts,
            'ends_at' => $request->ends,
        ]);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json();
    }

    /**
     * change status to `archived`
     */
    public function archive(Note $note)
    {
        $note->status_id = 2;
        $note->save();
        return response()->json();
    }

    /**
     * change status to `active`
     */
    public function retrieve(Note $note)
    {
        $note->status_id = 1;
        $note->save();
        return response()->json();
    }
}
