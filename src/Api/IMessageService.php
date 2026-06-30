<?php declare(strict_types=1);

namespace MessagingFoundation\Api;

use MessagingFoundation\Dto\Message;

interface IMessageService {

	public function enqueue(Message $message, string $transportName = '', int $priority = 100, ?int $notBefore = null): string;

	public function sendNow(Message $message, string $transportName = ''): string;
}
