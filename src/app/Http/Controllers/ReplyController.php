<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $commentId, Request $request)
    {
        $skip = is_numeric($request->offset) ? $request->offset : 0;
        $comments = Reply::where('comment_id', $commentId)->orderBy('updated_at', 'DESC')->offset($skip)->limit(20)->with(['user'])->get();
        return response()->json($comments);
    }

    public function store(string $commentId, Request $request)
    {
        $request->validate([
            'reply' => 'required|max:160',
        ]);
        Reply::create([
            'content' => $request->reply,
            'user_id' => Auth::user()->id,
            'comment_id' => $commentId,
        ]);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reply $reply)
    {
        $reply->delete();
        return response()->json();
    }
}
