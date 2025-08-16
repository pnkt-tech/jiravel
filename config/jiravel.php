<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Jira Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Jira integration.
    |
    */

    // Jira instance base URL (e.g., https://your-domain.atlassian.net)
    'base_url' => env('JIRA_BASE_URL', ''),

    // Jira username or email
    'username' => env('JIRA_USERNAME', ''),

    // Jira API token (not password)
    'api_token' => env('JIRA_API_TOKEN', ''),

    // Default project key
    'project_key' => env('JIRA_PROJECT_KEY', ''),

    // Request timeout in seconds
    'timeout' => env('JIRA_TIMEOUT', 30),

    // Retry attempts for failed requests
    'retry_attempts' => env('JIRA_RETRY_ATTEMPTS', 3),

    // Retry delay between attempts in seconds
    'retry_delay' => env('JIRA_RETRY_DELAY', 1),

    // Rate limiting configuration
    'rate_limiting' => [
        'enabled' => env('JIRA_RATE_LIMITING_ENABLED', true),
        'max_requests_per_minute' => env('JIRA_MAX_REQUESTS_PER_MINUTE', 1000),
        'retry_after_seconds' => env('JIRA_RETRY_AFTER_SECONDS', 60),
    ],

    // Cache configuration
    'cache' => [
        'enabled' => env('JIRA_CACHE_ENABLED', true),
        'ttl' => env('JIRA_CACHE_TTL', 3600), // 1 hour
        'prefix' => env('JIRA_CACHE_PREFIX', 'jiravel'),
    ],

    // Logging configuration
    'logging' => [
        'enabled' => env('JIRA_LOGGING_ENABLED', true),
        'channel' => env('JIRA_LOG_CHANNEL', 'jiravel'),
        'level' => env('JIRA_LOG_LEVEL', 'info'),
    ],

    // API Version configuration
    'api_version' => env('JIRA_API_VERSION', '3'),

    // Request configuration
    'requests' => [
        'default_headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ],
        'user_agent' => env('JIRA_USER_AGENT', 'Jiravel/1.0'),
    ],

    // Default issue types for new tickets
    'default_issue_types' => [
        'story' => 'Story',
        'bug' => 'Bug',
        'task' => 'Task',
        'epic' => 'Epic',
    ],

    // Default priority levels
    'priorities' => [
        'highest' => 'Highest',
        'high' => 'High',
        'medium' => 'Medium',
        'low' => 'Low',
        'lowest' => 'Lowest',
    ],
];
