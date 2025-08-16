<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Contracts;

interface JiraResponseInterface
{
    public function getStatusCode(): int;
    public function getData(): array;
    public function getHeaders(): array;
    public function isSuccessful(): bool;
    public function hasData(): bool;
}
