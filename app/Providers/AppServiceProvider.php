<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Blade;
use Session;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
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

        View::composer('*', function ($view) {

            if (Auth::check()) {

                $view->with([
                    'notifications'      => Auth::user()->unreadNotifications,
                    'notificationCount'  => Auth::user()->unreadNotifications->count(),
                ]);

            } else {

                $view->with([
                    'notifications'     => collect(),
                    'notificationCount' => 0,
                ]);
            }
        });
        
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);
        Blade::directive('toastr', function ($expression){
            return "<script>
                    toastr.{{ Session::get('alert-type') }}($expression)
                 </script>";
        });
    }
}
