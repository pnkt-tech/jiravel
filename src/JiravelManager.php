<?php

declare(strict_types=1);

namespace Pnkt\Jiravel;

use Pnkt\Jiravel\Contracts\Services\TicketServiceInterface;
use Pnkt\Jiravel\Contracts\Services\CommentServiceInterface;
use Pnkt\Jiravel\Contracts\Services\StatusServiceInterface;
use Pnkt\Jiravel\DataTransferObjects\TicketData;
use Pnkt\Jiravel\DataTransferObjects\CommentData;
use Pnkt\Jiravel\DataTransferObjects\StatusData;
use Pnkt\Jiravel\DataTransferObjects\AssigneeData;
use Pnkt\Jiravel\DataTransferObjects\AttributeData;
use Pnkt\Jiravel\DataTransferObjects\DescriptionData;
use Pnkt\Jiravel\DataTransferObjects\AttachmentData;
use Pnkt\Jiravel\DataTransferObjects\UserActivityData;
use Pnkt\Jiravel\DataTransferObjects\SearchCriteria;
use Pnkt\Jiravel\DataTransferObjects\TicketDetails;
use Illuminate\Container\Container;

final readonly class JiravelManager
{
    public function __construct(
        private readonly Container $app
    ) {}

    public function tickets(): TicketServiceInterface
    {
        return $this->app->make(TicketServiceInterface::class);
    }

    public function comments(): CommentServiceInterface
    {
        return $this->app->make(CommentServiceInterface::class);
    }

    public function status(): StatusServiceInterface
    {
        return $this->app->make(StatusServiceInterface::class);
    }

    // Convenience methods for ticket operations
    public function getTicket(string $ticketNumber): TicketDetails
    {
        return $this->tickets()->getTicketByNumber($ticketNumber);
    }

    public function searchTickets(string $query, int $maxResults = 50, int $startAt = 0): array
    {
        $searchCriteria = SearchCriteria::createForSearch(config('jiravel.project_key'), $query);
        return $this->tickets()->searchTickets($searchCriteria, $maxResults, $startAt);
    }

    public function createTicket(TicketData $ticketData): TicketDetails
    {
        return $this->tickets()->createTicket($ticketData);
    }

    public function addComment(string $ticketNumber, CommentData $commentData): void
    {
        $this->comments()->addComment($ticketNumber, $commentData);
    }

    public function changeStatus(string $ticketNumber, StatusData $statusData): void
    {
        $this->status()->changeStatus($ticketNumber, $statusData);
    }

    public function changeAttribute(string $ticketNumber, AttributeData $attributeData): void
    {
        $this->tickets()->changeAttribute($ticketNumber, $attributeData);
    }

    public function reassignTicket(string $ticketNumber, AssigneeData $assigneeData): void
    {
        $this->tickets()->reassignTicket($ticketNumber, $assigneeData);
    }

    public function editDescription(string $ticketNumber, DescriptionData $descriptionData): void
    {
        $this->tickets()->editDescription($ticketNumber, $descriptionData);
    }

    public function listTickets(
        ?string $assignee = null,
        ?string $label = null,
        ?string $issueType = null,
        int $maxResults = 50,
        int $startAt = 0
    ): array {
        $searchCriteria = SearchCriteria::createForList(
            config('jiravel.project_key'),
            $assignee,
            $label,
            $issueType
        );
        return $this->tickets()->searchTickets($searchCriteria, $maxResults, $startAt);
    }

    public function getTicketHistory(string $ticketNumber, int $maxResults = 100, int $startAt = 0): array
    {
        return $this->tickets()->getTicketHistory($ticketNumber, $maxResults, $startAt);
    }

    public function getAvailableTransitions(string $ticketNumber): array
    {
        return $this->status()->getAvailableTransitions($ticketNumber);
    }

    public function getComments(string $ticketNumber, int $maxResults = 50, int $startAt = 0): array
    {
        return $this->comments()->getComments($ticketNumber, $maxResults, $startAt);
    }

    public function updateComment(string $ticketNumber, string $commentId, CommentData $commentData): void
    {
        $this->comments()->updateComment($ticketNumber, $commentId, $commentData);
    }

    public function deleteComment(string $ticketNumber, string $commentId): void
    {
        $this->comments()->deleteComment($ticketNumber, $commentId);
    }

    public function deleteTicket(string $ticketNumber): bool
    {
        return $this->tickets()->deleteTicket($ticketNumber);
    }

    public function updateTicket(string $ticketNumber, TicketData $ticketData): TicketDetails
    {
        return $this->tickets()->updateTicket($ticketNumber, $ticketData);
    }
}
