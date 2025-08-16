<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Requests\Status;

use Pnkt\Jiravel\Http\Requests\JiraRequest;

final readonly class TransitionTicketRequest extends JiraRequest
{
    public function __construct(
        string $ticketNumber,
        string $transitionId,
        ?string $comment = null
    ) {
        parent::__construct(
            method: 'POST',
            endpoint: "/rest/api/3/issue/{$ticketNumber}/transitions",
            params: [],
            data: $this->buildTransitionData($transitionId, $comment)
        );
    }

    public function getTicketNumber(): string
    {
        return $this->extractTicketNumberFromEndpoint();
    }

    public function getTransitionId(): string
    {
        return $this->transitionId;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    private function buildTransitionData(string $transitionId, ?string $comment): array
    {
        $data = [
            'transition' => [
                'id' => $transitionId,
            ],
        ];

        if ($comment !== null) {
            $data['update'] = [
                'comment' => [
                    [
                        'add' => [
                            'body' => [
                                'type' => 'doc',
                                'version' => 1,
                                'content' => [
                                    [
                                        'type' => 'paragraph',
                                        'content' => [
                                            [
                                                'type' => 'text',
                                                'text' => $comment,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        return $data;
    }

    private function extractTicketNumberFromEndpoint(): string
    {
        $parts = explode('/', $this->endpoint);
        return $parts[count($parts) - 2]; // Get the ticket number before 'transitions'
    }
}
