<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Services;

use Pnkt\Jiravel\Contracts\JiraClientInterface;
use Pnkt\Jiravel\Contracts\Services\TicketServiceInterface;
use Pnkt\Jiravel\DataTransferObjects\TicketData;
use Pnkt\Jiravel\DataTransferObjects\TicketDetails;
use Pnkt\Jiravel\DataTransferObjects\SearchCriteria;
use Pnkt\Jiravel\Exceptions\JiraException;
use Pnkt\Jiravel\Http\Requests\Tickets\GetTicketRequest;
use Pnkt\Jiravel\Http\Requests\Tickets\CreateTicketRequest;
use Pnkt\Jiravel\Http\Requests\Tickets\SearchTicketsRequest;
use Pnkt\Jiravel\Http\Requests\Tickets\UpdateTicketRequest;
use Pnkt\Jiravel\Http\Requests\Tickets\DeleteTicketRequest;
use Pnkt\Jiravel\Http\Requests\History\GetChangelogRequest;
use Pnkt\Jiravel\Http\Responses\TicketResponse;
use Pnkt\Jiravel\Http\Responses\SearchResponse;

/**
 * Service for managing Jira tickets.
 * 
 * This service provides methods for creating, reading, updating, and deleting
 * Jira tickets, as well as searching and managing ticket history.
 * 
 * @package Pnkt\Jiravel\Services
 */
