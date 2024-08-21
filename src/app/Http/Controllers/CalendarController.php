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
}
