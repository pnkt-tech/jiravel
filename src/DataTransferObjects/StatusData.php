<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

use Pnkt\Jiravel\Exceptions\JiraValidationException;

final readonly class StatusData
{
    public function __construct(
        public string $status,
        public ?string $comment = null,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty(trim($this->status))) {
            throw new JiraValidationException('Status cannot be empty');
        }

        if (strlen($this->status) > 100) {
            throw new JiraValidationException('Status cannot exceed 100 characters');
        }

        if ($this->comment !== null && strlen($this->comment) > 32767) {
            throw new JiraValidationException('Status comment cannot exceed 32767 characters');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'] ?? '',
            comment: $data['comment'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'comment' => $this->comment,
        ];
    }
}
