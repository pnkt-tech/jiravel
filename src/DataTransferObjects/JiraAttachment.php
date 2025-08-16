<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

use DateTimeImmutable;

final readonly class JiraAttachment
{
    public function __construct(
        public string $id,
        public string $filename,
        public JiraUser $author,
        public DateTimeImmutable $created,
        public int $size,
        public string $mimeType,
        public string $content,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            filename: $data['filename'] ?? '',
            author: JiraUser::fromArray($data['author'] ?? []),
            created: new DateTimeImmutable($data['created'] ?? 'now'),
            size: $data['size'] ?? 0,
            mimeType: $data['mimeType'] ?? '',
            content: $data['content'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'author' => $this->author->toArray(),
            'created' => $this->created->format('Y-m-d H:i:s'),
            'size' => $this->size,
            'mimeType' => $this->mimeType,
            'content' => $this->content,
        ];
    }
}
