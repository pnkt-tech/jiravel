<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Tests;

use Orchestra\Testbench\TestCase;
use Pnkt\Jiravel\JiravelServiceProvider;
use Pnkt\Jiravel\Facades\Jiravel;
use Pnkt\Jiravel\DataTransferObjects\TicketData;
use Pnkt\Jiravel\DataTransferObjects\CommentData;
use Pnkt\Jiravel\DataTransferObjects\StatusData;
use Pnkt\Jiravel\DataTransferObjects\AssigneeData;
use Pnkt\Jiravel\DataTransferObjects\AttributeData;
use Pnkt\Jiravel\DataTransferObjects\DescriptionData;
use Pnkt\Jiravel\DataTransferObjects\AttachmentData;
use Pnkt\Jiravel\DataTransferObjects\UserActivityData;

class JiravelTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            JiravelServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Jiravel' => \Pnkt\Jiravel\Facades\Jiravel::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('jiravel.base_url', 'https://test.atlassian.net');
        $app['config']->set('jiravel.username', 'test@example.com');
        $app['config']->set('jiravel.api_token', 'test-token');
        $app['config']->set('jiravel.project_key', 'TEST');
    }

    public function test_can_create_ticket_data(): void
    {
        $ticketData = new TicketData(
            summary: 'Test ticket',
            description: 'This is a test ticket',
            issueType: 'Bug',
            priority: 'High',
            assignee: 'test.user',
            labels: ['test', 'bug'],
            components: ['Test Component']
        );

        $this->assertEquals('Test ticket', $ticketData->summary);
        $this->assertEquals('This is a test ticket', $ticketData->description);
        $this->assertEquals('Bug', $ticketData->issueType);
        $this->assertEquals('High', $ticketData->priority);
        $this->assertEquals('test.user', $ticketData->assignee);
        $this->assertEquals(['test', 'bug'], $ticketData->labels);
        $this->assertEquals(['Test Component'], $ticketData->components);
    }

    public function test_can_create_comment_data(): void
    {
        $commentData = new CommentData('This is a test comment');

        $this->assertEquals('This is a test comment', $commentData->body);
    }

    public function test_can_create_status_data(): void
    {
        $statusData = new StatusData('In Progress', 'Starting work');

        $this->assertEquals('In Progress', $statusData->status);
        $this->assertEquals('Starting work', $statusData->comment);
    }

    public function test_can_create_assignee_data(): void
    {
        $assigneeData = new AssigneeData('test.user', 'Test User');

        $this->assertEquals('test.user', $assigneeData->username);
        $this->assertEquals('Test User', $assigneeData->displayName);
    }

    public function test_can_create_attribute_data(): void
    {
        $attributeData = new AttributeData('priority', ['name' => 'High']);

        $this->assertEquals('priority', $attributeData->field);
        $this->assertEquals(['name' => 'High'], $attributeData->value);
    }

    public function test_can_create_description_data(): void
    {
        $descriptionData = new DescriptionData('Updated description');

        $this->assertEquals('Updated description', $descriptionData->description);
    }

    public function test_can_create_attachment_data(): void
    {
        $attachmentData = new AttachmentData(
            filePath: '/path/to/file.pdf',
            filename: 'document.pdf',
            contentType: 'application/pdf'
        );

        $this->assertEquals('/path/to/file.pdf', $attachmentData->filePath);
        $this->assertEquals('document.pdf', $attachmentData->filename);
        $this->assertEquals('application/pdf', $attachmentData->contentType);
    }

    public function test_can_create_user_activity_data(): void
    {
        $activityData = new UserActivityData(
            username: 'test.user',
            period: 'monthly',
            startDate: '2024-01-01',
            endDate: '2024-01-31'
        );

        $this->assertEquals('test.user', $activityData->username);
        $this->assertEquals('monthly', $activityData->period);
        $this->assertEquals('2024-01-01', $activityData->startDate);
        $this->assertEquals('2024-01-31', $activityData->endDate);
    }

    public function test_can_access_jiravel_facade(): void
    {
        $this->assertInstanceOf(\Pnkt\Jiravel\JiravelManager::class, Jiravel::getFacadeRoot());
    }

    public function test_can_access_ticket_service(): void
    {
        $ticketService = Jiravel::tickets();
        $this->assertInstanceOf(\Pnkt\Jiravel\Services\JiraTicketService::class, $ticketService);
    }

    public function test_can_access_user_service(): void
    {
        $userService = Jiravel::users();
        $this->assertInstanceOf(\Pnkt\Jiravel\Services\JiraUserService::class, $userService);
    }

    public function test_can_access_attachment_service(): void
    {
        $attachmentService = Jiravel::attachments();
        $this->assertInstanceOf(\Pnkt\Jiravel\Services\JiraAttachmentService::class, $attachmentService);
    }

    public function test_can_access_history_service(): void
    {
        $historyService = Jiravel::history();
        $this->assertInstanceOf(\Pnkt\Jiravel\Services\JiraHistoryService::class, $historyService);
    }

    public function test_can_access_activity_service(): void
    {
        $activityService = Jiravel::activity();
        $this->assertInstanceOf(\Pnkt\Jiravel\Services\JiraActivityService::class, $activityService);
    }
}
