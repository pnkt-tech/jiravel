<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Requests\Tickets;

use Pnkt\Jiravel\Http\Requests\JiraRequest;
use Pnkt\Jiravel\DataTransferObjects\SearchCriteria;

final readonly class SearchTicketsRequest extends JiraRequest
{
    public function __construct(
        SearchCriteria $searchCriteria,
        int $maxResults = 50,
        int $startAt = 0
    ) {
        parent::__construct(
            method: 'POST',
            endpoint: '/rest/api/3/search',
            params: [],
            data: [
                'jql' => $searchCriteria->buildJQL(),
                'maxResults' => $maxResults,
                'startAt' => $startAt,
                'fields' => $searchCriteria->getFields(),
            ]
        );
    }

    public function getSearchCriteria(): SearchCriteria
    {
        return $this->searchCriteria;
    }

    public function getMaxResults(): int
    {
        return $this->maxResults;
    }

    public function getStartAt(): int
    {
        return $this->startAt;
    }
}
