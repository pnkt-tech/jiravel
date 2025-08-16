<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Contracts\Services;

use Pnkt\Jiravel\DataTransferObjects\StatusData;

interface StatusServiceInterface
{
    public function changeStatus(string $ticketNumber, StatusData $statusData): void;
    public function getAvailableTransitions(string $ticketNumber): array;
}
