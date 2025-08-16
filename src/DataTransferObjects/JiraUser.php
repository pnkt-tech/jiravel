<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class JiraUser
{
    public function __construct(
        public string $accountId,
        public string $name,
        public string $displayName,
        public string $emailAddress,
        public bool $active,
        public string $timeZone,
        public string $accountType,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            accountId: $data['accountId'] ?? '',
            name: $data['name'] ?? '',
            displayName: $data['displayName'] ?? '',
            emailAddress: $data['emailAddress'] ?? '',
            active: $data['active'] ?? false,
            timeZone: $data['timeZone'] ?? '',
            accountType: $data['accountType'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'accountId' => $this->accountId,
            'name' => $this->name,
            'displayName' => $this->displayName,
            'emailAddress' => $this->emailAddress,
            'active' => $this->active,
            'timeZone' => $this->timeZone,
            'accountType' => $this->accountType,
        ];
    }
}
