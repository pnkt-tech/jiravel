<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Requests\Tickets;

use Pnkt\Jiravel\Http\Requests\JiraRequest;

final readonly class DeleteTicketRequest extends JiraRequest
{
    public function __construct(string $ticketNumber)
    {
        parent::__construct(
            method: 'DELETE',
            endpoint: "/rest/api/3/issue/{$ticketNumber}",
            params: [],
            data: []
        );
    }

    public function getTicketNumber(): string
    {
        return $this->extractTicketNumberFromEndpoint();
    }

    private function extractTicketNumberFromEndpoint(): string
    {
        $parts = explode('/', $this->endpoint);
        return end($parts);
    }
}
