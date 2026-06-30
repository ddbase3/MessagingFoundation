<?php declare(strict_types=1);

namespace MessagingFoundation\Event;

final class MessageQueuedEvent {

	public function __construct(
		private readonly string $queueId
	) {}

	public function getQueueId(): string {
		return $this->queueId;
	}
}
