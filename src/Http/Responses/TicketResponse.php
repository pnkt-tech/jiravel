<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\Http\Responses;

use Pnkt\Jiravel\DataTransferObjects\TicketDetails;

final readonly class TicketResponse extends JiraResponse
{
    public function getTicketDetails(): TicketDetails
    {
        return TicketDetails::fromArray($this->data);
    }

    public function getTicketKey(): string
    {
        return $this->data['key'] ?? '';
    }

    public function getTicketId(): string
    {
        return $this->data['id'] ?? '';
    }

    public function getSummary(): string
    {
        return $this->data['fields']['summary'] ?? '';
    }

    public function getDescription(): string
    {
        $description = $this->data['fields']['description'] ?? null;
        
        if ($description === null) {
            return '';
        }

        // Extract text from Atlassian Document Format
        if (isset($description['content'])) {
            return $this->extractTextFromContent($description['content']);
        }

        return (string) $description;
    }

    public function getStatus(): string
    {
        return $this->data['fields']['status']['name'] ?? '';
    }

    public function getAssignee(): ?string
    {
        return $this->data['fields']['assignee']['name'] ?? null;
    }

    public function getReporter(): string
    {
        return $this->data['fields']['reporter']['name'] ?? '';
    }

    public function getIssueType(): string
    {
        return $this->data['fields']['issuetype']['name'] ?? '';
    }

    public function getPriority(): string
    {
        return $this->data['fields']['priority']['name'] ?? '';
    }

    public function getLabels(): array
    {
        return $this->data['fields']['labels'] ?? [];
    }

    public function getComponents(): array
    {
        $components = $this->data['fields']['components'] ?? [];
        return array_map(fn($component) => $component['name'], $components);
    }

    public function getCreatedDate(): string
    {
        return $this->data['fields']['created'] ?? '';
    }

    public function getUpdatedDate(): string
    {
        return $this->data['fields']['updated'] ?? '';
    }

    private function extractTextFromContent(array $content): string
    {
        $text = '';
        
        foreach ($content as $block) {
            if (isset($block['content'])) {
                foreach ($block['content'] as $item) {
                    if (isset($item['text'])) {
                        $text .= $item['text'];
                    }
                }
            }
        }
        
        return $text;
    }
}
