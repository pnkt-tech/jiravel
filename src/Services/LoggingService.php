<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Services;

use Illuminate\Support\Facades\Log;

final readonly class LoggingService
{
    public function logOperation(string $service, string $operation, array $context): void
    {
        if (!config('jiravel.logging.enabled', true)) {
            return;
        }

        Log::channel('jiravel')->info("{$service}: {$operation}", $context);
    }

    public function logError(string $service, string $operation, string $error, array $context = []): void
    {
        if (!config('jiravel.logging.enabled', true)) {
            return;
        }

        Log::channel('jiravel')->error("{$service}: {$operation} - {$error}", $context);
    }

    public function logWarning(string $service, string $operation, string $warning, array $context = []): void
    {
        if (!config('jiravel.logging.enabled', true)) {
            return;
        }

        Log::channel('jiravel')->warning("{$service}: {$operation} - {$warning}", $context);
    }

    public function logDebug(string $service, string $operation, array $context = []): void
    {
        if (!config('jiravel.logging.enabled', true) || config('jiravel.logging.level') !== 'debug') {
            return;
        }

        Log::channel('jiravel')->debug("{$service}: {$operation}", $context);
    }
}
