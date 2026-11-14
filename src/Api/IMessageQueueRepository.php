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

namespace Base3\MessagingFoundation\Api;

use Base3\MessagingFoundation\Dto\Message;
use Base3\MessagingFoundation\Dto\QueuedMessage;

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
