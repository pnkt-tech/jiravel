<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Contracts\Services;

use Pnkt\Jiravel\DataTransferObjects\TicketData;
use Pnkt\Jiravel\DataTransferObjects\TicketDetails;
use Pnkt\Jiravel\DataTransferObjects\SearchCriteria;
use Pnkt\Jiravel\DataTransferObjects\AttributeData;
use Pnkt\Jiravel\DataTransferObjects\AssigneeData;
use Pnkt\Jiravel\DataTransferObjects\DescriptionData;

interface TicketServiceInterface
{
    public function getTicketByNumber(string $ticketNumber): TicketDetails;
    public function searchTickets(SearchCriteria $searchCriteria, int $maxResults = 50, int $startAt = 0): array;
    public function createTicket(TicketData $ticketData): TicketDetails;
    public function updateTicket(string $ticketNumber, TicketData $ticketData): TicketDetails;
    public function deleteTicket(string $ticketNumber): bool;
    public function getTicketHistory(string $ticketNumber, int $maxResults = 100, int $startAt = 0): array;
    public function changeAttribute(string $ticketNumber, AttributeData $attributeData): void;
    public function reassignTicket(string $ticketNumber, AssigneeData $assigneeData): void;
    public function editDescription(string $ticketNumber, DescriptionData $descriptionData): void;
}
