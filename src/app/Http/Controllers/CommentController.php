<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $noteId)
    {
        $props = [
            'unreadNotificationCount' => Auth::user()->unreadNotifications->count(),
            'note' => Note::with(['user', 'category', 'status', 'tag', 'likes'])->withCount(['comments'])->find($noteId)
        ];
        return Inertia::render('Comment', $props);
    }

    public function store(string $noteId, Request $request)
    {
        $request->validate([
            'comment' => 'required|max:300',
        ]);
        Comment::create([
            'content' => $request->comment,
            'user_id' => Auth::user()->id,
            'note_id' => $noteId,
        ]);

        return back();
    }

    public function comments(string $noteId, Request $request) {

        $skip = is_numeric($request->offset) ? $request->offset : 0;
        $comments = Comment::where('note_id', $noteId)->orderBy('updated_at', 'DESC')->offset($skip)->limit(20)->with(['user'])->get();
        return response()->json($comments);
    }
}
