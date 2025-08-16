<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class JiraIssueType
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $iconUrl,
        public bool $subtask,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            iconUrl: $data['iconUrl'] ?? '',
            subtask: $data['subtask'] ?? false,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'iconUrl' => $this->iconUrl,
            'subtask' => $this->subtask,
        ];
    }
}
