<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

use Pnkt\Jiravel\Exceptions\JiraValidationException;

final readonly class TicketData
{
    public function __construct(
        public string $summary,
        public string $description,
        public string $issueType,
        public ?string $priority = null,
        public ?string $assignee = null,
        public ?string $reporter = null,
        public ?array $labels = null,
        public ?array $components = null,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty(trim($this->summary))) {
            throw new JiraValidationException('Summary cannot be empty');
        }

        if (empty(trim($this->issueType))) {
            throw new JiraValidationException('Issue type cannot be empty');
        }

        if (strlen($this->summary) > 255) {
            throw new JiraValidationException('Summary cannot exceed 255 characters');
        }

        if ($this->assignee !== null && empty(trim($this->assignee))) {
            throw new JiraValidationException('Assignee cannot be empty if provided');
        }

        if ($this->reporter !== null && empty(trim($this->reporter))) {
            throw new JiraValidationException('Reporter cannot be empty if provided');
        }

        if ($this->labels !== null && !is_array($this->labels)) {
            throw new JiraValidationException('Labels must be an array');
        }

        if ($this->components !== null && !is_array($this->components)) {
            throw new JiraValidationException('Components must be an array');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            summary: $data['summary'] ?? '',
            description: $data['description'] ?? '',
            issueType: $data['issue_type'] ?? '',
            priority: $data['priority'] ?? null,
            assignee: $data['assignee'] ?? null,
            reporter: $data['reporter'] ?? null,
            labels: $data['labels'] ?? null,
            components: $data['components'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'summary' => $this->summary,
            'description' => $this->description,
            'issue_type' => $this->issueType,
            'priority' => $this->priority,
            'assignee' => $this->assignee,
            'reporter' => $this->reporter,
            'labels' => $this->labels,
            'components' => $this->components,
        ];
    }
}
