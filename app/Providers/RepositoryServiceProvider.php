<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\MerchantRepositoryInterface;
use App\Repositories\Eloquent\MerchantRepository;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Repositories\Eloquent\WalletRepository;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Eloquent\TransactionRepository;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use App\Repositories\Eloquent\AuditLogRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MerchantRepositoryInterface::class, MerchantRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(AuditLogRepositoryInterface::class, AuditLogRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
