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
use Base3\MessagingFoundation\Dto\MessageDeliveryResult;
use Base3\MessagingFoundation\Dto\QueuedMessage;

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
