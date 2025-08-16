<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Contracts\Services;

use Pnkt\Jiravel\DataTransferObjects\CommentData;

interface CommentServiceInterface
{
    public function addComment(string $ticketNumber, CommentData $commentData): void;
    public function getComments(string $ticketNumber, int $maxResults = 50, int $startAt = 0): array;
    public function updateComment(string $ticketNumber, string $commentId, CommentData $commentData): void;
    public function deleteComment(string $ticketNumber, string $commentId): void;
}
