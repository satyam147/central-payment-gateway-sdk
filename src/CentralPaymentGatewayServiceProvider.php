<?php

namespace Satyam147\CentralPaymentGateway;

use Illuminate\Support\ServiceProvider;
use Satyam147\CentralPaymentGateway\CentralPaymentGatewayClient;

class CentralPaymentGatewayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        // Publish config file
        $this->publishes([
            __DIR__ . '/../config/central-payment-gateway.php' => config_path('central-payment-gateway.php'),
        ], 'central-payment-gateway-config');
    }

    /**
     * Register package services.
     */
    public function register(): void
    {
        // Merge the config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/central-payment-gateway.php', 'central-payment-gateway'
        );
    }
}
