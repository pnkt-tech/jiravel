<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class TicketStatus
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $iconUrl,
        public readonly string $statusCategory
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            iconUrl: $data['iconUrl'] ?? '',
            statusCategory: $data['statusCategory']['name'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'iconUrl' => $this->iconUrl,
            'statusCategory' => $this->statusCategory,
        ];
    }

    public function isResolved(): bool
    {
        return in_array($this->name, ['Done', 'Resolved', 'Closed']);
    }

    public function isInProgress(): bool
    {
        return in_array($this->name, ['In Progress', 'In Development']);
    }

    public function isOpen(): bool
    {
        return in_array($this->name, ['Open', 'To Do', 'Backlog']);
    }
}
