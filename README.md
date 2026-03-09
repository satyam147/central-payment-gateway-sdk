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
use Satyam147\CentralPaymentGateway\CentralPaymentGatewayClient;

// Easiest: Automatically uses config values from config/central-payment-gateway.php
$client = new CentralPaymentGatewayClient();

// Or override config values with explicit arguments
$client = new CentralPaymentGatewayClient(
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

### Get payment status/details
```php
$response = $client->getPaymentStatus('txn_abc123'); // Your transaction reference
$status = json_decode($response->getBody(), true);
```

### Check payment status (manual)
```php
$response = $client->checkPaymentStatus('txn_abc123');
$statusCheck = json_decode($response->getBody(), true);
```

### Refund payment
```php
$response = $client->refundPayment('txn_abc123', [
    'amount' => 500, // partial/main refund amount
]);
$refund = json_decode($response->getBody(), true);
```

### Get refund summary
```php
$response = $client->getRefundSummary('txn_abc123');
$summary = json_decode($response->getBody(), true);
```

See `src/CentralPaymentGatewayClient.php` for more details.