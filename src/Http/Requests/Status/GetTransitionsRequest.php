<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Requests\Status;

use Pnkt\Jiravel\Http\Requests\JiraRequest;

final readonly class GetTransitionsRequest extends JiraRequest
{
    public function __construct(string $ticketNumber)
    {
        parent::__construct(
            method: 'GET',
            endpoint: "/rest/api/3/issue/{$ticketNumber}/transitions",
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
        return $parts[count($parts) - 2]; // Get the ticket number before 'transitions'
    }
}
