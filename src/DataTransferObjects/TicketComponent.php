<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class TicketComponent
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly bool $isAssigneeTypeValid
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            isAssigneeTypeValid: $data['isAssigneeTypeValid'] ?? false
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'isAssigneeTypeValid' => $this->isAssigneeTypeValid,
        ];
    }
}
