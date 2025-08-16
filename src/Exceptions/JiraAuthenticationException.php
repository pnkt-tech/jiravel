<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Exceptions;

use Exception;

final class JiraAuthenticationException extends Exception
{
    public function __construct(
        string $message = 'Authentication failed',
        int $code = 401,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
