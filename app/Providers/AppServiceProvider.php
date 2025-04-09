<?php

namespace App\Providers;

use App\Repository\CategoryRepository;
use App\Repository\Interface\ICategoryRepository;
use App\Repository\Interface\ITaskRepository;
use App\Repository\Interface\IUserRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ITaskRepository::class, TaskRepository::class);
        $this->app->singleton(IUserRepository::class, UserRepository::class);
        $this->app->singleton(ICategoryRepository::class, CategoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
