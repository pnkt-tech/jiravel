<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Requests\Comments;

use Pnkt\Jiravel\Http\Requests\JiraRequest;
use Pnkt\Jiravel\DataTransferObjects\CommentData;

final readonly class UpdateCommentRequest extends JiraRequest
{
    public function __construct(
        string $ticketNumber,
        string $commentId,
        CommentData $commentData
    ) {
        parent::__construct(
            method: 'PUT',
            endpoint: "/rest/api/3/issue/{$ticketNumber}/comment/{$commentId}",
            params: [],
            data: $this->buildCommentData($commentData)
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
        return $parts[count($parts) - 3]; // Get the ticket number before 'comment'
    }

    private function extractCommentIdFromEndpoint(): string
    {
        $parts = explode('/', $this->endpoint);
        return end($parts); // Get the comment ID
    }
}
