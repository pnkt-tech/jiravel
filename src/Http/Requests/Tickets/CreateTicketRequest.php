<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Requests\Tickets;

use Pnkt\Jiravel\Http\Requests\JiraRequest;
use Pnkt\Jiravel\DataTransferObjects\TicketData;

final readonly class CreateTicketRequest extends JiraRequest
{
    public function __construct(
        TicketData $ticketData,
        string $projectKey
    ) {
        parent::__construct(
            method: 'POST',
            endpoint: '/rest/api/3/issue',
            params: [],
            data: $this->buildTicketData($ticketData, $projectKey)
        );
    }

    public function getTicketData(): TicketData
    {
        return $this->ticketData;
    }

    public function getProjectKey(): string
    {
        return $this->projectKey;
    }

    private function buildTicketData(TicketData $ticketData, string $projectKey): array
    {
        $data = [
            'fields' => [
                'project' => [
                    'key' => $projectKey,
                ],
                'summary' => $ticketData->summary,
                'description' => $this->buildDescription($ticketData->description),
                'issuetype' => [
                    'name' => $ticketData->issueType,
                ],
            ],
        ];

        if ($ticketData->priority !== null) {
            $data['fields']['priority'] = ['name' => $ticketData->priority];
        }

        if ($ticketData->assignee !== null) {
            $data['fields']['assignee'] = ['name' => $ticketData->assignee];
        }

        if ($ticketData->labels !== null) {
            $data['fields']['labels'] = $ticketData->labels;
        }

        if ($ticketData->components !== null) {
            $data['fields']['components'] = array_map(
                fn(string $component) => ['name' => $component],
                $ticketData->components
            );
        }

        return $data;
    }

    private function buildDescription(string $description): array
    {
        return [
            'type' => 'doc',
            'version' => 1,
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $description,
                        ],
                    ],
                ],
            ],
        ];
    }
}
