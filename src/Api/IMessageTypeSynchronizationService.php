<?php declare(strict_types=1);

namespace MessagingFoundation\Api;

interface IMessageTypeSynchronizationService {

	/**
	 * @return array<string, mixed>
	 */
	public function syncAll(string $language = 'en'): array;

	/**
	 * @return array<string, mixed>
	 */
	public function syncOne(string $typeName, string $language = 'en'): array;

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public function getProviderSummaries(): array;
}
