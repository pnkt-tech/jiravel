<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class JiraStatusCategory
{
    public function __construct(
        public int $id,
        public string $key,
        public string $name,
        public string $colorName,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            key: $data['key'] ?? '',
            name: $data['name'] ?? '',
            colorName: $data['colorName'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'name' => $this->name,
            'colorName' => $this->colorName,
        ];
    }
}
