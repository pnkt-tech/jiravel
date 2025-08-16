<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class JiraComponent
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public ?JiraUser $lead,
        public bool $assigneeType,
        public string $assigneeTypeName,
        public bool $realAssigneeType,
        public string $realAssigneeTypeName,
        public bool $isAssigneeTypeValid,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            lead: isset($data['lead']) ? JiraUser::fromArray($data['lead']) : null,
            assigneeType: $data['assigneeType'] ?? false,
            assigneeTypeName: $data['assigneeTypeName'] ?? '',
            realAssigneeType: $data['realAssigneeType'] ?? false,
            realAssigneeTypeName: $data['realAssigneeTypeName'] ?? '',
            isAssigneeTypeValid: $data['isAssigneeTypeValid'] ?? false,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'lead' => $this->lead?->toArray(),
            'assigneeType' => $this->assigneeType,
            'assigneeTypeName' => $this->assigneeTypeName,
            'realAssigneeType' => $this->realAssigneeType,
            'realAssigneeTypeName' => $this->realAssigneeTypeName,
            'isAssigneeTypeValid' => $this->isAssigneeTypeValid,
        ];
    }
}
