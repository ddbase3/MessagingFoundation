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

final class MessageTemplate {

	public function __construct(
		private readonly string $id,
		private readonly string $typeName,
		private readonly string $label,
		private readonly string $description = '',
		private readonly string $scopeType = 'global',
		private readonly string $scopeId = '',
		private readonly string $defaultTransport = '',
		private readonly bool $enabled = true
	) {}

	public function getId(): string { return $this->id; }
	public function getTypeName(): string { return $this->typeName; }
	public function getLabel(): string { return $this->label; }
	public function getDescription(): string { return $this->description; }
	public function getScopeType(): string { return $this->scopeType; }
	public function getScopeId(): string { return $this->scopeId; }
	public function getDefaultTransport(): string { return $this->defaultTransport; }
	public function isEnabled(): bool { return $this->enabled; }
}
