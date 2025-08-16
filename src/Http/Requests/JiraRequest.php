<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Requests;

use Pnkt\Jiravel\Contracts\RequestInterface;

abstract readonly class JiraRequest implements RequestInterface
{
    public function __construct(
        protected readonly string $method,
        protected readonly string $endpoint,
        protected readonly array $params = [],
        protected readonly array $data = []
    ) {}

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getCacheKey(): string
    {
        return 'jiravel_' . md5($this->method . $this->endpoint . serialize($this->params) . serialize($this->data));
    }

    public function shouldCache(): bool
    {
        return $this->method === 'GET';
    }
}
