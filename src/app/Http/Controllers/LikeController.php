<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function like(Request $request)
    {
        $exists = Like::where('note_id', $request->note_id)->where('user_id', Auth::id())->exists();
        if ($exists === false && Note::find($request->note_id)) {
            Like::create([
                'user_id' => Auth::id(),
                'note_id' => $request->note_id
            ]);
        }
        return response()->json();
    }

    public function unlike(Request $request)
    {
        Like::where('note_id', $request->note_id)->where('user_id', Auth::id())->delete();
        return response()->json();
    }
}
