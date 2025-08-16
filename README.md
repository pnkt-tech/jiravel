# Jiravel

A Laravel package for Jira integration with comprehensive ticket management capabilities.

## Installation

```bash
composer require pnkt/jiravel
```

Publish the config file:

```bash
php artisan vendor:publish --tag=jiravel-config
```

## Configuration

Add your Jira credentials to `.env`:

```env
JIRA_BASE_URL=https://your-domain.atlassian.net
JIRA_USERNAME=your-email@example.com
JIRA_API_TOKEN=your-api-token
JIRA_PROJECT_KEY=PROJECT
```

Add the logging channel to `config/logging.php`:

```php
'channels' => [
    'jiravel' => [
        'driver' => 'daily',
        'path' => storage_path('logs/jiravel.log'),
        'level' => env('JIRA_LOG_LEVEL', 'info'),
        'days' => 14,
    ],
],
```

## Usage

### Basic Operations

```php
use Pnkt\Jiravel\Facades\Jiravel;
use Pnkt\Jiravel\DataTransferObjects\TicketData;
use Pnkt\Jiravel\DataTransferObjects\CommentData;
use Pnkt\Jiravel\DataTransferObjects\StatusData;

// Get a ticket
$ticket = Jiravel::getTicket('PROJECT-123');

// Create a ticket
$ticketData = new TicketData(
    summary: 'Bug Report',
    description: 'Critical bug description',
    issueType: 'Bug',
    priority: 'High',
    assignee: 'developer@example.com'
);

$newTicket = Jiravel::createTicket($ticketData);

// Search tickets
$tickets = Jiravel::searchTickets('project = PROJECT AND type = Bug');

// Add comment
$commentData = new CommentData(
    body: 'This issue has been assigned',
    author: 'manager@example.com'
);

Jiravel::addComment('PROJECT-123', $commentData);

// Change status
$statusData = new StatusData(
    status: 'In Progress',
    comment: 'Starting work'
);

Jiravel::changeStatus('PROJECT-123', $statusData);
```

### Advanced Operations

```php
use Pnkt\Jiravel\DataTransferObjects\AssigneeData;
use Pnkt\Jiravel\DataTransferObjects\AttributeData;
use Pnkt\Jiravel\DataTransferObjects\DescriptionData;

// Reassign ticket
$assigneeData = new AssigneeData(assignee: 'senior-dev@example.com');
Jiravel::reassignTicket('PROJECT-123', $assigneeData);

// Change attribute
$attributeData = new AttributeData(
    attribute: 'priority',
    value: 'High'
);
Jiravel::changeAttribute('PROJECT-123', $attributeData);

// Edit description
$descriptionData = new DescriptionData(
    description: 'Updated description'
);
Jiravel::editDescription('PROJECT-123', $descriptionData);

// Get ticket history
$history = Jiravel::getTicketHistory('PROJECT-123');
```

### Using Services Directly

```php
use Pnkt\Jiravel\Contracts\Services\TicketServiceInterface;
use Pnkt\Jiravel\Contracts\Services\CommentServiceInterface;
use Pnkt\Jiravel\Contracts\Services\StatusServiceInterface;

$ticketService = app(TicketServiceInterface::class);
$commentService = app(CommentServiceInterface::class);
$statusService = app(StatusServiceInterface::class);

// Get available transitions
$transitions = $statusService->getAvailableTransitions('PROJECT-123');

// Get comments
$comments = $commentService->getComments('PROJECT-123');
```

### Error Handling

```php
use Pnkt\Jiravel\Exceptions\JiraException;
use Pnkt\Jiravel\Exceptions\JiraValidationException;
use Pnkt\Jiravel\Exceptions\JiraRateLimitException;

try {
    $ticket = Jiravel::getTicket('PROJECT-999');
} catch (JiraValidationException $e) {
    // Handle validation errors
    Log::warning('Validation error: ' . $e->getMessage());
} catch (JiraRateLimitException $e) {
    // Handle rate limiting
    $retryAfter = $e->retryAfter;
    Log::warning("Rate limited. Retry after {$retryAfter} seconds");
} catch (JiraException $e) {
    // Handle general Jira errors
    Log::error('Jira error: ' . $e->getMessage());
}
```

## Features

- **Ticket Management**: Create, read, update, delete tickets
- **Comments**: Add, update, delete comments
- **Status Transitions**: Change ticket status with transition support
- **Search**: Advanced search using JQL
- **History**: Get complete ticket history and changelog
- **Rate Limiting**: Built-in rate limiting (1000 requests/minute)
- **Caching**: Automatic caching for GET requests
- **Logging**: Comprehensive logging for debugging

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
