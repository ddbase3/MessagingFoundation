<?php declare(strict_types=1);

namespace MessagingFoundation\Api;

use Base3\Api\IBase;
use Base3\Api\ISchemaProvider;

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
