<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Contracts;

interface RequestInterface
{
    public function getMethod(): string;
    public function getEndpoint(): string;
    public function getParams(): array;
    public function getData(): array;
    public function getCacheKey(): string;
    public function shouldCache(): bool;
}
