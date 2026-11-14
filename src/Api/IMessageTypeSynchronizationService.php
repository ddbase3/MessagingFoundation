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
