<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class JiraTransition
{
    public function __construct(
        public string $id,
        public string $name,
        public JiraStatus $to,
        public bool $hasScreen,
        public bool $isGlobal,
        public bool $isInitial,
        public bool $isConditional,
        public array $fields,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            name: $data['name'] ?? '',
            to: JiraStatus::fromArray($data['to'] ?? []),
            hasScreen: $data['hasScreen'] ?? false,
            isGlobal: $data['isGlobal'] ?? false,
            isInitial: $data['isInitial'] ?? false,
            isConditional: $data['isConditional'] ?? false,
            fields: $data['fields'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'to' => $this->to->toArray(),
            'hasScreen' => $this->hasScreen,
            'isGlobal' => $this->isGlobal,
            'isInitial' => $this->isInitial,
            'isConditional' => $this->isConditional,
            'fields' => $this->fields,
        ];
    }
}
