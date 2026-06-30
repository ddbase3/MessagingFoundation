<?php declare(strict_types=1);

namespace MessagingFoundation\Api;

use MessagingFoundation\Dto\Message;

interface IMessageRenderer {

	/**
	 * @param array<string,mixed> $context
	 */
	public function render(string $typeName, string $language, array $context = [], string $transportName = ''): Message;
}
