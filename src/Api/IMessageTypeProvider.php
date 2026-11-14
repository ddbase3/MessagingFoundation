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

use Base3\CoreFoundation\Api\IBase;
use Base3\CoreFoundation\Api\ISchemaProvider;

interface IMessageTypeProvider extends IBase, ISchemaProvider {

	public function getLabel(): string;

	public function getDescription(): string;

	public function getDefaultSubject(): string;

	public function getDefaultBodyText(): string;

	public function getDefaultBodyHtml(): string;

	/**
	 * @return array<int,array<string,mixed>>
	 */
	public function getPlaceholders(): array;
}
