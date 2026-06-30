<?php declare(strict_types=1);

namespace MessagingFoundation\Api;

use MessagingFoundation\Dto\Message;
use MessagingFoundation\Dto\QueuedMessage;

interface IMessageQueueRepository {

	public function ensureStorage(): void;

	public function insert(Message $message, string $transportName, int $priority, ?int $notBefore): string;

	/**
	 * @return array<int,QueuedMessage>
	 */
	public function claimNext(int $limit, int $lockSeconds): array;

	public function markSent(string $queueId): void;

	public function markFailed(string $queueId, string $errorMessage, int $retryDelaySeconds): void;

	public function cancel(string $queueId): void;

	/**
	 * @return array{rows:array<int,array<string,mixed>>,total:int}
	 */
	public function page(array $request): array;
}
