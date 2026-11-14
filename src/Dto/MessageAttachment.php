<?php declare(strict_types=1);

/***********************************************************************
 * This file is part of MessagingFoundation for BASE3 Framework.
 *
 * MessagingFoundation defines protocol-neutral messaging contracts.
 * It provides shared APIs, DTOs, events and exceptions for BASE3.
 * Storage, delivery and queues belong to implementation plugins.
 *
 * Developed by Daniel Dahme
 * Licensed under GPL-3.0
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * https://base3.de
 * https://github.com/ddbase3/MessagingFoundation
 **********************************************************************/

namespace Base3\MessagingFoundation\Dto;

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
