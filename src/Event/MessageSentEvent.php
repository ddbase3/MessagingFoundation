<?php declare(strict_types=1);

namespace MessagingFoundation\Event;

final class MessageSentEvent {

	public function __construct(
		private readonly string $queueId,
		private readonly string $deliveryId
	) {}

	public function getQueueId(): string { return $this->queueId; }
	public function getDeliveryId(): string { return $this->deliveryId; }
}
