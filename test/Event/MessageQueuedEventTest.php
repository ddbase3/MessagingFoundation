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

use Base3\MessagingFoundation\Event\MessageQueuedEvent;
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
