<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Responses;

use Pnkt\Jiravel\DataTransferObjects\TicketDetails;

final readonly class SearchResponse extends JiraResponse
{
    public function getTotal(): int
    {
        return $this->data['total'] ?? 0;
    }

    public function getMaxResults(): int
    {
        return $this->data['maxResults'] ?? 0;
    }

    public function getStartAt(): int
    {
        return $this->data['startAt'] ?? 0;
    }

    public function getIssues(): array
    {
        return $this->data['issues'] ?? [];
    }

    public function getTicketDetails(): array
    {
        return array_map(
            fn(array $issue) => TicketDetails::fromArray($issue),
            $this->getIssues()
        );
    }

    public function hasMoreResults(): bool
    {
        return ($this->getStartAt() + $this->getMaxResults()) < $this->getTotal();
    }

    public function getNextStartAt(): int
    {
        return $this->getStartAt() + $this->getMaxResults();
    }
}
