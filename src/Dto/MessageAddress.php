<?php declare(strict_types=1);

namespace MessagingFoundation\Dto;

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
