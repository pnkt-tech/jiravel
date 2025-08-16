<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Services;

use Pnkt\Jiravel\Contracts\JiraClientInterface;
use Pnkt\Jiravel\Contracts\Services\StatusServiceInterface;
use Pnkt\Jiravel\DataTransferObjects\StatusData;
use Pnkt\Jiravel\Exceptions\JiraException;
use Pnkt\Jiravel\Http\Requests\Status\GetTransitionsRequest;
use Pnkt\Jiravel\Http\Requests\Status\TransitionTicketRequest;

final readonly class StatusService implements StatusServiceInterface
{
    public function __construct(
        private readonly JiraClientInterface $jiraClient,
        private readonly LoggingService $loggingService
    ) {}

    public function changeStatus(string $ticketNumber, StatusData $statusData): void
    {
        $this->loggingService->logOperation('StatusService', 'changeStatus', [
            'ticketNumber' => $ticketNumber,
            'newStatus' => $statusData->status
        ]);

        // Get available transitions
        $transitionsRequest = new GetTransitionsRequest($ticketNumber);
        $transitionsResponse = $this->jiraClient->send($transitionsRequest);
        
        if (!$transitionsResponse->isSuccessful()) {
            throw new JiraException("Failed to get transitions for ticket {$ticketNumber}");
        }

        $transitionId = $this->findTransitionId($transitionsResponse->getData()['transitions'] ?? [], $statusData->status);
        
        if (!$transitionId) {
            throw new JiraException("Status transition '{$statusData->status}' not found for ticket {$ticketNumber}");
        }

        $request = new TransitionTicketRequest($ticketNumber, $transitionId, $statusData->comment);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to change status for ticket {$ticketNumber}");
        }

        $this->loggingService->logOperation('StatusService', 'changeStatus', [
            'ticketNumber' => $ticketNumber,
            'newStatus' => $statusData->status,
            'success' => true
        ]);
    }

    public function getAvailableTransitions(string $ticketNumber): array
    {
        $this->loggingService->logOperation('StatusService', 'getAvailableTransitions', [
            'ticketNumber' => $ticketNumber
        ]);

        $request = new GetTransitionsRequest($ticketNumber);
        $response = $this->jiraClient->send($request);

        if (!$response->isSuccessful()) {
            throw new JiraException("Failed to get transitions for ticket {$ticketNumber}");
        }

        $transitions = $response->getData()['transitions'] ?? [];

        $this->loggingService->logOperation('StatusService', 'getAvailableTransitions', [
            'ticketNumber' => $ticketNumber,
            'transitionsCount' => count($transitions),
            'success' => true
        ]);

        return $transitions;
    }

    private function findTransitionId(array $transitions, string $statusName): ?string
    {
        foreach ($transitions as $transition) {
            if (strtolower($transition['to']['name']) === strtolower($statusName)) {
                return $transition['id'];
            }
        }

        return null;
    }
}
