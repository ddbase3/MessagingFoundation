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

use Base3\MessagingFoundation\Event\MessageFailedEvent;
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
