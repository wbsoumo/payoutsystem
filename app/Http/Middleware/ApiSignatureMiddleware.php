<?php

namespace App\Http\Middleware;

use App\Services\ApiService;
use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiSignatureMiddleware
{
    protected ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $apiKey = $request->header('x-api-key');
        $signature = $request->header('x-signature');
        $timestamp = $request->header('x-timestamp');
        $nonce = $request->header('x-nonce');
        $merchantId = $request->header('x-merchant-id');

        if (!$apiKey || !$signature || !$timestamp || !$nonce || !$merchantId) {
            $responseContent = [
                'success' => false,
                'error' => 'Missing security headers (x-api-key, x-signature, x-timestamp, x-nonce, x-merchant-id required)',
            ];
            
            $this->logApiCall($request, null, 400, $startTime, false, false, false, $responseContent);
            return response()->json($responseContent, 400);
        }

        $requestBody = $request->getContent();
        $clientIp = $request->ip() ?? '127.0.0.1';

        $validation = $this->apiService->validateRequest(
            $apiKey,
            $signature,
            $timestamp,
            $nonce,
            $requestBody,
            $clientIp,
            $merchantId
        );

        if (!$validation['status']) {
            $responseContent = [
                'success' => false,
                'error' => $validation['message'],
            ];

            // Resolve which validations passed/failed
            $tsVal = (abs(time() - (int)$timestamp) <= 300);
            // Nonce and signature validation logs
            $nonceVal = ($validation['message'] !== 'Replay attack detected (nonce reused)');
            $sigVal = ($validation['message'] !== 'Invalid signature');

            $this->logApiCall(
                $request,
                $validation['merchant_id'],
                $validation['code'],
                $startTime,
                $sigVal,
                $tsVal,
                $nonceVal,
                $responseContent
            );

            return response()->json($responseContent, $validation['code']);
        }

        // Attach merchant to request
        $request->attributes->set('merchant', $validation['merchant']);
        $request->attributes->set('merchant_id', $validation['merchant_id']);

        $response = $next($request);

        // Log successful API Call
        $executionTimeMs = (int) ((microtime(true) - $startTime) * 1000);
        $this->logApiCall(
            $request,
            $validation['merchant_id'],
            $response->getStatusCode(),
            $startTime,
            true,
            true,
            true,
            json_decode($response->getContent(), true) ?? []
        );

        return $response;
    }

    protected function logApiCall(
        Request $request,
        ?string $merchantId,
        int $statusCode,
        float $startTime,
        bool $sigResult,
        bool $tsResult,
        bool $nonceResult,
        array $responsePayload
    ): void {
        $executionTimeMs = (int) ((microtime(true) - $startTime) * 1000);

        ApiLog::create([
            'merchant_id' => $merchantId,
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'status_code' => $statusCode,
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'response' => $responsePayload,
            'execution_time_ms' => $executionTimeMs,
            'source_ip' => $request->ip() ?? '127.0.0.1',
            'user_agent' => $request->userAgent(),
            'signature_result' => $sigResult,
            'timestamp_validation' => $tsResult,
            'nonce_validation' => $nonceResult,
        ]);
    }
}
