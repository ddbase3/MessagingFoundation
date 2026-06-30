<?php declare(strict_types=1);

namespace MessagingFoundation\Dto;

final class MessageVariant {

	public function __construct(
		private readonly string $id,
		private readonly string $templateId,
		private readonly string $language,
		private readonly string $subject,
		private readonly string $bodyText,
		private readonly string $bodyHtml = '',
		private readonly bool $enabled = true
	) {}

	public function getId(): string { return $this->id; }
	public function getTemplateId(): string { return $this->templateId; }
	public function getLanguage(): string { return $this->language; }
	public function getSubject(): string { return $this->subject; }
	public function getBodyText(): string { return $this->bodyText; }
	public function getBodyHtml(): string { return $this->bodyHtml; }
	public function isEnabled(): bool { return $this->enabled; }
}
