<?php declare(strict_types=1);

namespace MessagingFoundation\Api;

use MessagingFoundation\Dto\Message;
use MessagingFoundation\Dto\QueuedMessage;

interface IMessageQueueService {

	public function enqueue(Message $message, string $transportName = '', int $priority = 100, ?int $notBefore = null): string;

	/**
	 * @return array<int,QueuedMessage>
	 */
	public function claimNext(int $limit = 20, int $lockSeconds = 300): array;

	public function markSent(string $queueId): void;

	public function markFailed(string $queueId, string $errorMessage, int $retryDelaySeconds = 300): void;

	public function cancel(string $queueId): void;
}
