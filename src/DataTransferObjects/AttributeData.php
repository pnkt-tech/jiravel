<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

use Pnkt\Jiravel\Exceptions\JiraValidationException;

final readonly class AttributeData
{
    public function __construct(
        public string $attribute,
        public mixed $value,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty(trim($this->attribute))) {
            throw new JiraValidationException('Attribute name cannot be empty');
        }

        if (strlen($this->attribute) > 255) {
            throw new JiraValidationException('Attribute name cannot exceed 255 characters');
        }

        // Validate common Jira field names
        $validFields = [
            'summary', 'description', 'priority', 'assignee', 'reporter',
            'labels', 'components', 'issuetype', 'project', 'status'
        ];

        if (!in_array(strtolower($this->attribute), $validFields)) {
            throw new JiraValidationException("Invalid attribute name: {$this->attribute}");
        }

        if ($this->value === null || $this->value === '') {
            throw new JiraValidationException('Attribute value cannot be empty');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            attribute: $data['attribute'] ?? '',
            value: $data['value'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'attribute' => $this->attribute,
            'value' => $this->value,
        ];
    }
}
