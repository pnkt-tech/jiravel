<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

use DateTimeImmutable;

final readonly class JiraWorklog
{
    public function __construct(
        public int $startAt,
        public int $maxResults,
        public int $total,
        public array $worklogs,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            startAt: $data['startAt'] ?? 0,
            maxResults: $data['maxResults'] ?? 0,
            total: $data['total'] ?? 0,
            worklogs: array_map(fn($worklog) => JiraWorklogEntry::fromArray($worklog), $data['worklogs'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'startAt' => $this->startAt,
            'maxResults' => $this->maxResults,
            'total' => $this->total,
            'worklogs' => array_map(fn($worklog) => $worklog->toArray(), $this->worklogs),
        ];
    }
}

final readonly class JiraWorklogEntry
{
    public function __construct(
        public string $id,
        public JiraUser $author,
        public JiraUser $updateAuthor,
        public string $comment,
        public DateTimeImmutable $started,
        public DateTimeImmutable $updated,
        public int $timeSpentSeconds,
        public string $timeSpent,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            author: JiraUser::fromArray($data['author'] ?? []),
            updateAuthor: JiraUser::fromArray($data['updateAuthor'] ?? []),
            comment: $data['comment'] ?? '',
            started: new DateTimeImmutable($data['started'] ?? 'now'),
            updated: new DateTimeImmutable($data['updated'] ?? 'now'),
            timeSpentSeconds: $data['timeSpentSeconds'] ?? 0,
            timeSpent: $data['timeSpent'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'author' => $this->author->toArray(),
            'updateAuthor' => $this->updateAuthor->toArray(),
            'comment' => $this->comment,
            'started' => $this->started->format('Y-m-d H:i:s'),
            'updated' => $this->updated->format('Y-m-d H:i:s'),
            'timeSpentSeconds' => $this->timeSpentSeconds,
            'timeSpent' => $this->timeSpent,
        ];
    }
}
