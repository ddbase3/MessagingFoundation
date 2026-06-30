<?php declare(strict_types=1);

namespace MessagingFoundation\Dto;

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
