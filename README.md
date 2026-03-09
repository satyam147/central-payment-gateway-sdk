# Central Payment Gateway (satyam147/central-payment-gateway)

A modular Laravel package for central payment gateway functionality.

## Installation

Require the package in your Laravel project (for local development, use Composer path repository):

```
composer require satyam147/central-payment-gateway
```

## Service Provider

The package registers its own service provider.

## Package Requirements

- Laravel Framework >= 12.0
- guzzlehttp/guzzle >= 7.0

## Publish the Config

To publish the config file to your app, run:

```
php artisan vendor:publish --tag=central-payment-gateway-config
```

## SDK Usage Example

### Initialize the client
```php
use Satyam147\CentralPaymentGateway\Client;

// Easiest: Automatically uses config values from config/central-payment-gateway.php
$client = new Client();

// Or override config values with explicit arguments
$client = new Client(
    'https://your-gateway.example.com', // baseUrl
    'your_public_key',                  // apiKey
    'your_secret_key',                  // apiSecret
);
```

### Initiate a payment
```php
$response = $client->initiatePayment([
    'amount' => 1000,
    'currency' => 'INR',
    // ... other fields
]);
$data = json_decode($response->getBody(), true);
```

### Get payment status
```php
$response = $client->getPaymentStatus('payment_id_123');
$status = json_decode($response->getBody(), true);
```

### List transactions
```php
// Optionally provide filters e.g., ['from_date' => '2026-01-01', 'status' => 'success']
$response = $client->listTransactions();
$transactions = json_decode($response->getBody(), true);
```

See `src/Client.php` for method details.