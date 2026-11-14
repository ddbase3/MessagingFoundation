<?php declare(strict_types=1);

/***********************************************************************
 * This file is part of MessagingFoundation for BASE3 Framework.
 *
 * MessagingFoundation defines protocol-neutral messaging contracts.
 * It provides shared APIs, DTOs, events and exceptions for BASE3.
 * Storage, delivery and queues belong to implementation plugins.
 *
 * Developed by Daniel Dahme
 * Licensed under GPL-3.0
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * https://base3.de
 * https://github.com/ddbase3/MessagingFoundation
 **********************************************************************/

namespace Base3\MessagingFoundation\Test\Event;

use Base3\MessagingFoundation\Event\MessageSentEvent;
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
