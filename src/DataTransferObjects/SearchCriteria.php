<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class SearchCriteria
{
    public function __construct(
        public readonly string $projectKey,
        public readonly ?string $query = null,
        public readonly ?string $assignee = null,
        public readonly ?string $label = null,
        public readonly ?string $issueType = null,
        public readonly ?string $status = null,
        public readonly ?string $priority = null,
        public readonly ?string $reporter = null,
        public readonly ?string $component = null,
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
        public readonly array $fields = []
    ) {}

    public function buildJQL(): string
    {
        $conditions = ["project = {$this->projectKey}"];

        if ($this->query !== null) {
            $conditions[] = "(summary ~ \"{$this->query}\" OR description ~ \"{$this->query}\" OR comment ~ \"{$this->query}\")";
        }

        if ($this->assignee !== null) {
            $conditions[] = "assignee = {$this->assignee}";
        }

        if ($this->label !== null) {
            $conditions[] = "labels = {$this->label}";
        }

        if ($this->issueType !== null) {
            $conditions[] = "issuetype = \"{$this->issueType}\"";
        }

        if ($this->status !== null) {
            $conditions[] = "status = \"{$this->status}\"";
        }

        if ($this->priority !== null) {
            $conditions[] = "priority = \"{$this->priority}\"";
        }

        if ($this->reporter !== null) {
            $conditions[] = "reporter = {$this->reporter}";
        }

        if ($this->component !== null) {
            $conditions[] = "component = \"{$this->component}\"";
        }

        if ($this->startDate !== null && $this->endDate !== null) {
            $conditions[] = "updated >= \"{$this->startDate}\" AND updated <= \"{$this->endDate}\"";
        }

        return implode(' AND ', $conditions) . ' ORDER BY updated DESC';
    }

    public function getFields(): array
    {
        if (empty($this->fields)) {
            return [
                'summary',
                'description',
                'status',
                'assignee',
                'reporter',
                'created',
                'updated',
                'priority',
                'issuetype',
                'labels',
                'components',
            ];
        }

        return $this->fields;
    }

    public static function createForProject(string $projectKey): self
    {
        return new self(projectKey: $projectKey);
    }

    public static function createForSearch(string $projectKey, string $query): self
    {
        return new self(projectKey: $projectKey, query: $query);
    }

    public static function createForAssignee(string $projectKey, string $assignee): self
    {
        return new self(projectKey: $projectKey, assignee: $assignee);
    }

    public static function createForLabel(string $projectKey, string $label): self
    {
        return new self(projectKey: $projectKey, label: $label);
    }

    public static function createForIssueType(string $projectKey, string $issueType): self
    {
        return new self(projectKey: $projectKey, issueType: $issueType);
    }

    public static function createForDateRange(string $projectKey, string $startDate, string $endDate): self
    {
        return new self(projectKey: $projectKey, startDate: $startDate, endDate: $endDate);
    }

    public static function createForList(
        string $projectKey,
        ?string $assignee = null,
        ?string $label = null,
        ?string $issueType = null,
        ?string $status = null,
        ?string $priority = null,
        ?string $reporter = null,
        ?string $component = null,
        array $fields = []
    ): self {
        return new self(
            projectKey: $projectKey,
            assignee: $assignee,
            label: $label,
            issueType: $issueType,
            status: $status,
            priority: $priority,
            reporter: $reporter,
            component: $component,
            fields: $fields
        );
    }

    public function toArray(): array
    {
        return [
            'projectKey' => $this->projectKey,
            'query' => $this->query,
            'assignee' => $this->assignee,
            'label' => $this->label,
            'issueType' => $this->issueType,
            'status' => $this->status,
            'priority' => $this->priority,
            'reporter' => $this->reporter,
            'component' => $this->component,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'fields' => $this->fields,
        ];
    }
}
