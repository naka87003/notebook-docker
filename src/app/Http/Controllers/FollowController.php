<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function follow(Request $request)
    {
        $exists = Follow::where('followee_id', $request->user_id)->where('follower_id', Auth::id())->exists();
        if ($exists === false && User::find($request->user_id)) {
            Follow::create([
                'follower_id' => Auth::id(),
                'followee_id' => $request->user_id

            ]);
        }
        return response()->json();
    }

    public function unfollow(Request $request)
    {
        Follow::where('followee_id', $request->user_id)->where('follower_id', Auth::id())->delete();
        return response()->json();
    }
}
