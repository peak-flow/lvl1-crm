<?php

namespace App\Providers;

use App\Http\Middleware\EnsureWaiverIsSigned;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Livewire re-runs only the middleware it knows about when a button is
        // pressed, so ours is added to that list.
        Livewire::addPersistentMiddleware([EnsureWaiverIsSigned::class]);

        Gate::define('manage-members', function (User $member) {
            return $member->is_admin;
        });
    }
}
