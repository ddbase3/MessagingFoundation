<?php declare(strict_types=1);

namespace MessagingFoundation\Dto;

final class Message {

	/**
	 * @param array<int, MessageAddress> $recipients
	 * @param array<int, MessageAttachment> $attachments
	 * @param array<string, mixed> $metadata
	 */
	public function __construct(
		private readonly string $typeName,
		private readonly string $subject,
		private readonly string $bodyText,
		private readonly string $bodyHtml = '',
		private readonly array $recipients = [],
		private readonly array $attachments = [],
		private readonly string $fromAddress = '',
		private readonly string $fromName = '',
		private readonly string $replyToAddress = '',
		private readonly string $replyToName = '',
		private readonly array $metadata = []
	) {}

	public function getTypeName(): string {
		return $this->typeName;
	}

	public function getSubject(): string {
		return $this->subject;
	}

	public function getBodyText(): string {
		return $this->bodyText;
	}

	public function getBodyHtml(): string {
		return $this->bodyHtml;
	}

	/**
	 * @return array<int, MessageAddress>
	 */
	public function getRecipients(): array {
		return $this->recipients;
	}

	/**
	 * @return array<int, MessageAttachment>
	 */
	public function getAttachments(): array {
		return $this->attachments;
	}

	public function getFromAddress(): string {
		return $this->fromAddress;
	}

	public function getFromName(): string {
		return $this->fromName;
	}

	public function getReplyToAddress(): string {
		return $this->replyToAddress;
	}

	public function getReplyToName(): string {
		return $this->replyToName;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getMetadata(): array {
		return $this->metadata;
	}

	/**
	 * @param array<int, MessageAddress> $recipients
	 */
	public function withRecipients(array $recipients): self {
		return new self(
			$this->typeName,
			$this->subject,
			$this->bodyText,
			$this->bodyHtml,
			$recipients,
			$this->attachments,
			$this->fromAddress,
			$this->fromName,
			$this->replyToAddress,
			$this->replyToName,
			$this->metadata
		);
	}

	/**
	 * @param array<int, MessageAttachment> $attachments
	 */
	public function withAttachments(array $attachments): self {
		return new self(
			$this->typeName,
			$this->subject,
			$this->bodyText,
			$this->bodyHtml,
			$this->recipients,
			$attachments,
			$this->fromAddress,
			$this->fromName,
			$this->replyToAddress,
			$this->replyToName,
			$this->metadata
		);
	}

	public function withSender(string $fromAddress, string $fromName = ''): self {
		return new self(
			$this->typeName,
			$this->subject,
			$this->bodyText,
			$this->bodyHtml,
			$this->recipients,
			$this->attachments,
			$fromAddress,
			$fromName,
			$this->replyToAddress,
			$this->replyToName,
			$this->metadata
		);
	}

	public function withReplyTo(string $replyToAddress, string $replyToName = ''): self {
		return new self(
			$this->typeName,
			$this->subject,
			$this->bodyText,
			$this->bodyHtml,
			$this->recipients,
			$this->attachments,
			$this->fromAddress,
			$this->fromName,
			$replyToAddress,
			$replyToName,
			$this->metadata
		);
	}

	/**
	 * @param array<string, mixed> $metadata
	 */
	public function withMetadata(array $metadata, bool $merge = true): self {
		return new self(
			$this->typeName,
			$this->subject,
			$this->bodyText,
			$this->bodyHtml,
			$this->recipients,
			$this->attachments,
			$this->fromAddress,
			$this->fromName,
			$this->replyToAddress,
			$this->replyToName,
			$merge ? array_merge($this->metadata, $metadata) : $metadata
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array {
		return [
			'type_name' => $this->typeName,
			'subject' => $this->subject,
			'body_text' => $this->bodyText,
			'body_html' => $this->bodyHtml,
			'recipients' => array_map(fn(MessageAddress $recipient) => $recipient->toArray(), $this->recipients),
			'attachments' => array_map(fn(MessageAttachment $attachment) => $attachment->toArray(), $this->attachments),
			'from_address' => $this->fromAddress,
			'from_name' => $this->fromName,
			'reply_to_address' => $this->replyToAddress,
			'reply_to_name' => $this->replyToName,
			'metadata' => $this->metadata
		];
	}

	public static function fromArray(array $data): self {
		$recipients = [];
		foreach(($data['recipients'] ?? []) as $recipient) {
			if(is_array($recipient)) {
				$recipients[] = MessageAddress::fromArray($recipient);
			}
		}

		$attachments = [];
		foreach(($data['attachments'] ?? []) as $attachment) {
			if(is_array($attachment)) {
				$attachments[] = MessageAttachment::fromArray($attachment);
			}
		}

		return new self(
			(string)($data['type_name'] ?? ''),
			(string)($data['subject'] ?? ''),
			(string)($data['body_text'] ?? ''),
			(string)($data['body_html'] ?? ''),
			$recipients,
			$attachments,
			(string)($data['from_address'] ?? ''),
			(string)($data['from_name'] ?? ''),
			(string)($data['reply_to_address'] ?? ''),
			(string)($data['reply_to_name'] ?? ''),
			is_array($data['metadata'] ?? null) ? $data['metadata'] : []
		);
	}
}
