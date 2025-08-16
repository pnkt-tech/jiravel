<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

use Pnkt\Jiravel\Exceptions\JiraValidationException;

final readonly class DescriptionData
{
    public function __construct(
        public string $description,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty(trim($this->description))) {
            throw new JiraValidationException('Description cannot be empty');
        }

        if (strlen($this->description) > 32767) {
            throw new JiraValidationException('Description cannot exceed 32767 characters');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            description: $data['description'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
        ];
    }
}
