<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function users(Request $request)
    {
        $query =  User::whereLike('name', "%{$request->search}%");
        $users = $query->orderBy('updated_at', 'DESC')->limit(10)->get();
        return response()->json($users);
    }

    public function user(string $id)
    {
        $user = User::with(['followees', 'followers'])->find((int)$id);
        return response()->json($user);
    }
}
