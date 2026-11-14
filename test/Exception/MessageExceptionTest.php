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

namespace Base3\MessagingFoundation\Test\Exception;

use Base3\MessagingFoundation\Exception\MessageException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class MessageExceptionTest extends TestCase {

	public function testMessageExceptionIsARuntimeException(): void {
		$exception = new MessageException('Message could not be queued');

		$this->assertInstanceOf(RuntimeException::class, $exception);
		$this->assertSame('Message could not be queued', $exception->getMessage());
		$this->assertSame(0, $exception->getCode());
	}

	public function testExceptionPreservesCodeAndPreviousThrowable(): void {
		$previous = new RuntimeException('Broken connection');
		$exception = new MessageException('Queue unavailable', 503, $previous);

		$this->assertSame('Queue unavailable', $exception->getMessage());
		$this->assertSame(503, $exception->getCode());
		$this->assertSame($previous, $exception->getPrevious());
	}

	public function testMessageExceptionCanBeCaughtAsRuntimeException(): void {
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Queue failure');

		throw new MessageException('Queue failure');
	}
}
