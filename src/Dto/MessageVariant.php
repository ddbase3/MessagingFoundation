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

final class MessageVariant {

	public function __construct(
		private readonly string $id,
		private readonly string $templateId,
		private readonly string $language,
		private readonly string $subject,
		private readonly string $bodyText,
		private readonly string $bodyHtml = '',
		private readonly bool $enabled = true,
		private readonly bool $fallback = false
	) {}

	public function getId(): string { return $this->id; }
	public function getTemplateId(): string { return $this->templateId; }
	public function getLanguage(): string { return $this->language; }
	public function getSubject(): string { return $this->subject; }
	public function getBodyText(): string { return $this->bodyText; }
	public function getBodyHtml(): string { return $this->bodyHtml; }
	public function isEnabled(): bool { return $this->enabled; }
	public function isFallback(): bool { return $this->fallback; }
}
