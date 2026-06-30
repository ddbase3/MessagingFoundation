<?php declare(strict_types=1);

namespace MessagingFoundation\Api;

interface IMessageTransportRegistry {

	/**
	 * @return array<string,IMessageTransport>
	 */
	public function getTransports(): array;

	public function getTransport(string $name = ''): ?IMessageTransport;

	public function getDefaultTransportName(): string;

	/**
	 * @return array<string,mixed>
	 */
	public function getTransportSettings(string $name): array;
}
