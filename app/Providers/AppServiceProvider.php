<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Field;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;

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
        Product::observe(AuditObserver::class);
        Category::observe(AuditObserver::class);
        Field::observe(AuditObserver::class);
        InventoryTransaction::observe(AuditObserver::class);
    }
}
