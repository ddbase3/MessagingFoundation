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
use Base3\MessagingFoundation\Exception\MessageTransportNotFoundException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class MessageTransportNotFoundExceptionTest extends TestCase {

	public function testMissingTransportIsASpecializedMessageException(): void {
		$exception = new MessageTransportNotFoundException('Unknown transport');

		$this->assertInstanceOf(MessageException::class, $exception);
		$this->assertInstanceOf(RuntimeException::class, $exception);
		$this->assertSame('Unknown transport', $exception->getMessage());
	}

	public function testMissingTransportExceptionPreservesCodeAndCause(): void {
		$previous = new RuntimeException('Registry unavailable');
		$exception = new MessageTransportNotFoundException('Missing smtp', 404, $previous);

		$this->assertSame(404, $exception->getCode());
		$this->assertSame($previous, $exception->getPrevious());
	}

	public function testSpecializedExceptionCanBeCaughtByMessageExceptionContract(): void {
		$this->expectException(MessageException::class);
		$this->expectExceptionMessage('webhook missing');

		throw new MessageTransportNotFoundException('webhook missing');
	}
}
