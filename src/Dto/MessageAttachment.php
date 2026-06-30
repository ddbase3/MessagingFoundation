<?php declare(strict_types=1);

namespace MessagingFoundation\Dto;

final class MessageAttachment {

	public function __construct(
		private readonly string $path,
		private readonly string $name = '',
		private readonly string $mimeType = '',
		private readonly bool $inline = false,
		private readonly string $contentId = ''
	) {}

	public function getPath(): string {
		return $this->path;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getMimeType(): string {
		return $this->mimeType;
	}

	public function isInline(): bool {
		return $this->inline;
	}

	public function getContentId(): string {
		return $this->contentId;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function toArray(): array {
		return [
			'path' => $this->path,
			'name' => $this->name,
			'mime_type' => $this->mimeType,
			'inline' => $this->inline,
			'content_id' => $this->contentId
		];
	}

	public static function fromArray(array $data): self {
		return new self(
			(string)($data['path'] ?? ''),
			(string)($data['name'] ?? ''),
			(string)($data['mime_type'] ?? ''),
			(bool)($data['inline'] ?? false),
			(string)($data['content_id'] ?? '')
		);
	}
}
