<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TagController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/calendar', function () {
        return Inertia::render('Calendar');
    })->name('calendar');

    Route::resource('/notes', NoteController::class)->except(['create']);
    Route::put('/notes/archive/{note}', [NoteController::class, 'archive'])->name('notes.archive');
    Route::put('/notes/retrieve/{note}', [NoteController::class, 'retrieve'])->name('notes.retrieve');
    
    Route::resource('/tags', TagController::class)->except(['create', 'show']);
    Route::get('/tags/items/select', [TagController::class, 'selectItems'])->name('tags.items.select');
    Route::get('/tags/items/datatable', [TagController::class, 'datatableItems'])->name('tags.items.datatable');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
