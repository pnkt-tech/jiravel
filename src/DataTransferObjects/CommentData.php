<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

use Pnkt\Jiravel\Exceptions\JiraValidationException;

final readonly class CommentData
{
    public function __construct(
        public string $body,
        public ?string $author = null,
        public ?string $created = null,
        public ?string $updated = null,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty(trim($this->body))) {
            throw new JiraValidationException('Comment body cannot be empty');
        }

        if (strlen($this->body) > 32767) {
            throw new JiraValidationException('Comment body cannot exceed 32767 characters');
        }

        if ($this->author !== null && empty(trim($this->author))) {
            throw new JiraValidationException('Author cannot be empty if provided');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            body: $data['body'] ?? '',
            author: $data['author'] ?? null,
            created: $data['created'] ?? null,
            updated: $data['updated'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'body' => $this->body,
            'author' => $this->author,
            'created' => $this->created,
            'updated' => $this->updated,
        ];
    }
}
