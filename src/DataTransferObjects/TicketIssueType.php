<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class TicketIssueType
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $iconUrl,
        public readonly bool $subtask
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            iconUrl: $data['iconUrl'] ?? '',
            subtask: $data['subtask'] ?? false
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

    public function isStory(): bool
    {
        return $this->name === 'Story';
    }

    public function isBug(): bool
    {
        return $this->name === 'Bug';
    }

    public function isTask(): bool
    {
        return $this->name === 'Task';
    }

    public function isEpic(): bool
    {
        return $this->name === 'Epic';
    }

    public function isSubtask(): bool
    {
        return $this->subtask;
    }
}
