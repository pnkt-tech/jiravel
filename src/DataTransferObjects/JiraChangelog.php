<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

use DateTimeImmutable;

final readonly class JiraChangelog
{
    public function __construct(
        public int $startAt,
        public int $maxResults,
        public int $total,
        public array $histories,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            startAt: $data['startAt'] ?? 0,
            maxResults: $data['maxResults'] ?? 0,
            total: $data['total'] ?? 0,
            histories: array_map(fn($history) => JiraChangelogHistory::fromArray($history), $data['histories'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'startAt' => $this->startAt,
            'maxResults' => $this->maxResults,
            'total' => $this->total,
            'histories' => array_map(fn($history) => $history->toArray(), $this->histories),
        ];
    }
}

final readonly class JiraChangelogHistory
{
    public function __construct(
        public string $id,
        public JiraUser $author,
        public DateTimeImmutable $created,
        public array $items,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            author: JiraUser::fromArray($data['author'] ?? []),
            created: new DateTimeImmutable($data['created'] ?? 'now'),
            items: array_map(fn($item) => JiraChangelogItem::fromArray($item), $data['items'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'author' => $this->author->toArray(),
            'created' => $this->created->format('Y-m-d H:i:s'),
            'items' => array_map(fn($item) => $item->toArray(), $this->items),
        ];
    }
}

final readonly class JiraChangelogItem
{
    public function __construct(
        public string $field,
        public string $fieldType,
        public string $fieldId,
        public ?string $fromString,
        public ?string $toString,
        public ?string $from,
        public ?string $to,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            field: $data['field'] ?? '',
            fieldType: $data['fieldType'] ?? '',
            fieldId: $data['fieldId'] ?? '',
            fromString: $data['fromString'] ?? null,
            toString: $data['toString'] ?? null,
            from: $data['from'] ?? null,
            to: $data['to'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'fieldType' => $this->fieldType,
            'fieldId' => $this->fieldId,
            'fromString' => $this->fromString,
            'toString' => $this->toString,
            'from' => $this->from,
            'to' => $this->to,
        ];
    }
}
