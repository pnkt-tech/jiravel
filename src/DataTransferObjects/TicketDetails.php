<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

use Pnkt\Jiravel\DataTransferObjects\TicketStatus;
use Pnkt\Jiravel\DataTransferObjects\TicketAssignee;
use Pnkt\Jiravel\DataTransferObjects\TicketReporter;
use Pnkt\Jiravel\DataTransferObjects\TicketIssueType;
use Pnkt\Jiravel\DataTransferObjects\TicketPriority;
use Pnkt\Jiravel\DataTransferObjects\TicketComponent;

final readonly class TicketDetails
{
    public function __construct(
        public readonly string $key,
        public readonly string $id,
        public readonly string $summary,
        public readonly string $description,
        public readonly TicketStatus $status,
        public readonly ?TicketAssignee $assignee,
        public readonly TicketReporter $reporter,
        public readonly TicketIssueType $issueType,
        public readonly TicketPriority $priority,
        public readonly array $labels,
        public readonly array $components,
        public readonly string $createdDate,
        public readonly string $updatedDate,
        public readonly ?string $resolutionDate = null,
        public readonly ?string $dueDate = null,
        public readonly array $attachments = [],
        public readonly array $comments = [],
        public readonly array $worklog = []
    ) {}

    public function getNumber(): string
    {
        return $this->key;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            key: $data['key'] ?? '',
            id: $data['id'] ?? '',
            summary: $data['fields']['summary'] ?? '',
            description: self::extractDescription($data['fields']['description'] ?? null),
            status: TicketStatus::fromArray($data['fields']['status'] ?? []),
            assignee: isset($data['fields']['assignee']) 
                ? TicketAssignee::fromArray($data['fields']['assignee']) 
                : null,
            reporter: TicketReporter::fromArray($data['fields']['reporter'] ?? []),
            issueType: TicketIssueType::fromArray($data['fields']['issuetype'] ?? []),
            priority: TicketPriority::fromArray($data['fields']['priority'] ?? []),
            labels: $data['fields']['labels'] ?? [],
            components: array_map(
                fn(array $component) => TicketComponent::fromArray($component),
                $data['fields']['components'] ?? []
            ),
            createdDate: $data['fields']['created'] ?? '',
            updatedDate: $data['fields']['updated'] ?? '',
            resolutionDate: $data['fields']['resolutiondate'] ?? null,
            dueDate: $data['fields']['duedate'] ?? null,
            attachments: $data['fields']['attachment'] ?? [],
            comments: $data['fields']['comment'] ?? [],
            worklog: $data['fields']['worklog'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'id' => $this->id,
            'summary' => $this->summary,
            'description' => $this->description,
            'status' => $this->status->toArray(),
            'assignee' => $this->assignee?->toArray(),
            'reporter' => $this->reporter->toArray(),
            'issueType' => $this->issueType->toArray(),
            'priority' => $this->priority->toArray(),
            'labels' => $this->labels,
            'components' => array_map(fn(TicketComponent $component) => $component->toArray(), $this->components),
            'createdDate' => $this->createdDate,
            'updatedDate' => $this->updatedDate,
            'resolutionDate' => $this->resolutionDate,
            'dueDate' => $this->dueDate,
            'attachments' => $this->attachments,
            'comments' => $this->comments,
            'worklog' => $this->worklog,
        ];
    }

    private static function extractDescription(?array $description): string
    {
        if ($description === null) {
            return '';
        }

        if (isset($description['content'])) {
            return self::extractTextFromContent($description['content']);
        }

        return (string) $description;
    }

    private static function extractTextFromContent(array $content): string
    {
        $text = '';
        
        foreach ($content as $block) {
            if (isset($block['content'])) {
                foreach ($block['content'] as $item) {
                    if (isset($item['text'])) {
                        $text .= $item['text'];
                    }
                }
            }
        }
        
        return $text;
    }

    public function isResolved(): bool
    {
        return in_array($this->status->name, ['Done', 'Resolved', 'Closed']);
    }

    public function isAssigned(): bool
    {
        return $this->assignee !== null;
    }

    public function hasLabels(): bool
    {
        return !empty($this->labels);
    }

    public function hasComponents(): bool
    {
        return !empty($this->components);
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    public function hasComments(): bool
    {
        return !empty($this->comments);
    }

    public function hasWorklog(): bool
    {
        return !empty($this->worklog);
    }
}
