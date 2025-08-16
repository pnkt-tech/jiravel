<?php

declare(strict_types=1);

namespace Pnkt\Jiravel\DataTransferObjects;

final readonly class AttachmentData
{
    public function __construct(
        public string $filePath,
        public string $filename,
        public ?string $contentType = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            filePath: $data['file_path'],
            filename: $data['filename'],
            contentType: $data['content_type'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'file_path' => $this->filePath,
            'filename' => $this->filename,
            'content_type' => $this->contentType,
        ];
    }
}
