<?php declare(strict_types=1);

namespace MessagingFoundation\Api;

use MessagingFoundation\Dto\Message;
use MessagingFoundation\Dto\MessageDeliveryResult;
use MessagingFoundation\Dto\QueuedMessage;

interface IMessageDeliveryRepository {

	public function ensureStorage(): void;

	public function create(QueuedMessage $queuedMessage, Message $message): string;

	public function finish(string $deliveryId, MessageDeliveryResult $result): void;

	/**
	 * @return array{rows:array<int,array<string,mixed>>,total:int}
	 */
	public function page(array $request): array;

	/**
	 * @return array<string,mixed>|null
	 */
	public function detail(string $deliveryId): ?array;

	public function cleanup(int $retentionDays): int;
}
