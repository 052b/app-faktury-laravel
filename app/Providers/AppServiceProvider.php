<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoicePosition;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        Schema::defaultStringLength(125);

        Paginator::useBootstrapFive();

        if(env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        Client::preventLazyLoading(! app()->isProduction());
        Invoice::preventLazyLoading(! app()->isProduction());
        InvoicePosition::preventLazyLoading(! app()->isProduction());
    }
}
