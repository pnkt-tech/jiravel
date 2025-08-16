<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

use DateTimeImmutable;

final readonly class JiraTicket
{
    public function __construct(
        public string $key,
        public string $id,
        public string $summary,
        public string $description,
        public JiraStatus $status,
        public JiraIssueType $issueType,
        public ?JiraPriority $priority,
        public ?JiraUser $assignee,
        public JiraUser $reporter,
        public DateTimeImmutable $created,
        public DateTimeImmutable $updated,
        public array $labels,
        public array $components,
        public ?JiraWorklog $worklog = null,
        public array $attachments = [],
        public array $comments = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            key: $data['key'],
            id: $data['id'],
            summary: $data['fields']['summary'] ?? '',
            description: self::extractDescription($data['fields']['description'] ?? ''),
            status: JiraStatus::fromArray($data['fields']['status'] ?? []),
            issueType: JiraIssueType::fromArray($data['fields']['issuetype'] ?? []),
            priority: isset($data['fields']['priority']) ? JiraPriority::fromArray($data['fields']['priority']) : null,
            assignee: isset($data['fields']['assignee']) ? JiraUser::fromArray($data['fields']['assignee']) : null,
            reporter: JiraUser::fromArray($data['fields']['reporter'] ?? []),
            created: new DateTimeImmutable($data['fields']['created'] ?? 'now'),
            updated: new DateTimeImmutable($data['fields']['updated'] ?? 'now'),
            labels: $data['fields']['labels'] ?? [],
            components: array_map(fn($component) => JiraComponent::fromArray($component), $data['fields']['components'] ?? []),
            worklog: isset($data['fields']['worklog']) ? JiraWorklog::fromArray($data['fields']['worklog']) : null,
            attachments: array_map(fn($attachment) => JiraAttachment::fromArray($attachment), $data['fields']['attachment'] ?? []),
            comments: array_map(fn($comment) => JiraComment::fromArray($comment), $data['fields']['comment']['comments'] ?? []),
        );
    }

    private static function extractDescription(mixed $description): string
    {
        if (is_string($description)) {
            return $description;
        }

        if (is_array($description) && isset($description['content'])) {
            return self::extractTextFromContent($description['content']);
        }

        return '';
    }

    private static function extractTextFromContent(array $content): string
    {
        $text = '';
        foreach ($content as $item) {
            if (isset($item['type']) && $item['type'] === 'paragraph' && isset($item['content'])) {
                foreach ($item['content'] as $textItem) {
                    if (isset($textItem['type']) && $textItem['type'] === 'text' && isset($textItem['text'])) {
                        $text .= $textItem['text'];
                    }
                }
            }
        }
        return $text;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'id' => $this->id,
            'summary' => $this->summary,
            'description' => $this->description,
            'status' => $this->status->toArray(),
            'issueType' => $this->issueType->toArray(),
            'priority' => $this->priority?->toArray(),
            'assignee' => $this->assignee?->toArray(),
            'reporter' => $this->reporter->toArray(),
            'created' => $this->created->format('Y-m-d H:i:s'),
            'updated' => $this->updated->format('Y-m-d H:i:s'),
            'labels' => $this->labels,
            'components' => array_map(fn($component) => $component->toArray(), $this->components),
            'worklog' => $this->worklog?->toArray(),
            'attachments' => array_map(fn($attachment) => $attachment->toArray(), $this->attachments),
            'comments' => array_map(fn($comment) => $comment->toArray(), $this->comments),
        ];
    }
}
