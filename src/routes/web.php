<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline');
  
    Route::get('/users/user', [UserController::class, 'user'])->name('users.user');
    Route::get('/users/users', [UserController::class, 'users'])->name('users.users');

    Route::post('/likes/like', [LikeController::class, 'like'])->name('likes.like');
    Route::post('/likes/unlike', [LikeController::class, 'unlike'])->name('likes.unlike');

    Route::post('/follows/follow', [FollowController::class, 'follow'])->name('follows.follow');
    Route::post('/follows/unfollow', [FollowController::class, 'unfollow'])->name('follows.unfollow');
    
    Route::resource('/notes', NoteController::class)->except(['create', 'edit', 'show']);
    Route::get('/notes/posts', [NoteController::class, 'posts'])->name('notes.posts');
    Route::put('/notes/archive/{note}', [NoteController::class, 'archive'])->name('notes.archive');
    Route::put('/notes/retrieve/{note}', [NoteController::class, 'retrieve'])->name('notes.retrieve');
    Route::get('/notes/schedule', [NoteController::class, 'schedule'])->name('notes.schedule');
    Route::get('/notes/likes/{note}', [NoteController::class, 'likes'])->name('notes.likes');

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
