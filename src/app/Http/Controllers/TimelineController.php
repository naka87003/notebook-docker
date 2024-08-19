<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TimelineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Timeline');
    }

    /**
     * タイムラインに表示するNotesを取得
     */
    public function posts(Request $request)
    {
        $query = Note::where('category_id', 1)->where('public', true);

        if ($request->search) {
            $query->where(function (Builder $query) use ($request) {
                $query->whereLike('title', "%{$request->search}%")
                    ->orWhereLike('content', "%{$request->search}%")
                    ->orWhereHas('user', function (Builder $query) use ($request) {
                        $query->whereLike('name', "%{$request->search}%");
                    })
                    ->orWhereHas('tag', function (Builder $query) use ($request) {
                        $query->whereLike('name', "%{$request->search}%");
                    });
            });
        }
        if ($request->onlyMyLiked === 'true') {
            $query->whereHas('likes', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
        }
        if ($request->user) {
            $query->where('user_id', $request->user);
        }
        if (is_numeric($request->offset)) {
            $query->offset($request->offset);
        }
        $notes = $query->with(['user', 'category', 'status', 'tag', 'likes'])->orderBy('updated_at', 'DESC')->limit(20)->get();
        return response()->json($notes);
    }

    public function like(Request $request)
    {
        if (Note::find($request->note_id)) {
            Like::create([
                'user_id' => Auth::id(),
                'note_id' => $request->note_id
            ]);
        }

        return response()->json();
    }

    public function unlike(Request $request)
    {
        if (Note::find($request->note_id)) {
            Like::where('note_id', $request->note_id)->where('user_id', Auth::id())->delete();
        }
        return response()->json();
    }

    public function users(Request $request)
    {
        $query =  User::whereLike('name', "%{$request->search}%");
        $users = $query->orderBy('updated_at', 'DESC')->limit(10)->get();

        return response()->json($users);
    }
}
