<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Exceptions;

use Exception;

final class JiraValidationException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
