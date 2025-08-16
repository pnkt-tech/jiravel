<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Contracts;

use Pnkt\Jiravel\Http\Requests\JiraRequest;
use Pnkt\Jiravel\Http\Responses\JiraResponse;

interface JiraClientInterface
{
    public function send(JiraRequest $request): JiraResponse;
    public function get(string $endpoint, array $params = []): JiraResponse;
    public function post(string $endpoint, array $data = []): JiraResponse;
    public function put(string $endpoint, array $data = []): JiraResponse;
    public function delete(string $endpoint): JiraResponse;
    public function upload(string $endpoint, string $filePath, string $filename): JiraResponse;
}
