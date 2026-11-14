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

final class MessageDeliveryResult {

	/**
	 * @param array<string,mixed> $details
	 */
	public function __construct(
		private readonly bool $success,
		private readonly string $message = '',
		private readonly string $externalId = '',
		private readonly array $details = []
	) {}

	public function isSuccess(): bool { return $this->success; }
	public function getMessage(): string { return $this->message; }
	public function getExternalId(): string { return $this->externalId; }
	/** @return array<string,mixed> */
	public function getDetails(): array { return $this->details; }

	/**
	 * @return array<string,mixed>
	 */
	public function toArray(): array {
		return [
			'success' => $this->success,
			'message' => $this->message,
			'external_id' => $this->externalId,
			'details' => $this->details
		];
	}
}
