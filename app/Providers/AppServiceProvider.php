<?php

namespace App\Providers;

use App\Interfaces\Repositories\LoanRepaymentRepositoryInterface;
use App\Interfaces\Repositories\LoanRepositoryInterface;
use App\Repositories\LoanRepaymentRepository;
use App\Repositories\LoanRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoanRepositoryInterface::class, LoanRepository::class);
        $this->app->bind(LoanRepaymentRepositoryInterface::class, LoanRepaymentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
