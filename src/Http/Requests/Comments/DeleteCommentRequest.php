<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Requests\Comments;

use Pnkt\Jiravel\Http\Requests\JiraRequest;

final readonly class DeleteCommentRequest extends JiraRequest
{
    public function __construct(
        string $ticketNumber,
        string $commentId
    ) {
        parent::__construct(
            method: 'DELETE',
            endpoint: "/rest/api/3/issue/{$ticketNumber}/comment/{$commentId}",
            params: [],
            data: []
        );
    }

    public function getTicketNumber(): string
    {
        return $this->extractTicketNumberFromEndpoint();
    }

    public function getCommentId(): string
    {
        return $this->extractCommentIdFromEndpoint();
    }

    private function extractTicketNumberFromEndpoint(): string
    {
        $parts = explode('/', $this->endpoint);
        return $parts[count($parts) - 3]; // Get the ticket number before 'comment'
    }

    private function extractCommentIdFromEndpoint(): string
    {
        $parts = explode('/', $this->endpoint);
        return end($parts); // Get the comment ID
    }
}
