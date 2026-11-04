<?php declare(strict_types=1);

namespace MessagingFoundation\Test\Event;

use MessagingFoundation\Event\MessageQueuedEvent;
use PHPUnit\Framework\TestCase;

final class MessageQueuedEventTest extends TestCase {

	public function testQueueIdentifierIsExposedUnchanged(): void {
		$event = new MessageQueuedEvent('queue-100');

		$this->assertSame('queue-100', $event->getQueueId());
	}

	public function testEmptyIdentifierIsPreserved(): void {
		$event = new MessageQueuedEvent('');

		$this->assertSame('', $event->getQueueId());
	}

	public function testIdentifiersWithSeparatorsArePreserved(): void {
		$event = new MessageQueuedEvent('customer/a:123');

		$this->assertSame('customer/a:123', $event->getQueueId());
	}
}
