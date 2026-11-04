<?php declare(strict_types=1);

namespace MessagingFoundation\Test\Dto;

use MessagingFoundation\Dto\Message;
use MessagingFoundation\Dto\QueuedMessage;
use PHPUnit\Framework\TestCase;

final class QueuedMessageTest extends TestCase {

	public function testAllGettersPreserveQueueStateAndMessageIdentity(): void {
		$message = new Message('notice', 'Subject', 'Body');
		$queued = new QueuedMessage('queue-42', $message, 'smtp', 'pending', 2, 5);

		$this->assertSame('queue-42', $queued->getId());
		$this->assertSame($message, $queued->getMessage());
		$this->assertSame('smtp', $queued->getTransportName());
		$this->assertSame('pending', $queued->getStatus());
		$this->assertSame(2, $queued->getAttempts());
		$this->assertSame(5, $queued->getMaxAttempts());
	}

	public function testZeroAttemptsAndEmptyTransportArePreserved(): void {
		$message = new Message('notice', '', '');
		$queued = new QueuedMessage('queue-new', $message, '', 'queued', 0, 0);

		$this->assertSame('', $queued->getTransportName());
		$this->assertSame(0, $queued->getAttempts());
		$this->assertSame(0, $queued->getMaxAttempts());
		$this->assertSame($message, $queued->getMessage());
	}

	public function testTheQueueDtoDoesNotCopyOrSerializeItsMessage(): void {
		$message = new Message('type', '', 'payload');
		$queued = new QueuedMessage('id', $message, 'webhook', 'sent', 1, 1);

		$this->assertSame($message, $queued->getMessage());
		$this->assertSame('payload', $queued->getMessage()->getBodyText());
	}

	public function testQueueStatusAndRetryCountsAreRetainedVerbatim(): void {
		$queued = new QueuedMessage('queue-x', new Message('type', '', ''), 'external', 'custom-status', 10, 3);

		$this->assertSame('custom-status', $queued->getStatus());
		$this->assertSame(10, $queued->getAttempts());
		$this->assertSame(3, $queued->getMaxAttempts());
	}
}
