<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use Pnkt\Jiravel\Services\TicketService;
use Pnkt\Jiravel\Services\LoggingService;
use Pnkt\Jiravel\Contracts\JiraClientInterface;
use Pnkt\Jiravel\DataTransferObjects\TicketData;
use Pnkt\Jiravel\DataTransferObjects\SearchCriteria;
use Pnkt\Jiravel\Http\Responses\JiraResponse;
use Pnkt\Jiravel\Exceptions\JiraException;
use Mockery;

class TicketServiceTest extends TestCase
{
    private TicketService $ticketService;
    private $jiraClientMock;
    private $loggingServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->jiraClientMock = Mockery::mock(JiraClientInterface::class);
        $this->loggingServiceMock = Mockery::mock(LoggingService::class);
        
        $this->ticketService = new TicketService(
            $this->jiraClientMock,
            'TEST',
            $this->loggingServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetTicketByNumberSuccess(): void
    {
        // Arrange
        $ticketNumber = 'TEST-123';
        $responseData = [
            'id' => '12345',
            'key' => 'TEST-123',
            'fields' => [
                'summary' => 'Test Ticket',
                'description' => 'Test Description',
                'status' => ['name' => 'Open'],
                'assignee' => ['name' => 'test@example.com'],
            ]
        ];

        $response = new JiraResponse(200, $responseData, []);
        
        $this->loggingServiceMock->shouldReceive('logOperation')->twice();
        $this->jiraClientMock->shouldReceive('send')
            ->once()
            ->andReturn($response);

        // Act
        $result = $this->ticketService->getTicketByNumber($ticketNumber);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals('TEST-123', $result->getNumber());
        $this->assertEquals('Test Ticket', $result->summary);
    }

    public function testGetTicketByNumberFailure(): void
    {
        // Arrange
        $ticketNumber = 'TEST-999';
        $response = new JiraResponse(404, ['errorMessages' => ['Issue does not exist']], []);
        
        $this->loggingServiceMock->shouldReceive('logOperation')->once();
        $this->jiraClientMock->shouldReceive('send')
            ->once()
            ->andReturn($response);

        // Act & Assert
        $this->expectException(JiraException::class);
        $this->expectExceptionMessage('Failed to get ticket TEST-999');
        
        $this->ticketService->getTicketByNumber($ticketNumber);
    }

    public function testCreateTicketSuccess(): void
    {
        // Arrange
        $ticketData = new TicketData(
            summary: 'New Test Ticket',
            description: 'New Test Description',
            issueType: 'Task',
            priority: 'High',
            assignee: 'test@example.com'
        );

        $responseData = [
            'id' => '12346',
            'key' => 'TEST-124',
            'fields' => [
                'summary' => 'New Test Ticket',
                'description' => 'New Test Description',
                'status' => ['name' => 'Open'],
                'assignee' => ['name' => 'test@example.com'],
            ]
        ];

        $response = new JiraResponse(201, $responseData, []);
        
        $this->loggingServiceMock->shouldReceive('logOperation')->twice();
        $this->jiraClientMock->shouldReceive('send')
            ->once()
            ->andReturn($response);

        // Act
        $result = $this->ticketService->createTicket($ticketData);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals('TEST-124', $result->getNumber());
        $this->assertEquals('New Test Ticket', $result->summary);
    }

    public function testSearchTicketsSuccess(): void
    {
        // Arrange
        $searchCriteria = new SearchCriteria(
            jql: 'project = TEST',
            fields: ['summary', 'status'],
            orderBy: 'created DESC'
        );

        $responseData = [
            'total' => 1,
            'issues' => [
                [
                    'id' => '12345',
                    'key' => 'TEST-123',
                    'fields' => [
                        'summary' => 'Test Ticket',
                        'status' => ['name' => 'Open'],
                    ]
                ]
            ]
        ];

        $response = new JiraResponse(200, $responseData, []);
        
        $this->loggingServiceMock->shouldReceive('logOperation')->twice();
        $this->jiraClientMock->shouldReceive('send')
            ->once()
            ->andReturn($response);

        // Act
        $result = $this->ticketService->searchTickets($searchCriteria);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('TEST-123', $result[0]->getNumber());
    }
}
