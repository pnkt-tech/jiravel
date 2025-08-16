<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

use Pnkt\Jiravel\Exceptions\JiraValidationException;

final readonly class AssigneeData
{
    public function __construct(
        public string $assignee,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty(trim($this->assignee))) {
            throw new JiraValidationException('Assignee cannot be empty');
        }

        if (strlen($this->assignee) > 255) {
            throw new JiraValidationException('Assignee cannot exceed 255 characters');
        }

        // Validate email format if it looks like an email
        if (str_contains($this->assignee, '@') && !filter_var($this->assignee, FILTER_VALIDATE_EMAIL)) {
            throw new JiraValidationException('Assignee must be a valid email address or username');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            assignee: $data['assignee'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'assignee' => $this->assignee,
        ];
    }
}
