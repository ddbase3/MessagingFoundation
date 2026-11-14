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

final class MessageAddress {

	public function __construct(
		private readonly string $type,
		private readonly string $address,
		private readonly string $name = ''
	) {}

	public function getType(): string {
		return $this->type;
	}

	public function getAddress(): string {
		return $this->address;
	}

	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function toArray(): array {
		return [
			'type' => $this->type,
			'address' => $this->address,
			'name' => $this->name
		];
	}

	public static function fromArray(array $data): self {
		return new self(
			(string)($data['type'] ?? 'to'),
			(string)($data['address'] ?? ''),
			(string)($data['name'] ?? '')
		);
	}
}
