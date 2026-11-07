<?php

namespace App\Providers;

use App\Contracts\Repositories\AddonRepositoryInterface;
use App\Contracts\Repositories\EventRepositoryInterface;
use App\Contracts\Repositories\RegistrantRepositoryInterface;
use App\Contracts\Repositories\VoucherRepositoryInterface;
use App\Repositories\AddonRepository;
use App\Repositories\EventRepository;
use App\Repositories\RegistrantRepository;
use App\Repositories\VoucherRepository;
use App\Services\Payment\CalculationService;
use App\Services\Payment\PaymentSimulationService;
use App\Services\Ticket\TicketGenerationService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EventRepositoryInterface::class, EventRepository::class);
        $this->app->singleton(RegistrantRepositoryInterface::class, RegistrantRepository::class);
        $this->app->singleton(AddonRepositoryInterface::class, AddonRepository::class);
        $this->app->singleton(VoucherRepositoryInterface::class, VoucherRepository::class);

        $this->app->singleton(CalculationService::class);
        $this->app->singleton(PaymentSimulationService::class);
        $this->app->singleton(TicketGenerationService::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
