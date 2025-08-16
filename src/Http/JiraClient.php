<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Pnkt\Jiravel\Contracts\JiraClientInterface;
use Pnkt\Jiravel\Contracts\RequestInterface;
use Pnkt\Jiravel\Contracts\JiraResponseInterface;
use Pnkt\Jiravel\Exceptions\JiraException;
use Pnkt\Jiravel\Exceptions\JiraRateLimitException;
use Pnkt\Jiravel\Exceptions\JiraAuthenticationException;
use Pnkt\Jiravel\Http\Requests\JiraRequest;
use Pnkt\Jiravel\Http\Responses\JiraResponse;

final readonly class JiraClient implements JiraClientInterface
{
    private Client $client;
    private const RATE_LIMIT_KEY = 'jiravel_rate_limit';
    private const MAX_REQUESTS_PER_MINUTE = 1000; // Jira's default limit

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $username,
        private readonly string $apiToken,
        private readonly int $timeout = 30,
        private readonly int $retryAttempts = 3,
        private readonly int $retryDelay = 1
    ) {
        $this->validateConfiguration();
        $this->client = new Client([
            'base_uri' => rtrim($this->baseUrl, '/'),
            'timeout' => $this->timeout,
            'auth' => [$this->username, $this->apiToken],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    private function validateConfiguration(): void
    {
        if (empty(trim($this->baseUrl))) {
            throw new JiraException('Jira base URL cannot be empty');
        }

        if (empty(trim($this->username))) {
            throw new JiraException('Jira username cannot be empty');
        }

        if (empty(trim($this->apiToken))) {
            throw new JiraException('Jira API token cannot be empty');
        }

        if (!filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            throw new JiraException('Invalid Jira base URL format');
        }
    }

    public function send(RequestInterface $request): JiraResponse
    {
        $this->checkRateLimit();
        $this->logRequest($request);

        $cacheKey = $request->getCacheKey();
        
        if ($request->shouldCache() && config('jiravel.cache.enabled', true)) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                $this->logCacheHit($request);
                return $cached;
            }
        }

        try {
            $response = $this->executeRequest($request);
            
            if ($request->shouldCache() && config('jiravel.cache.enabled', true)) {
                Cache::put($cacheKey, $response, config('jiravel.cache.ttl', 3600));
                $this->logCacheStore($request);
            }

            $this->logResponse($request, $response);
            return $response;

        } catch (ClientException $e) {
            $this->handleClientException($e, $request);
        } catch (ServerException $e) {
            $this->handleServerException($e, $request);
        } catch (GuzzleException $e) {
            $this->logError($request, $e->getMessage());
            throw new JiraException("Jira API request failed: {$e->getMessage()}", 0, $e);
        }
    }

    private function checkRateLimit(): void
    {
        $currentMinute = (int) (time() / 60);
        $rateLimitKey = self::RATE_LIMIT_KEY . ':' . $currentMinute;
        
        $requestCount = Cache::get($rateLimitKey, 0);
        
        if ($requestCount >= self::MAX_REQUESTS_PER_MINUTE) {
            $retryAfter = 60 - (time() % 60);
            throw new JiraRateLimitException(
                'Rate limit exceeded. Maximum ' . self::MAX_REQUESTS_PER_MINUTE . ' requests per minute.',
                429,
                null,
                $retryAfter
            );
        }
        
        Cache::put($rateLimitKey, $requestCount + 1, 60);
    }

    private function handleClientException(ClientException $e, RequestInterface $request): void
    {
        $statusCode = $e->getResponse()->getStatusCode();
        $message = $e->getMessage();

        switch ($statusCode) {
            case 401:
                throw new JiraAuthenticationException(
                    'Invalid Jira credentials. Please check your username and API token.',
                    401,
                    $e
                );
            case 403:
                throw new JiraException(
                    'Access denied. You do not have permission to perform this action.',
                    403,
                    $e
                );
            case 404:
                throw new JiraException(
                    'Resource not found. The requested Jira resource does not exist.',
                    404,
                    $e
                );
            case 429:
                $retryAfter = $e->getResponse()->getHeaderLine('Retry-After');
                throw new JiraRateLimitException(
                    'Rate limit exceeded. Please wait before making more requests.',
                    429,
                    $e,
                    $retryAfter ? (int) $retryAfter : null
                );
            default:
                $this->logError($request, $message);
                throw new JiraException("Jira API client error: {$message}", $statusCode, $e);
        }
    }

    private function handleServerException(ServerException $e, RequestInterface $request): void
    {
        $statusCode = $e->getResponse()->getStatusCode();
        $message = $e->getMessage();

        $this->logError($request, "Jira server error ({$statusCode}): {$message}");
        
        throw new JiraException(
            "Jira server error. Please try again later. Status: {$statusCode}",
            $statusCode,
            $e
        );
    }

    public function get(string $endpoint, array $params = []): JiraResponse
    {
        $request = new class($endpoint, $params) extends JiraRequest {
            public function __construct(string $endpoint, array $params)
            {
                parent::__construct('GET', $endpoint, $params);
            }
        };

        return $this->send($request);
    }

    public function post(string $endpoint, array $data = []): JiraResponse
    {
        $request = new class($endpoint, $data) extends JiraRequest {
            public function __construct(string $endpoint, array $data)
            {
                parent::__construct('POST', $endpoint, [], $data);
            }
        };

        return $this->send($request);
    }

    public function put(string $endpoint, array $data = []): JiraResponse
    {
        $request = new class($endpoint, $data) extends JiraRequest {
            public function __construct(string $endpoint, array $data)
            {
                parent::__construct('PUT', $endpoint, [], $data);
            }
        };

        return $this->send($request);
    }

    public function delete(string $endpoint): JiraResponse
    {
        $request = new class($endpoint) extends JiraRequest {
            public function __construct(string $endpoint)
            {
                parent::__construct('DELETE', $endpoint);
            }
        };

        return $this->send($request);
    }

    public function upload(string $endpoint, string $filePath, string $filename): JiraResponse
    {
        $this->logUploadRequest($endpoint, $filename);

        try {
            $response = $this->client->post($endpoint, [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => $filename,
                    ],
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $jiraResponse = new class($response->getStatusCode(), $data, $response->getHeaders()) extends JiraResponse {};

            $this->logUploadResponse($endpoint, $filename, $jiraResponse);
            return $jiraResponse;

        } catch (GuzzleException $e) {
            $this->logUploadError($endpoint, $filename, $e->getMessage());
            throw new JiraException("File upload failed: {$e->getMessage()}", 0, $e);
        }
    }

    private function executeRequest(RequestInterface $request): JiraResponse
    {
        $options = [
            'query' => $request->getParams(),
        ];

        if (!empty($request->getData())) {
            $options['json'] = $request->getData();
        }

        $response = $this->client->request(
            $request->getMethod(),
            $request->getEndpoint(),
            $options
        );

        return new JiraResponse(
            $response->getStatusCode(),
            json_decode($response->getBody()->getContents(), true) ?? [],
            $response->getHeaders()
        );
    }

    private function logRequest(RequestInterface $request): void
    {
        Log::channel('jiravel')->info('Jira API Request', [
            'method' => $request->getMethod(),
            'endpoint' => $request->getEndpoint(),
            'params' => $request->getParams(),
            'data' => $request->getData(),
        ]);
    }

    private function logResponse(RequestInterface $request, JiraResponse $response): void
    {
        Log::channel('jiravel')->info('Jira API Response', [
            'method' => $request->getMethod(),
            'endpoint' => $request->getEndpoint(),
            'status_code' => $response->getStatusCode(),
            'success' => $response->isSuccessful(),
        ]);
    }

    private function logError(RequestInterface $request, string $error): void
    {
        Log::channel('jiravel')->error('Jira API Error', [
            'method' => $request->getMethod(),
            'endpoint' => $request->getEndpoint(),
            'error' => $error,
        ]);
    }

    private function logCacheHit(RequestInterface $request): void
    {
        Log::channel('jiravel')->info('Jira API Cache Hit', [
            'method' => $request->getMethod(),
            'endpoint' => $request->getEndpoint(),
        ]);
    }

    private function logCacheStore(RequestInterface $request): void
    {
        Log::channel('jiravel')->info('Jira API Cache Store', [
            'method' => $request->getMethod(),
            'endpoint' => $request->getEndpoint(),
        ]);
    }

    private function logUploadRequest(string $endpoint, string $filename): void
    {
        if (!config('jiravel.logging.enabled', true)) {
            return;
        }

        Log::channel('jiravel')->info('Jira API Upload Request', [
            'endpoint' => $endpoint,
            'filename' => $filename,
        ]);
    }

    private function logUploadResponse(string $endpoint, string $filename, JiraResponse $response): void
    {
        if (!config('jiravel.logging.enabled', true)) {
            return;
        }

        Log::channel('jiravel')->info('Jira API Upload Response', [
            'endpoint' => $endpoint,
            'filename' => $filename,
            'status_code' => $response->getStatusCode(),
            'successful' => $response->isSuccessful(),
        ]);
    }

    private function logUploadError(string $endpoint, string $filename, string $error): void
    {
        if (!config('jiravel.logging.enabled', true)) {
            return;
        }

        Log::channel('jiravel')->error('Jira API Upload Error', [
            'endpoint' => $endpoint,
            'filename' => $filename,
            'error' => $error,
        ]);
    }
}
