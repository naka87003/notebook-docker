<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $props = [
            'categoryItems' => Category::get(),
            'unreadNotificationCount' => $user->unreadNotifications->count(),
            'newRegisteredUser' => Note::where('user_id', $user->id)->doesntExist()
        ];
        if (isset($request->tag) && is_numeric($request->tag)) {
            $props['tag'] = (int)$request->tag;
        }
        if (isset($request->status) && is_numeric($request->status)) {
            $props['status'] = (int)$request->status;
        }

        

        return Inertia::render('Dashboard', $props);
    }
}
