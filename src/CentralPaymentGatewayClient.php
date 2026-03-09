<?php

namespace Satyam147\CentralPaymentGateway;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

class CentralPaymentGatewayClient
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
    /**
     * Initiate a payment.
     * POST /api/v1/payments
     * @param array $payload
     * @return ResponseInterface
     */
    public function initiatePayment(array $payload): ResponseInterface
    {
        $method = 'POST';
        $endpoint = '/api/v1/payments';
        $body = json_encode($payload);
        $timestamp = (string)time();
        $signature = $this->generateSignature(
            $this->apiKey,
            $timestamp,
            $method,
            $endpoint,
            $body
        );

        return $this->http->request($method, $endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->apiKey,
                'X-Signature' => $signature,
                'X-Timestamp' => $timestamp,
            ],
            'body' => $body,
        ]);
    }

    public function getPaymentStatus(string $transactionId): ResponseInterface
    {
        $method = 'GET';
        $endpoint = "/api/v1/payments/{$transactionId}";
        $body = '';
        $timestamp = (string)time();
        $signature = $this->generateSignature(
            $this->apiKey,
            $timestamp,
            $method,
            $endpoint,
            $body
        );
        return $this->http->request($method, $endpoint, [
            'headers' => [
                'X-Api-Key' => $this->apiKey,
                'X-Signature' => $signature,
                'X-Timestamp' => $timestamp,
            ]
        ]);
    }

    public function checkPaymentStatus(string $transactionId): ResponseInterface
    {
        $method = 'POST';
        $endpoint = "/api/v1/payments/{$transactionId}/check-status";
        $body = '';
        $timestamp = (string)time();
        $signature = $this->generateSignature(
            $this->apiKey,
            $timestamp,
            $method,
            $endpoint,
            $body
        );
        return $this->http->request($method, $endpoint, [
            'headers' => [
                'X-Api-Key' => $this->apiKey,
                'X-Signature' => $signature,
                'X-Timestamp' => $timestamp,
            ]
        ]);
    }
    
    public function getRefundSummary(string $transactionId): ResponseInterface
    {
        $method = 'GET';
        $endpoint = "/api/v1/payments/{$transactionId}/refunds/summary";
        $body = '';
        $timestamp = (string)time();
        $signature = $this->generateSignature(
            $this->apiKey,
            $timestamp,
            $method,
            $endpoint,
            $body
        );
        return $this->http->request($method, $endpoint, [
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
    protected function generateSignature(
        string  $apiKey,
        string  $timestamp,
        string  $method,
        string  $path,
        ?string $body
    ): string
    {
        $stringToSign = $apiKey . $timestamp . strtoupper($method) . $path . ($body ?? '');
        return hash_hmac('sha256', $stringToSign, $this->apiSecret);
    }
}
