<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class UserActivityData
{
    public function __construct(
        public string $username,
        public string $period = 'monthly',
        public ?string $startDate = null,
        public ?string $endDate = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'],
            period: $data['period'] ?? 'monthly',
            startDate: $data['start_date'] ?? null,
            endDate: $data['end_date'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'period' => $this->period,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ];
    }
}
