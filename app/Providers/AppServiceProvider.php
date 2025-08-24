<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ComplaintService;
use App\Contracts\ComplaintServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind the interface to the implementation
        $this->app->bind(ComplaintServiceInterface::class, ComplaintService::class);
        
        // Register the ComplaintService as a singleton
        $this->app->singleton(ComplaintService::class, function ($app) {
            return new ComplaintService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
