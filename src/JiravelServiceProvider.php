<?php

declare(strict_types=1);

namespace Pnkt\Jiravel;

use Pnkt\Jiravel\Contracts\JiraClientInterface;
use Pnkt\Jiravel\Http\JiraClient;
use Pnkt\Jiravel\Services\TicketService;
use Pnkt\Jiravel\Services\CommentService;
use Pnkt\Jiravel\Services\StatusService;
use Pnkt\Jiravel\Services\LoggingService;
use Pnkt\Jiravel\Contracts\Services\TicketServiceInterface;
use Pnkt\Jiravel\Contracts\Services\CommentServiceInterface;
use Pnkt\Jiravel\Contracts\Services\StatusServiceInterface;
use Illuminate\Support\ServiceProvider;

final class JiravelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/jiravel.php', 'jiravel');

        // Register logging service
        $this->app->singleton(LoggingService::class, function ($app) {
            return new LoggingService();
        });

        // Register JiraClient as singleton
        $this->app->singleton(JiraClientInterface::class, function ($app) {
            return new JiraClient(
                baseUrl: config('jiravel.base_url'),
                username: config('jiravel.username'),
                apiToken: config('jiravel.api_token'),
                timeout: config('jiravel.timeout', 30),
                retryAttempts: config('jiravel.retry_attempts', 3),
                retryDelay: config('jiravel.retry_delay', 1)
            );
        });

        // Register domain services with interfaces (ISP)
        $this->app->singleton(TicketServiceInterface::class, function ($app) {
            return new TicketService(
                $app->make(JiraClientInterface::class),
                config('jiravel.project_key'),
                $app->make(LoggingService::class)
            );
        });

        $this->app->singleton(CommentServiceInterface::class, function ($app) {
            return new CommentService(
                $app->make(JiraClientInterface::class),
                $app->make(LoggingService::class)
            );
        });

        $this->app->singleton(StatusServiceInterface::class, function ($app) {
            return new StatusService(
                $app->make(JiraClientInterface::class),
                $app->make(LoggingService::class)
            );
        });

        // Register main manager
        $this->app->singleton('jiravel', function ($app) {
            return new JiravelManager($app);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/jiravel.php' => config_path('jiravel.php'),
            ], 'jiravel-config');
        }
    }
}
