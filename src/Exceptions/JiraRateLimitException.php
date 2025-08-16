<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Exceptions;

use Exception;

final class JiraRateLimitException extends Exception
{
    public function __construct(
        string $message = 'Rate limit exceeded',
        int $code = 429,
        ?Exception $previous = null,
        public readonly ?int $retryAfter = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
