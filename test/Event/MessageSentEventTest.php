<?php declare(strict_types=1);

namespace MessagingFoundation\Test\Event;

use MessagingFoundation\Event\MessageSentEvent;
use PHPUnit\Framework\TestCase;

final class MessageSentEventTest extends TestCase {

	public function testQueueAndDeliveryIdentifiersAreIndependent(): void {
		$event = new MessageSentEvent('queue-1', 'delivery-5');

		$this->assertSame('queue-1', $event->getQueueId());
		$this->assertSame('delivery-5', $event->getDeliveryId());
	}

	public function testEmptyIdentifiersAreRetained(): void {
		$event = new MessageSentEvent('', '');

		$this->assertSame('', $event->getQueueId());
		$this->assertSame('', $event->getDeliveryId());
	}

	public function testDeliveryIdIsNotDerivedFromQueueId(): void {
		$event = new MessageSentEvent('queue-abc', 'remote:smtp/456');

		$this->assertSame('queue-abc', $event->getQueueId());
		$this->assertSame('remote:smtp/456', $event->getDeliveryId());
	}
}
