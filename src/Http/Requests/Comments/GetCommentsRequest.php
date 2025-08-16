<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Requests\Comments;

use Pnkt\Jiravel\Http\Requests\JiraRequest;

final readonly class GetCommentsRequest extends JiraRequest
{
    public function __construct(
        string $ticketNumber,
        int $maxResults = 50,
        int $startAt = 0
    ) {
        parent::__construct(
            method: 'GET',
            endpoint: "/rest/api/3/issue/{$ticketNumber}/comment",
            params: [
                'maxResults' => $maxResults,
                'startAt' => $startAt,
            ],
            data: []
        );
    }

    public function getTicketNumber(): string
    {
        return $this->extractTicketNumberFromEndpoint();
    }

    public function getMaxResults(): int
    {
        return $this->params['maxResults'] ?? 50;
    }

    public function getStartAt(): int
    {
        return $this->params['startAt'] ?? 0;
    }

    private function extractTicketNumberFromEndpoint(): string
    {
        $parts = explode('/', $this->endpoint);
        return $parts[count($parts) - 2]; // Get the ticket number before 'comment'
    }
}
