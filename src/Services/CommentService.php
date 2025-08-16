<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Services;

use Pnkt\Jiravel\Contracts\JiraClientInterface;
use Pnkt\Jiravel\Contracts\Services\CommentServiceInterface;
use Pnkt\Jiravel\DataTransferObjects\CommentData;
use Pnkt\Jiravel\Exceptions\JiraException;
use Pnkt\Jiravel\Http\Requests\Comments\AddCommentRequest;
use Pnkt\Jiravel\Http\Requests\Comments\GetCommentsRequest;
use Pnkt\Jiravel\Http\Requests\Comments\UpdateCommentRequest;
use Pnkt\Jiravel\Http\Requests\Comments\DeleteCommentRequest;

final readonly class CommentService implements CommentServiceInterface
{
    public function __construct(
        private readonly JiraClientInterface $jiraClient,
        private readonly LoggingService $loggingService
    ) {}

    public function addComment(string $ticketNumber, CommentData $commentData): void
    {
        $this->loggingService->logOperation('CommentService', 'addComment', [
            'ticketNumber' => $ticketNumber,
            'commentLength' => strlen($commentData->body)
        ]);

        $request = new AddCommentRequest($ticketNumber, $commentData);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to add comment to ticket {$ticketNumber}");
        }

        $this->loggingService->logOperation('CommentService', 'addComment', [
            'ticketNumber' => $ticketNumber,
            'success' => true
        ]);
    }

    public function getComments(string $ticketNumber, int $maxResults = 50, int $startAt = 0): array
    {
        $this->loggingService->logOperation('CommentService', 'getComments', [
            'ticketNumber' => $ticketNumber,
            'maxResults' => $maxResults,
            'startAt' => $startAt
        ]);

        $request = new GetCommentsRequest($ticketNumber, $maxResults, $startAt);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to get comments for ticket {$ticketNumber}");
        }

        $this->loggingService->logOperation('CommentService', 'getComments', [
            'ticketNumber' => $ticketNumber,
            'commentsCount' => count($response->getData()['comments'] ?? []),
            'success' => true
        ]);

        return $response->getData();
    }

    public function updateComment(string $ticketNumber, string $commentId, CommentData $commentData): void
    {
        $this->loggingService->logOperation('CommentService', 'updateComment', [
            'ticketNumber' => $ticketNumber,
            'commentId' => $commentId,
            'commentLength' => strlen($commentData->body)
        ]);

        $request = new UpdateCommentRequest($ticketNumber, $commentId, $commentData);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to update comment {$commentId} for ticket {$ticketNumber}");
        }

        $this->loggingService->logOperation('CommentService', 'updateComment', [
            'ticketNumber' => $ticketNumber,
            'commentId' => $commentId,
            'success' => true
        ]);
    }

    public function deleteComment(string $ticketNumber, string $commentId): void
    {
        $this->loggingService->logOperation('CommentService', 'deleteComment', [
            'ticketNumber' => $ticketNumber,
            'commentId' => $commentId
        ]);

        $request = new DeleteCommentRequest($ticketNumber, $commentId);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to delete comment {$commentId} for ticket {$ticketNumber}");
        }

        $this->loggingService->logOperation('CommentService', 'deleteComment', [
            'ticketNumber' => $ticketNumber,
            'commentId' => $commentId,
            'success' => true
        ]);
    }
}
