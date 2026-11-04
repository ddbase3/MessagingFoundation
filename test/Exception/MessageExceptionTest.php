<?php declare(strict_types=1);

namespace MessagingFoundation\Test\Exception;

use MessagingFoundation\Exception\MessageException;
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
