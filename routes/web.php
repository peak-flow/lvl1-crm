<?php

use App\Http\Controllers\LessonController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\PageIdeaController;
use App\Http\Controllers\StyleIdeaController;
use App\Livewire\Admin\EditMembership;
use App\Livewire\Admin\MemberList;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Ideas\IdeaBoard;
use App\Livewire\Members\WaiverForm;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/learn', [LessonController::class, 'index'])->name('learn.index');
Route::get('/learn/{lessonSlug}', [LessonController::class, 'show'])->name('learn.show');

Route::get('/style-ideas', [StyleIdeaController::class, 'index'])->name('style-ideas.index');
Route::get('/style-ideas/{styleSlug}', [StyleIdeaController::class, 'show'])->name('style-ideas.show');

Route::get('/page-ideas', [PageIdeaController::class, 'index'])->name('page-ideas.index');
Route::get('/page-ideas/{pageSlug}', [PageIdeaController::class, 'show'])->name('page-ideas.show');

Route::middleware('guest')->group(function () {
    Route::livewire('/register', Register::class)->name('register');
    Route::livewire('/login', Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::livewire('/waiver', WaiverForm::class)->name('waiver.edit');
    Route::livewire('/ideas', IdeaBoard::class)->name('ideas.index')->middleware('waiver.signed');
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::middleware('can:manage-members')->group(function () {
        Route::livewire('/admin/members', MemberList::class)->name('admin.members.index');
        Route::livewire('/admin/members/{member}', EditMembership::class)->name('admin.members.edit');
    });
});
