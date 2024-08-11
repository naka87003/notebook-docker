<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CategoryService $category)
    {
        return Inertia::render('Dashboard',[
            'categoryItems' => $category->selectItems()
        ]);
    }
}
