<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Pnkt\Jiravel\Services\JiraTicketService tickets()
 * @method static \Pnkt\Jiravel\Services\JiraUserService users()
 * @method static \Pnkt\Jiravel\Services\JiraAttachmentService attachments()
 * @method static \Pnkt\Jiravel\Services\JiraHistoryService history()
 * @method static \Pnkt\Jiravel\Services\JiraActivityService activity()
 * 
 * @method static \Pnkt\Jiravel\DataTransferObjects\TicketDetails getTicket(string $ticketNumber)
 * @method static array searchTickets(string $query, int $maxResults = 50, int $startAt = 0)
 * @method static \Pnkt\Jiravel\DataTransferObjects\TicketDetails createTicket(\Pnkt\Jiravel\DataTransferObjects\TicketData $ticketData)
 * @method static void addComment(string $ticketNumber, \Pnkt\Jiravel\DataTransferObjects\CommentData $commentData)
 * @method static void changeStatus(string $ticketNumber, \Pnkt\Jiravel\DataTransferObjects\StatusData $statusData)
 * @method static void changeAttribute(string $ticketNumber, \Pnkt\Jiravel\DataTransferObjects\AttributeData $attributeData)
 * @method static void reassignTicket(string $ticketNumber, \Pnkt\Jiravel\DataTransferObjects\AssigneeData $assigneeData)
 * @method static void editDescription(string $ticketNumber, \Pnkt\Jiravel\DataTransferObjects\DescriptionData $descriptionData)
 * @method static array listTickets(?string $assignee = null, ?string $label = null, ?string $issueType = null, int $maxResults = 50, int $startAt = 0)
 * @method static array getTicketHistory(string $ticketNumber)
 * 
 * @method static array addAttachment(string $ticketNumber, \Pnkt\Jiravel\DataTransferObjects\AttachmentData $attachmentData)
 * @method static array getAttachments(string $ticketNumber)
 * @method static array deleteAttachment(string $attachmentId)
 * @method static string downloadAttachment(string $attachmentId)
 * 
 * @method static array getUser(string $username)
 * @method static array searchUsers(string $query)
 * @method static array getProjectUsers()
 * @method static array getUserActivitySummary(\Pnkt\Jiravel\DataTransferObjects\UserActivityData $activityData)
 * @method static array getUserWorklog(string $username, string $startDate, string $endDate)
 * @method static array getUsersByRole(string $role)
 * 
 * @method static array getStatusHistory(string $ticketNumber)
 * @method static array getAssigneeHistory(string $ticketNumber)
 * @method static array getPriorityHistory(string $ticketNumber)
 * @method static array getCommentHistory(string $ticketNumber)
 * @method static array getWorklogHistory(string $ticketNumber)
 * @method static array getAttachmentHistory(string $ticketNumber)
 * 
 * @method static array getTeamActivitySummary(string $period = 'monthly', ?string $startDate = null, ?string $endDate = null)
 * @method static array getProjectActivitySummary(string $period = 'monthly', ?string $startDate = null, ?string $endDate = null)
 * @method static array getWorklogSummary(string $username, string $startDate, string $endDate)
 */
class Jiravel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'jiravel';
    }
}
