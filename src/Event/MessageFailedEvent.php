<?php declare(strict_types=1);

namespace MessagingFoundation\Event;

final class MessageFailedEvent {

	public function __construct(
		private readonly string $queueId,
		private readonly string $deliveryId,
		private readonly string $errorMessage
	) {}

	public function getQueueId(): string { return $this->queueId; }
	public function getDeliveryId(): string { return $this->deliveryId; }
	public function getErrorMessage(): string { return $this->errorMessage; }
}
