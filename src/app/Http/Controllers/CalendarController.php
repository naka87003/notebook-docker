<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index()
    {
        return Inertia::render(
            'Calendar',
            ['categoryItems' => Category::get()]
        );
    }

    public function Schedule()
    {
        $schedule = Note::where('user_id', Auth::id())->where('category_id', 3)->with(['user', 'category', 'status', 'tag'])->get();

        return response()->json($schedule);
    }
}
