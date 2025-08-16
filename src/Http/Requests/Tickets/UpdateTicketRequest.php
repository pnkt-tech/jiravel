<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Requests\Tickets;

use Pnkt\Jiravel\Http\Requests\JiraRequest;
use Pnkt\Jiravel\DataTransferObjects\TicketData;

final readonly class UpdateTicketRequest extends JiraRequest
{
    public function __construct(
        string $ticketNumber,
        TicketData $ticketData
    ) {
        parent::__construct(
            method: 'PUT',
            endpoint: "/rest/api/3/issue/{$ticketNumber}",
            params: [],
            data: $this->buildUpdateData($ticketData)
        );
    }

    public function getTicketNumber(): string
    {
        return $this->extractTicketNumberFromEndpoint();
    }

    public function getTicketData(): TicketData
    {
        return $this->ticketData;
    }

    private function buildUpdateData(TicketData $ticketData): array
    {
        $data = [
            'fields' => [
                'summary' => $ticketData->summary,
                'description' => [
                    'type' => 'doc',
                    'version' => 1,
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $ticketData->description,
                                ],
                            ],
                        ],
                    ],
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

    private function extractTicketNumberFromEndpoint(): string
    {
        $parts = explode('/', $this->endpoint);
        return end($parts);
    }
}
