<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Contracts;

use Pnkt\Jiravel\Http\Requests\JiraRequest;
use Pnkt\Jiravel\Http\Responses\BaseJiraResponse;

interface JiraClientInterface
{
    public function send(JiraRequest $request): BaseJiraResponse;
    public function get(string $endpoint, array $params = []): BaseJiraResponse;
    public function post(string $endpoint, array $data = []): BaseJiraResponse;
    public function put(string $endpoint, array $data = []): BaseJiraResponse;
    public function delete(string $endpoint): BaseJiraResponse;
    public function upload(string $endpoint, string $filePath, string $filename): BaseJiraResponse;
}
