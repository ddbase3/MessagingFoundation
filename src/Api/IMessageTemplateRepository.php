<?php declare(strict_types=1);

namespace MessagingFoundation\Api;

use MessagingFoundation\Dto\MessageTemplate;

interface IMessageTemplateRepository {

	public function ensureStorage(): void;

	public function save(MessageTemplate $template): void;

	public function getById(string $id): ?MessageTemplate;

	public function getByType(string $typeName, string $scopeType = 'global', string $scopeId = ''): ?MessageTemplate;

	public function delete(string $id): void;

	/**
	 * @return array<int,MessageTemplate>
	 */
	public function listAll(): array;

	/**
	 * @return array{rows:array<int,array<string,mixed>>,total:int}
	 */
	public function page(array $request): array;
}
