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

use Base3\MessagingFoundation\Dto\MessageTemplate;

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
