<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class JiraActivitySummary
{
    public function __construct(
        public int $totalTickets,
        public array $ticketsByStatus,
        public array $ticketsByType,
        public array $ticketsByPriority,
        public float $totalWorkHours,
        public float $averageResolutionTime,
        public int $createdTickets,
        public int $resolvedTickets,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            totalTickets: $data['total_tickets'] ?? 0,
            ticketsByStatus: $data['tickets_by_status'] ?? [],
            ticketsByType: $data['tickets_by_type'] ?? [],
            ticketsByPriority: $data['tickets_by_priority'] ?? [],
            totalWorkHours: $data['total_work_hours'] ?? 0.0,
            averageResolutionTime: $data['average_resolution_time'] ?? 0.0,
            createdTickets: $data['created_tickets'] ?? 0,
            resolvedTickets: $data['resolved_tickets'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'total_tickets' => $this->totalTickets,
            'tickets_by_status' => $this->ticketsByStatus,
            'tickets_by_type' => $this->ticketsByType,
            'tickets_by_priority' => $this->ticketsByPriority,
            'total_work_hours' => $this->totalWorkHours,
            'average_resolution_time' => $this->averageResolutionTime,
            'created_tickets' => $this->createdTickets,
            'resolved_tickets' => $this->resolvedTickets,
        ];
    }
}
