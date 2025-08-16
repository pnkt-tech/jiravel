<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Exceptions;

use Exception;

final class JiraException extends Exception
{
    public static function configurationMissing(string $key): self
    {
        return new self("Jira configuration missing: {$key}");
    }

    public static function invalidCredentials(): self
    {
        return new self('Invalid Jira credentials. Please check your username and API token.');
    }

    public static function projectNotFound(string $projectKey): self
    {
        return new self("Jira project not found: {$projectKey}");
    }

    public static function ticketNotFound(string $ticketNumber): self
    {
        return new self("Jira ticket not found: {$ticketNumber}");
    }

    public static function userNotFound(string $username): self
    {
        return new self("Jira user not found: {$username}");
    }

    public static function invalidStatusTransition(string $ticketNumber, string $status): self
    {
        return new self("Invalid status transition '{$status}' for ticket {$ticketNumber}");
    }

    public static function attachmentNotFound(string $attachmentId): self
    {
        return new self("Jira attachment not found: {$attachmentId}");
    }

    public static function fileNotFound(string $filePath): self
    {
        return new self("File not found: {$filePath}");
    }

    public static function invalidDateRange(string $startDate, string $endDate): self
    {
        return new self("Invalid date range: {$startDate} to {$endDate}");
    }

    public static function rateLimitExceeded(): self
    {
        return new self('Jira API rate limit exceeded. Please try again later.');
    }

    public static function permissionDenied(string $operation): self
    {
        return new self("Permission denied for operation: {$operation}");
    }
}
