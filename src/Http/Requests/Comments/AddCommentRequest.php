<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Requests\Comments;

use Pnkt\Jiravel\Http\Requests\JiraRequest;
use Pnkt\Jiravel\DataTransferObjects\CommentData;

final readonly class AddCommentRequest extends JiraRequest
{
    public function __construct(
        string $ticketNumber,
        CommentData $commentData
    ) {
        parent::__construct(
            method: 'POST',
            endpoint: "/rest/api/3/issue/{$ticketNumber}/comment",
            params: [],
            data: $this->buildCommentData($commentData)
        );
    }

    public function getTicketNumber(): string
    {
        return $this->extractTicketNumberFromEndpoint();
    }

    public function getCommentData(): CommentData
    {
        return $this->commentData;
    }

    private function buildCommentData(CommentData $commentData): array
    {
        return [
            'body' => [
                'type' => 'doc',
                'version' => 1,
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $commentData->body,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function extractTicketNumberFromEndpoint(): string
    {
        $parts = explode('/', $this->endpoint);
        return $parts[count($parts) - 2]; // Get the ticket number before 'comment'
    }
}
