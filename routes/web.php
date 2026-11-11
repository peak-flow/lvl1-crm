<?php

use App\Http\Controllers\LessonController;
use App\Http\Controllers\LogoutController;
use App\Livewire\Admin\EditMembership;
use App\Livewire\Admin\MemberList;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Ideas\IdeaBoard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/learn', [LessonController::class, 'index'])->name('learn.index');
Route::get('/learn/{lessonSlug}', [LessonController::class, 'show'])->name('learn.show');

Route::middleware('guest')->group(function () {
    Route::livewire('/register', Register::class)->name('register');
    Route::livewire('/login', Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::livewire('/ideas', IdeaBoard::class)->name('ideas.index');
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::middleware('can:manage-members')->group(function () {
        Route::livewire('/admin/members', MemberList::class)->name('admin.members.index');
        Route::livewire('/admin/members/{member}', EditMembership::class)->name('admin.members.edit');
    });
});
