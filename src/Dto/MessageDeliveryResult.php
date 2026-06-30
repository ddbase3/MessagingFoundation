<?php declare(strict_types=1);

namespace MessagingFoundation\Dto;

final class MessageDeliveryResult {

	/**
	 * @param array<string,mixed> $details
	 */
	public function __construct(
		private readonly bool $success,
		private readonly string $message = '',
		private readonly string $externalId = '',
		private readonly array $details = []
	) {}

	public function isSuccess(): bool { return $this->success; }
	public function getMessage(): string { return $this->message; }
	public function getExternalId(): string { return $this->externalId; }
	/** @return array<string,mixed> */
	public function getDetails(): array { return $this->details; }

	/**
	 * @return array<string,mixed>
	 */
	public function toArray(): array {
		return [
			'success' => $this->success,
			'message' => $this->message,
			'external_id' => $this->externalId,
			'details' => $this->details
		];
	}
}
