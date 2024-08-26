<?php

namespace App\Http\Controllers;

use App\Models\Reply;
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
        $replies = Reply::where('comment_id', $commentId)->orderBy('updated_at', 'DESC')->offset($skip)->limit(20)->with(['user', 'addressee'])->get();
        return response()->json($replies);
    }

    public function store(string $commentId, Request $request)
    {
        $request->validate([
            'reply' => 'required|max:160',
        ]);

        $props = [
            'content' => $request->reply,
            'user_id' => Auth::user()->id,
            'comment_id' => $commentId,
        ];

        if (isset($request->reply_to) && is_numeric($request->reply_to)) {
            $props['reply_to'] = $request->reply_to;
        }

        Reply::create($props);

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
