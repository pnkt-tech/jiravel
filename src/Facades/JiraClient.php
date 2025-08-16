<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Facades;

use Illuminate\Support\Facades\Facade;
use Pnkt\Jiravel\Contracts\JiraClientInterface;
use Pnkt\Jiravel\Http\Requests\JiraRequest;
use Pnkt\Jiravel\Http\Responses\JiraResponse;

/**
 * @method static JiraResponse send(JiraRequest $request)
 * @method static JiraResponse get(string $endpoint, array $params = [])
 * @method static JiraResponse post(string $endpoint, array $data = [])
 * @method static JiraResponse put(string $endpoint, array $data = [])
 * @method static JiraResponse delete(string $endpoint)
 * @method static JiraResponse upload(string $endpoint, string $filePath, string $filename)
 */
class JiraClient extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return JiraClientInterface::class;
    }
}
