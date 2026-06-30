<?php declare(strict_types=1);

namespace MessagingFoundation\Api;

use Base3\Api\IBase;
use Base3\Api\ISchemaProvider;
use MessagingFoundation\Dto\Message;
use MessagingFoundation\Dto\MessageDeliveryResult;

interface IMessageTransport extends IBase, ISchemaProvider {

	public function getLabel(): string;

	public function supports(Message $message, array $settings = []): bool;

	public function send(Message $message, array $settings = []): MessageDeliveryResult;
}
