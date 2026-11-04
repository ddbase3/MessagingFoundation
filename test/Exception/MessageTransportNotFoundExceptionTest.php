<?php declare(strict_types=1);

namespace MessagingFoundation\Test\Exception;

use MessagingFoundation\Exception\MessageException;
use MessagingFoundation\Exception\MessageTransportNotFoundException;
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
