<?php declare(strict_types=1);

namespace MessagingFoundation\Test\Event;

use MessagingFoundation\Event\MessageFailedEvent;
use PHPUnit\Framework\TestCase;

final class MessageFailedEventTest extends TestCase {

	public function testAllFailureFieldsAreAccessible(): void {
		$event = new MessageFailedEvent('queue-1', 'delivery-3', 'Transport offline');

		$this->assertSame('queue-1', $event->getQueueId());
		$this->assertSame('delivery-3', $event->getDeliveryId());
		$this->assertSame('Transport offline', $event->getErrorMessage());
	}

	public function testNoDeliveryIdIsRepresentedByEmptyString(): void {
		$event = new MessageFailedEvent('queue-2', '', 'Failed before delivery');

		$this->assertSame('queue-2', $event->getQueueId());
		$this->assertSame('', $event->getDeliveryId());
		$this->assertSame('Failed before delivery', $event->getErrorMessage());
	}

	public function testErrorMessageIsPreservedWithoutTrimming(): void {
		$event = new MessageFailedEvent('', '', " failure\nstack line ");

		$this->assertSame('', $event->getQueueId());
		$this->assertSame('', $event->getDeliveryId());
		$this->assertSame(" failure\nstack line ", $event->getErrorMessage());
	}
}
