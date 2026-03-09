<?php

namespace Satyam147\CentralPaymentGateway;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class Client
{
    protected GuzzleClient $http;
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiSecret;

    public function __construct(?string $baseUrl = null, ?string $apiKey = null, ?string $apiSecret = null)
    {
        $this->baseUrl = rtrim($baseUrl ?? config('central-payment-gateway.base_url'), '/');
        $this->apiKey = $apiKey ?? config('central-payment-gateway.api_key');
        $this->apiSecret = $apiSecret ?? config('central-payment-gateway.api_secret');
        $this->http = new GuzzleClient([
            'base_uri' => $this->baseUrl,
            'timeout' => 10,
        ]);
    }

    /**
     * Stub: Initiate a payment
     *
     * @param array $payload (amount, currency, etc)
     * @return ResponseInterface
     */
    public function initiatePayment(array $payload): ResponseInterface
    {
        $endpoint = '/api/payment';
        $body = json_encode($payload);
        $timestamp = time();
        $signature = $this->generateSignature('POST', $endpoint, $body, $timestamp);

        return $this->http->request('POST', $endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->apiKey,
                'X-Signature' => $signature,
                'X-Timestamp' => $timestamp,
            ],
            'body' => $body,
        ]);
    }

    /**
     * Get payment status
     * @param string $paymentId
     * @return ResponseInterface
     */
    public function getPaymentStatus(string $paymentId): ResponseInterface
    {
        $endpoint = "/api/payment/{$paymentId}";
        $timestamp = time();
        $signature = $this->generateSignature('GET', $endpoint, null, $timestamp);
        return $this->http->request('GET', $endpoint, [
            'headers' => [
                'X-Api-Key' => $this->apiKey,
                'X-Signature' => $signature,
                'X-Timestamp' => $timestamp,
            ]
        ]);
    }

    /**
     * List transactions
     * @param array $filters Optional filters (date range, status, etc)
     * @return ResponseInterface
     */
    public function listTransactions(array $filters = []): ResponseInterface
    {
        $endpoint = '/api/transactions';
        $query = http_build_query($filters);
        $finalEndpoint = $query ? "$endpoint?$query" : $endpoint;
        $timestamp = time();
        $signature = $this->generateSignature('GET', $endpoint, null, $timestamp);
        return $this->http->request('GET', $finalEndpoint, [
            'headers' => [
                'X-Api-Key' => $this->apiKey,
                'X-Signature' => $signature,
                'X-Timestamp' => $timestamp,
            ]
        ]);
    }

    /**
     * Generate HMAC signature
     */
    protected function generateSignature(string $method, string $path, ?string $body, int $timestamp): string
    {
        $baseString = $method . "\n" . $path . "\n" . ($body ?? '') . "\n" . $timestamp;
        return hash_hmac('sha256', $baseString, $this->apiSecret);
    }
}