<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Responses;

use Pnkt\Jiravel\Contracts\JiraResponseInterface;

abstract readonly class JiraResponse implements JiraResponseInterface
{
    public function __construct(
        protected readonly int $statusCode,
        protected readonly array $data,
        protected readonly array $headers = []
    ) {}

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function hasData(): bool
    {
        return !empty($this->data);
    }
}
