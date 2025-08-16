<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class TicketPriority
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $iconUrl
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            iconUrl: $data['iconUrl'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'iconUrl' => $this->iconUrl,
        ];
    }

    public function isHighest(): bool
    {
        return $this->name === 'Highest';
    }

    public function isHigh(): bool
    {
        return $this->name === 'High';
    }

    public function isMedium(): bool
    {
        return $this->name === 'Medium';
    }

    public function isLow(): bool
    {
        return $this->name === 'Low';
    }

    public function isLowest(): bool
    {
        return $this->name === 'Lowest';
    }

    public function isUrgent(): bool
    {
        return in_array($this->name, ['Highest', 'High']);
    }
}
