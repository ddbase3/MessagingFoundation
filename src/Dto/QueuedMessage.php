<?php declare(strict_types=1);

namespace MessagingFoundation\Dto;

final class QueuedMessage {

	public function __construct(
		private readonly string $id,
		private readonly Message $message,
		private readonly string $transportName,
		private readonly string $status,
		private readonly int $attempts,
		private readonly int $maxAttempts
	) {}

	public function getId(): string { return $this->id; }
	public function getMessage(): Message { return $this->message; }
	public function getTransportName(): string { return $this->transportName; }
	public function getStatus(): string { return $this->status; }
	public function getAttempts(): int { return $this->attempts; }
	public function getMaxAttempts(): int { return $this->maxAttempts; }
}
