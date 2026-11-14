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

use Base3\MessagingFoundation\Dto\MessageVariant;

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