final readonly class TicketService implements TicketServiceInterface
{
    /**
     * Create a new TicketService instance.
     * 
     * @param JiraClientInterface $jiraClient The Jira API client
     * @param string $projectKey The default project key for ticket operations
     * @param LoggingService $loggingService The logging service for operation tracking
     */
    public function __construct(
        private readonly JiraClientInterface $jiraClient,
        private readonly string $projectKey,
        private readonly LoggingService $loggingService
    ) {}

    /**
     * Retrieve a ticket by its number.
     * 
     * @param string $ticketNumber The ticket number (e.g., "PROJ-123")
     * @return TicketDetails The ticket details
     * @throws JiraException When the ticket cannot be retrieved
     */
    public function getTicketByNumber(string $ticketNumber): TicketDetails
    {
        $this->loggingService->logOperation('TicketService', 'getTicketByNumber', ['ticketNumber' => $ticketNumber]);

        $request = new GetTicketRequest($ticketNumber);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to get ticket {$ticketNumber}");
        }

        $ticketResponse = new TicketResponse(
            $response->getStatusCode(),
            $response->getData(),
            $response->getHeaders()
        );

        $this->loggingService->logOperation('TicketService', 'getTicketByNumber', [
            'ticketNumber' => $ticketNumber,
            'success' => true
        ]);

        return $ticketResponse->getTicketDetails();
    }

    /**
     * Search for tickets using JQL criteria.
     * 
     * @param SearchCriteria $searchCriteria The search criteria including JQL
     * @param int $maxResults Maximum number of results to return (default: 50)
     * @param int $startAt Starting index for pagination (default: 0)
     * @return array Array of TicketDetails objects
     * @throws JiraException When the search fails
     */
    public function searchTickets(SearchCriteria $searchCriteria, int $maxResults = 50, int $startAt = 0): array
    {
        $this->loggingService->logOperation('TicketService', 'searchTickets', [
            'searchCriteria' => $searchCriteria->toArray(),
            'maxResults' => $maxResults,
            'startAt' => $startAt
        ]);

        $request = new SearchTicketsRequest($searchCriteria, $maxResults, $startAt);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException('Failed to search tickets');
        }

        $searchResponse = new SearchResponse(
            $response->getStatusCode(),
            $response->getData(),
            $response->getHeaders()
        );

        $this->loggingService->logOperation('TicketService', 'searchTickets', [
            'total' => $searchResponse->getTotal(),
            'results' => count($searchResponse->getIssues()),
            'success' => true
        ]);

        return $searchResponse->getTicketDetails();
    }

    /**
     * Create a new ticket in Jira.
     * 
     * @param TicketData $ticketData The ticket data including summary, description, etc.
     * @return TicketDetails The created ticket details
     * @throws JiraException When the ticket creation fails
     */
    public function createTicket(TicketData $ticketData): TicketDetails
    {
        $this->loggingService->logOperation('TicketService', 'createTicket', [
            'summary' => $ticketData->summary,
            'issueType' => $ticketData->issueType,
            'assignee' => $ticketData->assignee
        ]);

        $request = new CreateTicketRequest($ticketData, $this->projectKey);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException('Failed to create ticket');
        }

        $ticketResponse = new TicketResponse(
            $response->getStatusCode(),
            $response->getData(),
            $response->getHeaders()
        );

        $this->loggingService->logOperation('TicketService', 'createTicket', [
            'ticketNumber' => $ticketResponse->getTicketDetails()->getNumber(),
            'success' => true
        ]);

        return $ticketResponse->getTicketDetails();
    }

    /**
     * Update an existing ticket.
     * 
     * @param string $ticketNumber The ticket number to update
     * @param TicketData $ticketData The updated ticket data
     * @return TicketDetails The updated ticket details
     * @throws JiraException When the ticket update fails
     */
    public function updateTicket(string $ticketNumber, TicketData $ticketData): TicketDetails
    {
        $this->loggingService->logOperation('TicketService', 'updateTicket', [
            'ticketNumber' => $ticketNumber,
            'summary' => $ticketData->summary
        ]);

        $request = new UpdateTicketRequest($ticketNumber, $ticketData);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to update ticket {$ticketNumber}");
        }

        $ticketResponse = new TicketResponse(
            $response->getStatusCode(),
            $response->getData(),
            $response->getHeaders()
        );

        $this->loggingService->logOperation('TicketService', 'updateTicket', [
            'ticketNumber' => $ticketNumber,
            'success' => true
        ]);

        return $ticketResponse->getTicketDetails();
    }

    /**
     * Delete a ticket from Jira.
     * 
     * @param string $ticketNumber The ticket number to delete
     * @return bool True if deletion was successful
     * @throws JiraException When the ticket deletion fails
     */
    public function deleteTicket(string $ticketNumber): bool
    {
        $this->loggingService->logOperation('TicketService', 'deleteTicket', ['ticketNumber' => $ticketNumber]);

        $request = new DeleteTicketRequest($ticketNumber);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to delete ticket {$ticketNumber}");
        }

        $this->loggingService->logOperation('TicketService', 'deleteTicket', [
            'ticketNumber' => $ticketNumber,
            'success' => true
        ]);

        return true;
    }

    /**
     * Get the change history for a ticket.
     * 
     * @param string $ticketNumber The ticket number
     * @param int $maxResults Maximum number of history entries to return (default: 100)
     * @param int $startAt Starting index for pagination (default: 0)
     * @return array Array of changelog entries
     * @throws JiraException When the history retrieval fails
     */
    public function getTicketHistory(string $ticketNumber, int $maxResults = 100, int $startAt = 0): array
    {
        $this->loggingService->logOperation('TicketService', 'getTicketHistory', [
            'ticketNumber' => $ticketNumber,
            'maxResults' => $maxResults,
            'startAt' => $startAt
        ]);

        $request = new GetChangelogRequest($ticketNumber, $maxResults, $startAt);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to get ticket history for {$ticketNumber}");
        }

        $this->loggingService->logOperation('TicketService', 'getTicketHistory', [
            'ticketNumber' => $ticketNumber,
            'success' => true
        ]);

        return $response->getData()['values'] ?? [];
    }

    /**
     * Change a specific attribute of a ticket.
     * 
     * @param string $ticketNumber The ticket number
     * @param \Pnkt\Jiravel\DataTransferObjects\AttributeData $attributeData The attribute data
     * @throws JiraException When the attribute change fails
     */
    public function changeAttribute(string $ticketNumber, \Pnkt\Jiravel\DataTransferObjects\AttributeData $attributeData): void
    {
        $this->loggingService->logOperation('TicketService', 'changeAttribute', [
            'ticketNumber' => $ticketNumber,
            'attribute' => $attributeData->attribute,
            'value' => $attributeData->value
        ]);

        $ticketData = new TicketData(
            summary: '',
            description: '',
            issueType: '',
            priority: null,
            assignee: null,
            reporter: null,
            labels: [],
            components: []
        );

        $request = new UpdateTicketRequest($ticketNumber, $ticketData);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to change attribute for ticket {$ticketNumber}");
        }

        $this->loggingService->logOperation('TicketService', 'changeAttribute', [
            'ticketNumber' => $ticketNumber,
            'success' => true
        ]);
    }

    /**
     * Reassign a ticket to a different user.
     * 
     * @param string $ticketNumber The ticket number
     * @param \Pnkt\Jiravel\DataTransferObjects\AssigneeData $assigneeData The assignee data
     * @throws JiraException When the reassignment fails
     */
    public function reassignTicket(string $ticketNumber, \Pnkt\Jiravel\DataTransferObjects\AssigneeData $assigneeData): void
    {
        $this->loggingService->logOperation('TicketService', 'reassignTicket', [
            'ticketNumber' => $ticketNumber,
            'assignee' => $assigneeData->assignee
        ]);

        $ticketData = new TicketData(
            summary: '',
            description: '',
            issueType: '',
            priority: null,
            assignee: $assigneeData->assignee,
            reporter: null,
            labels: [],
            components: []
        );

        $request = new UpdateTicketRequest($ticketNumber, $ticketData);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to reassign ticket {$ticketNumber}");
        }

        $this->loggingService->logOperation('TicketService', 'reassignTicket', [
            'ticketNumber' => $ticketNumber,
            'success' => true
        ]);
    }

    /**
     * Edit the description of a ticket.
     * 
     * @param string $ticketNumber The ticket number
     * @param \Pnkt\Jiravel\DataTransferObjects\DescriptionData $descriptionData The description data
     * @throws JiraException When the description update fails
     */
    public function editDescription(string $ticketNumber, \Pnkt\Jiravel\DataTransferObjects\DescriptionData $descriptionData): void
    {
        $this->loggingService->logOperation('TicketService', 'editDescription', [
            'ticketNumber' => $ticketNumber,
            'descriptionLength' => strlen($descriptionData->description)
        ]);

        $ticketData = new TicketData(
            summary: '',
            description: $descriptionData->description,
            issueType: '',
            priority: null,
            assignee: null,
            reporter: null,
            labels: [],
            components: []
        );

        $request = new UpdateTicketRequest($ticketNumber, $ticketData);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to edit description for ticket {$ticketNumber}");
        }

        $this->loggingService->logOperation('TicketService', 'editDescription', [
            'ticketNumber' => $ticketNumber,
            'success' => true
        ]);
    }
}
