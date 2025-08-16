<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class JiraSearchResult
{
    public function __construct(
        public string $expand,
        public int $startAt,
        public int $maxResults,
        public int $total,
        public array $issues,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            expand: $data['expand'] ?? '',
            startAt: $data['startAt'] ?? 0,
            maxResults: $data['maxResults'] ?? 0,
            total: $data['total'] ?? 0,
            issues: array_map(fn($issue) => JiraTicket::fromArray($issue), $data['issues'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'expand' => $this->expand,
            'startAt' => $this->startAt,
            'maxResults' => $this->maxResults,
            'total' => $this->total,
            'issues' => array_map(fn($issue) => $issue->toArray(), $this->issues),
        ];
    }
}
