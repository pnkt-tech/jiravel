<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class TicketReporter
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $displayName,
        public readonly string $emailAddress,
        public readonly string $username,
        public readonly string $avatarUrl,
        public readonly bool $active
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            accountId: $data['accountId'] ?? '',
            displayName: $data['displayName'] ?? '',
            emailAddress: $data['emailAddress'] ?? '',
            username: $data['name'] ?? '',
            avatarUrl: $data['avatarUrls']['48x48'] ?? '',
            active: $data['active'] ?? true
        );
    }

    public function toArray(): array
    {
        return [
            'accountId' => $this->accountId,
            'displayName' => $this->displayName,
            'emailAddress' => $this->emailAddress,
            'username' => $this->username,
            'avatarUrl' => $this->avatarUrl,
            'active' => $this->active,
        ];
    }
}
