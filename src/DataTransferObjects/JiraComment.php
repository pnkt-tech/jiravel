<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

use DateTimeImmutable;

final readonly class JiraComment
{
    public function __construct(
        public string $id,
        public JiraUser $author,
        public JiraUser $updateAuthor,
        public string $body,
        public DateTimeImmutable $created,
        public DateTimeImmutable $updated,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            author: JiraUser::fromArray($data['author'] ?? []),
            updateAuthor: JiraUser::fromArray($data['updateAuthor'] ?? []),
            body: self::extractCommentBody($data['body'] ?? ''),
            created: new DateTimeImmutable($data['created'] ?? 'now'),
            updated: new DateTimeImmutable($data['updated'] ?? 'now'),
        );
    }

    private static function extractCommentBody(mixed $body): string
    {
        if (is_string($body)) {
            return $body;
        }

        if (is_array($body) && isset($body['content'])) {
            return self::extractTextFromContent($body['content']);
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
            'id' => $this->id,
            'author' => $this->author->toArray(),
            'updateAuthor' => $this->updateAuthor->toArray(),
            'body' => $this->body,
            'created' => $this->created->format('Y-m-d H:i:s'),
            'updated' => $this->updated->format('Y-m-d H:i:s'),
        ];
    }
}
