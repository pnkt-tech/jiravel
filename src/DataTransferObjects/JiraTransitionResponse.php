<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class JiraTransitionResponse
{
    public function __construct(
        public array $transitions,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            transitions: array_map(fn($transition) => JiraTransition::fromArray($transition), $data['transitions'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'transitions' => array_map(fn($transition) => $transition->toArray(), $this->transitions),
        ];
    }
}
