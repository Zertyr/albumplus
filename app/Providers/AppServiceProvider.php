<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use App\Repositories\CategoryRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->admin;
        });
        if (request ()->server ("SCRIPT_NAME") !== 'artisan') {
            view ()->share ('categories', resolve(CategoryRepository::class)->getAll());
        }
    }
}
