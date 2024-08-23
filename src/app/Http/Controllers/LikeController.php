<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function like($note_id)
    {
        $exists = Like::where('note_id', $note_id)->where('user_id', Auth::id())->exists();
        if ($exists === false && Note::find($note_id)) {
            Like::create([
                'user_id' => Auth::id(),
                'note_id' => $note_id
            ]);
        }
        return response()->json();
    }

    public function unlike($note_id)
    {
        Like::where('note_id', $note_id)->where('user_id', Auth::id())->delete();
        return response()->json();
    }
}
