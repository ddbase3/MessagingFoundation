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

namespace Base3\MessagingFoundation\Dto;

final class QueuedMessage {

	public function __construct(
		private readonly string $id,
		private readonly Message $message,
		private readonly string $transportName,
		private readonly string $status,
		private readonly int $attempts,
		private readonly int $maxAttempts
	) {}

	public function getId(): string { return $this->id; }
	public function getMessage(): Message { return $this->message; }
	public function getTransportName(): string { return $this->transportName; }
	public function getStatus(): string { return $this->status; }
	public function getAttempts(): int { return $this->attempts; }
	public function getMaxAttempts(): int { return $this->maxAttempts; }
}
