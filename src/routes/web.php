<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TimelineController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline');
    Route::get('/timeline/posts', [TimelineController::class, 'posts'])->name('timeline.posts');
    Route::get('/timeline/user', [TimelineController::class, 'user'])->name('timeline.user');
    Route::get('/timeline/users', [TimelineController::class, 'users'])->name('timeline.users');
    Route::post('/timeline/like', [TimelineController::class, 'like'])->name('timeline.like');
    Route::post('/timeline/unlike', [TimelineController::class, 'unlike'])->name('timeline.unlike');
    Route::post('/timeline/follow', [TimelineController::class, 'follow'])->name('timeline.follow');
    Route::post('/timeline/unfollow', [TimelineController::class, 'unfollow'])->name('timeline.unfollow');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/schedule', [CalendarController::class, 'schedule'])->name('calendar.schedule');

    Route::resource('/notes', NoteController::class)->except(['create', 'edit', 'show']);
    Route::put('/notes/archive/{note}', [NoteController::class, 'archive'])->name('notes.archive');
    Route::put('/notes/retrieve/{note}', [NoteController::class, 'retrieve'])->name('notes.retrieve');

    Route::resource('/tags', TagController::class)->except(['create', 'edit', 'show']);
    Route::get('/tags/items/select', [TagController::class, 'selectItems'])->name('tags.items.select');
    Route::get('/tags/items/datatable', [TagController::class, 'datatableItems'])->name('tags.items.datatable');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/image', [ProfileController::class, 'upload'])->name('profile.upload');
    Route::delete('/profile/image', [ProfileController::class, 'deleteImage'])->name('profile.delete.image');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
