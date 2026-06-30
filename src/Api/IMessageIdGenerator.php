<?php declare(strict_types=1);

namespace MessagingFoundation\Api;

interface IMessageIdGenerator {

	public function createId(string $prefix): string;
}
