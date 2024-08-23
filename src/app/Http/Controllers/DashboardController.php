<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
        $props = [
            'categoryItems' => Category::get(),
            'unreadNotificationCount' => Auth::user()->unreadNotifications->count()
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
