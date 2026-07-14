<?php declare(strict_types=1);

namespace MessagingFoundation\Api;

use MessagingFoundation\Dto\MessageVariant;

interface IMessageVariantRepository {

	public function ensureStorage(): void;

	public function save(MessageVariant $variant): void;

	public function getById(string $id): ?MessageVariant;

	public function getForTemplate(string $templateId, string $language): ?MessageVariant;

	public function getFallbackForTemplate(string $templateId): ?MessageVariant;

	public function delete(string $id): void;

	/**
	 * @return array<int,MessageVariant>
	 */
	public function listByTemplate(string $templateId): array;

	/**
	 * @return array{rows:array<int,array<string,mixed>>,total:int}
	 */
	public function page(array $request): array;
}
