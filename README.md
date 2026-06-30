# MessagingFoundation

MessagingFoundation is the protocol-neutral contract plugin for BASE3 messaging.

It defines the public APIs, DTOs, events and exceptions used by messaging implementations and consumer plugins. It does **not** implement message storage, queue processing, rendering persistence, delivery logs, transport logic or host-specific integrations.

The concrete implementation is expected to be provided by a separate plugin such as `MessageHub`.

---

## 1. Purpose

MessagingFoundation provides stable messaging abstractions for BASE3 plugins.

It exists so that consumer plugins can depend on one small, neutral contract layer instead of depending on a concrete implementation such as `MessageHub`, `Base3IliasLab`, PHPMailer, ILIAS mail APIs, SMTP libraries, webhook clients or other host-specific components.

A consumer plugin should be able to say:

```php
use MessagingFoundation\Api\IMessageService;
use MessagingFoundation\Api\IMessageRenderer;
use MessagingFoundation\Dto\MessageAddress;
```

without knowing how the message is queued, delivered, retried or logged.

MessagingFoundation defines:

* service interfaces
* repository interfaces
* transport interfaces
* message type provider interfaces
* immutable message DTOs
* message events
* base exceptions

MessagingFoundation intentionally does not define:

* database tables
* SQL schema
* queue implementation
* worker jobs
* administration displays
* PHPMailer integration
* SMTP implementation
* ILIAS mail integration
* template editor UI
* delivery log UI
* runtime configuration UI

---

## 2. Architectural role

MessagingFoundation is a foundation plugin.

Its role is to define contracts that other BASE3 plugins can share.

Typical plugin split:

```text
MessagingFoundation
  Defines interfaces, DTOs, events and exceptions.

MessageHub
  Implements templates, variants, rendering, queue, delivery logs, workers, checks and admin displays.

Base3IliasLab
  Provides project-specific transport decisions, such as PHPMailer.

Consumer plugins
  Provide message types and use IMessageService / IMessageRenderer.
```

Dependency direction:

```text
ConsumerPlugin -> MessagingFoundation
MessageHub -> MessagingFoundation
Base3IliasLab -> MessagingFoundation
```

MessagingFoundation should not depend on any of these:

```text
MessageHub
Base3Ilias
Base3IliasLab
ILIAS
PHPMailer
MissionBay
ClientStack
consumer plugins
```

This keeps the messaging contract stable and reusable.

---

## 3. Directory structure

Typical structure:

```text
MessagingFoundation/
├── README.md
├── VERSION
└── src/
    ├── MessagingFoundationPlugin.php
    ├── Api/
    │   ├── IMessageDeliveryRepository.php
    │   ├── IMessageIdGenerator.php
    │   ├── IMessageQueueRepository.php
    │   ├── IMessageQueueService.php
    │   ├── IMessageRenderer.php
    │   ├── IMessageService.php
    │   ├── IMessageTemplateRepository.php
    │   ├── IMessageTransport.php
    │   ├── IMessageTransportRegistry.php
    │   ├── IMessageTypeProvider.php
    │   ├── IMessageTypeSynchronizationService.php
    │   └── IMessageVariantRepository.php
    ├── Dto/
    │   ├── Message.php
    │   ├── MessageAddress.php
    │   ├── MessageAttachment.php
    │   ├── MessageDeliveryResult.php
    │   ├── MessageTemplate.php
    │   ├── MessageVariant.php
    │   └── QueuedMessage.php
    ├── Event/
    │   ├── MessageFailedEvent.php
    │   ├── MessageQueuedEvent.php
    │   └── MessageSentEvent.php
    └── Exception/
        ├── MessageException.php
        └── MessageTransportNotFoundException.php
```

Only PHP classes under `src/` are relevant for BASE3 discovery.

---

## 4. Plugin class

The plugin class is:

```php
MessagingFoundation\MessagingFoundationPlugin
```

Technical name:

```text
messagingfoundationplugin
```

The plugin itself is intentionally lightweight. It mainly makes the foundation plugin visible to the BASE3 runtime.

Example:

```php
<?php declare(strict_types=1);

namespace MessagingFoundation;

use Base3\Api\IContainer;
use Base3\Api\IPlugin;

final class MessagingFoundationPlugin implements IPlugin {

	public function __construct(
		private readonly IContainer $container
	) {}

	public static function getName(): string {
		return 'messagingfoundationplugin';
	}

	public function init() {
		$this->container->set(
			self::getName(),
			$this,
			IContainer::SHARED
		);
	}
}
```

MessagingFoundation should not register concrete messaging services. That is the job of an implementation plugin such as `MessageHub`.

---

## 5. Main concepts

### 5.1 Message

A `Message` is the runtime representation of a message to be queued or delivered.

Class:

```php
MessagingFoundation\Dto\Message
```

A message contains:

* message type name
* subject
* plain text body
* HTML body
* recipients
* attachments
* sender
* reply-to
* metadata

The DTO is immutable. Modification helpers return a new instance:

```php
$message = $message->withRecipient(
	new MessageAddress('to', 'jane@example.org', 'Jane Doe')
);
```

Common helpers:

```php
withRecipient(MessageAddress $recipient): self
withRecipients(array $recipients): self
withAttachment(MessageAttachment $attachment): self
withAttachments(array $attachments): self
withSender(string $fromAddress, string $fromName = ''): self
withReplyTo(string $replyToAddress, string $replyToName = ''): self
withMetadata(array $metadata, bool $merge = true): self
```

### 5.2 Message address

Class:

```php
MessagingFoundation\Dto\MessageAddress
```

Represents one recipient address.

Typical recipient kinds:

```text
to
cc
bcc
```

Example:

```php
new MessageAddress('to', 'jane@example.org', 'Jane Doe');
```

### 5.3 Message attachment

Class:

```php
MessagingFoundation\Dto\MessageAttachment
```

Represents an attachment reference.

It stores:

* file path
* display name
* MIME type
* inline flag
* content ID

MessagingFoundation does not define how attachments are stored. It only defines how a message can reference them.

Example:

```php
new MessageAttachment(
	'/path/to/report.pdf',
	'report.pdf',
	'application/pdf'
);
```

Inline example:

```php
new MessageAttachment(
	'/path/to/logo.png',
	'logo.png',
	'image/png',
	true,
	'logo'
);
```

### 5.4 Template

Class:

```php
MessagingFoundation\Dto\MessageTemplate
```

A template describes a message type and scope.

It contains:

* ID
* type name
* label
* description
* scope type
* scope ID
* default transport
* enabled flag

The concrete storage is implementation-specific.

### 5.5 Variant

Class:

```php
MessagingFoundation\Dto\MessageVariant
```

A variant contains renderable content for a template, usually per language.

It contains:

* ID
* template ID
* language
* subject
* plain text body
* HTML body
* enabled flag

### 5.6 Queued message

Class:

```php
MessagingFoundation\Dto\QueuedMessage
```

Represents a message after it has been placed into a queue.

It contains:

* queue ID
* message DTO
* transport name
* status
* attempt count
* maximum attempts

### 5.7 Delivery result

Class:

```php
MessagingFoundation\Dto\MessageDeliveryResult
```

Represents the result returned by a transport.

It contains:

* success flag
* message
* external ID
* result details

Example:

```php
return new MessageDeliveryResult(
	true,
	'Message sent.',
	'provider-id-123',
	[
		'transport' => 'phpmailer',
		'mode' => 'smtp'
	]
);
```

---

## 6. Service interfaces

### 6.1 `IMessageService`

High-level message service used by consumer plugins.

```php
MessagingFoundation\Api\IMessageService
```

Methods:

```php
public function enqueue(
	Message $message,
	string $transportName = '',
	int $priority = 100,
	?int $notBefore = null
): string;

public function sendNow(
	Message $message,
	string $transportName = ''
): string;
```

Use `enqueue()` for normal asynchronous delivery.

Use `sendNow()` only when immediate processing is explicitly required.

Example:

```php
$queueId = $messageService->enqueue($message);
```

Specific transport:

```php
$queueId = $messageService->enqueue($message, 'phpmailer');
```

### 6.2 `IMessageQueueService`

Queue-level service.

```php
MessagingFoundation\Api\IMessageQueueService
```

Methods:

```php
public function enqueue(Message $message, string $transportName = '', int $priority = 100, ?int $notBefore = null): string;

public function claimNext(int $limit = 20, int $lockSeconds = 300): array;

public function markSent(string $queueId): void;

public function markFailed(string $queueId, string $errorMessage, int $retryDelaySeconds = 300): void;

public function cancel(string $queueId): void;
```

Implementation plugins use this internally for queue handling.

Consumer plugins usually use `IMessageService` instead.

### 6.3 `IMessageRenderer`

Renders a message type and context into a `Message` DTO.

```php
MessagingFoundation\Api\IMessageRenderer
```

Method:

```php
public function render(
	string $typeName,
	string $language,
	array $context = [],
	string $transportName = ''
): Message;
```

Example:

```php
$message = $messageRenderer->render(
	'examplewelcomemessage',
	'en',
	[
		'name' => 'Jane Doe',
		'system_name' => 'BASE3'
	]
);
```

The renderer does not need to know recipients. Recipients can be attached afterwards:

```php
$message = $message->withRecipient(
	new MessageAddress('to', 'jane@example.org', 'Jane Doe')
);
```

### 6.4 `IMessageTransportRegistry`

Looks up available transports.

```php
MessagingFoundation\Api\IMessageTransportRegistry
```

Methods:

```php
public function getTransports(): array;

public function getTransport(string $name = ''): ?IMessageTransport;

public function getDefaultTransportName(): string;

public function getTransportSettings(string $name): array;
```

Implementation plugins use this to route messages to real transports.

### 6.5 `IMessageTypeSynchronizationService`

Synchronizes discoverable message type providers into implementation-specific templates and variants.

```php
MessagingFoundation\Api\IMessageTypeSynchronizationService
```

Methods:

```php
public function synchronizeAll(
	bool $createVariants = true,
	string $language = 'en'
): array;

public function synchronizeProvider(
	IMessageTypeProvider $provider,
	bool $createVariants = true,
	string $language = 'en'
): array;
```

An implementation such as MessageHub can use this to create missing templates and variants for discovered message types.

---

## 7. Repository interfaces

MessagingFoundation defines repository interfaces, but does not implement storage.

Concrete implementations live in an implementation plugin such as `MessageHub`.

### 7.1 `IMessageTemplateRepository`

```php
MessagingFoundation\Api\IMessageTemplateRepository
```

Responsible for storing and retrieving `MessageTemplate` DTOs.

Important methods:

```php
ensureStorage(): void
save(MessageTemplate $template): void
getById(string $id): ?MessageTemplate
getByType(string $typeName, string $scopeType = 'global', string $scopeId = ''): ?MessageTemplate
delete(string $id): void
listAll(): array
page(array $request): array
```

### 7.2 `IMessageVariantRepository`

```php
MessagingFoundation\Api\IMessageVariantRepository
```

Responsible for storing and retrieving `MessageVariant` DTOs.

Important methods:

```php
ensureStorage(): void
save(MessageVariant $variant): void
getById(string $id): ?MessageVariant
getForTemplate(string $templateId, string $language): ?MessageVariant
delete(string $id): void
listByTemplate(string $templateId): array
page(array $request): array
```

### 7.3 `IMessageQueueRepository`

```php
MessagingFoundation\Api\IMessageQueueRepository
```

Responsible for queue storage.

Important methods:

```php
ensureStorage(): void
insert(Message $message, string $transportName, int $priority, ?int $notBefore): string
claimNext(int $limit, int $lockSeconds): array
markSent(string $queueId): void
markFailed(string $queueId, string $errorMessage, int $retryDelaySeconds): void
cancel(string $queueId): void
page(array $request): array
```

### 7.4 `IMessageDeliveryRepository`

```php
MessagingFoundation\Api\IMessageDeliveryRepository
```

Responsible for delivery log storage.

Important methods:

```php
ensureStorage(): void
create(QueuedMessage $queuedMessage, Message $message): string
finish(string $deliveryId, MessageDeliveryResult $result): void
page(array $request): array
detail(string $deliveryId): ?array
cleanup(int $retentionDays): int
```

---

## 8. Transport interface

A transport sends a rendered message.

Interface:

```php
MessagingFoundation\Api\IMessageTransport
```

It extends BASE3 discoverability and schema capabilities.

Methods:

```php
public static function getName(): string;

public function getLabel(): string;

public function supports(Message $message, array $settings = []): bool;

public function send(Message $message, array $settings = []): MessageDeliveryResult;

public function getSchema(): array;
```

Example transport names:

```text
log
phpmailer
webhook
sms
push
teams
matrix
```

Transport names should be stable, lowercase technical identifiers.

### 8.1 Example transport skeleton

```php
<?php declare(strict_types=1);

namespace ExamplePlugin\Transport;

use MessagingFoundation\Api\IMessageTransport;
use MessagingFoundation\Dto\Message;
use MessagingFoundation\Dto\MessageDeliveryResult;

final class ExampleTransport implements IMessageTransport {

	public static function getName(): string {
		return 'example';
	}

	public function getLabel(): string {
		return 'Example transport';
	}

	public function supports(Message $message, array $settings = []): bool {
		return true;
	}

	public function send(Message $message, array $settings = []): MessageDeliveryResult {
		// Perform delivery here.

		return new MessageDeliveryResult(
			true,
			'Message sent by example transport.',
			'',
			[
				'transport' => self::getName()
			]
		);
	}

	public function getSchema(): array {
		return [
			'type' => 'object',
			'properties' => [
				'enabled' => ['type' => 'boolean']
			]
		];
	}
}
```

---

## 9. Message type providers

A message type provider describes one kind of message.

Interface:

```php
MessagingFoundation\Api\IMessageTypeProvider
```

Methods:

```php
public static function getName(): string;

public function getLabel(): string;

public function getDescription(): string;

public function getDefaultSubject(): string;

public function getDefaultBodyText(): string;

public function getDefaultBodyHtml(): string;

public function getPlaceholders(): array;

public function getSchema(): array;
```

Message type providers are usually implemented by consumer plugins.

### 9.1 Example provider

```php
<?php declare(strict_types=1);

namespace ExamplePlugin\Message;

use MessagingFoundation\Api\IMessageTypeProvider;

final class ExampleWelcomeMessageTypeProvider implements IMessageTypeProvider {

	public static function getName(): string {
		return 'examplewelcomemessage';
	}

	public function getLabel(): string {
		return 'Example welcome message';
	}

	public function getDescription(): string {
		return 'Welcome message sent by ExamplePlugin.';
	}

	public function getDefaultSubject(): string {
		return 'Welcome, {{name}}';
	}

	public function getDefaultBodyText(): string {
		return "Hello {{name}},\n\nwelcome to {{system_name}}.";
	}

	public function getDefaultBodyHtml(): string {
		return '<p>Hello {{name}},</p><p>welcome to <strong>{{system_name}}</strong>.</p>';
	}

	public function getPlaceholders(): array {
		return [
			[
				'name' => 'name',
				'label' => 'Name',
				'description' => 'Recipient display name.',
				'required' => true,
				'example' => 'Jane Doe'
			], [
				'name' => 'system_name',
				'label' => 'System name',
				'description' => 'Name of the current system.',
				'required' => true,
				'example' => 'BASE3'
			]
		];
	}

	public function getSchema(): array {
		return [
			'type' => 'object',
			'properties' => [
				'name' => ['type' => 'string'],
				'system_name' => ['type' => 'string']
			],
			'required' => [
				'name',
				'system_name'
			]
		];
	}
}
```

---

## 10. Events

MessagingFoundation defines messaging domain events.

Implementation plugins may fire these events through `IEventManager`.

### 10.1 `MessageQueuedEvent`

Class:

```php
MessagingFoundation\Event\MessageQueuedEvent
```

Purpose:

```text
A message was added to the queue.
```

Contains:

```php
getQueueId(): string
```

### 10.2 `MessageSentEvent`

Class:

```php
MessagingFoundation\Event\MessageSentEvent
```

Purpose:

```text
A message was successfully delivered.
```

Contains:

```php
getQueueId(): string
getDeliveryId(): string
```

### 10.3 `MessageFailedEvent`

Class:

```php
MessagingFoundation\Event\MessageFailedEvent
```

Purpose:

```text
A message delivery attempt failed.
```

Contains:

```php
getQueueId(): string
getDeliveryId(): string
getErrorMessage(): string
```

### 10.4 Event usage

Example:

```php
$eventManager->on(
	MessageSentEvent::class,
	function(MessageSentEvent $event): void {
		// React to a successful delivery.
	}
);
```

Events should be used for small, synchronous reactions. Slow follow-up work should be queued separately.

---

## 11. Exceptions

### 11.1 `MessageException`

Base exception:

```php
MessagingFoundation\Exception\MessageException
```

Use this for general messaging errors.

### 11.2 `MessageTransportNotFoundException`

Exception:

```php
MessagingFoundation\Exception\MessageTransportNotFoundException
```

Use this when a requested transport does not exist or cannot be resolved.

---

## 12. Typical consumer flow

A consumer plugin normally does the following:

```text
1. Provide an IMessageTypeProvider.
2. Let MessageHub synchronize the provider into templates and variants.
3. Render a message with IMessageRenderer.
4. Add recipients with Message::withRecipient().
5. Enqueue the message with IMessageService.
```

Example:

```php
<?php declare(strict_types=1);

namespace ExamplePlugin\Service;

use MessagingFoundation\Api\IMessageRenderer;
use MessagingFoundation\Api\IMessageService;
use MessagingFoundation\Dto\MessageAddress;

final class ExampleNotificationService {

	public function __construct(
		private readonly IMessageRenderer $messageRenderer,
		private readonly IMessageService $messageService
	) {}

	public function sendWelcomeMessage(string $email, string $name): string {
		$message = $this->messageRenderer
			->render(
				'examplewelcomemessage',
				'en',
				[
					'name' => $name,
					'system_name' => 'BASE3'
				]
			)
			->withRecipient(
				new MessageAddress('to', $email, $name)
			)
			->withMetadata(
				[
					'consumer_plugin' => 'ExamplePlugin'
				]
			);

		return $this->messageService->enqueue($message);
	}
}
```

The returned value is the queue ID.

---

## 13. Sending immediately

Consumer plugins can request immediate sending:

```php
$queueId = $messageService->sendNow($message);
```

This is useful for tests and selected synchronous flows.

For normal application behavior, prefer:

```php
$queueId = $messageService->enqueue($message);
```

Queue-based delivery is safer for user-facing runtime flows.

---

## 14. Choosing a transport

A consumer plugin can request a specific transport:

```php
$queueId = $messageService->enqueue(
	$message,
	'phpmailer'
);
```

If no transport is provided, the implementation plugin chooses the configured default transport.

MessagingFoundation does not define where the default transport is stored. In MessageHub, it is normally read from settings such as:

```text
messaging/default/default_transport
```

---

## 15. Metadata

`Message` supports arbitrary metadata:

```php
$message = $message->withMetadata(
	[
		'consumer_plugin' => 'ExamplePlugin',
		'object_id' => '123',
		'trigger' => 'course_registration'
	]
);
```

Metadata can be useful for:

* diagnostics
* delivery logs
* correlation IDs
* consumer plugin references
* audit traces

Do not store secrets in metadata.

---

## 16. Sender and reply-to

A sender can be set per message:

```php
$message = $message->withSender(
	'noreply@example.org',
	'Example System'
);
```

A reply-to address can be set separately:

```php
$message = $message->withReplyTo(
	'support@example.org',
	'Support'
);
```

Many installations prefer setting the sender in transport settings instead of per message.

---

## 17. Attachments

Attachments can be attached through immutable helpers:

```php
$message = $message->withAttachment(
	new MessageAttachment(
		'/path/to/file.pdf',
		'file.pdf',
		'application/pdf'
	)
);
```

Inline attachments:

```php
$message = $message->withAttachment(
	new MessageAttachment(
		'/path/to/logo.png',
		'logo.png',
		'image/png',
		true,
		'logo'
	)
);
```

A transport implementation decides how attachments are handled.

MessagingFoundation only defines the DTO.

---

## 18. Installation

Install MessagingFoundation as a BASE3 plugin.

Typical path:

```text
components/Base3/MessagingFoundation
```

Requirements:

```text
BASE3 Framework
PHP 8.2+
```

Then ensure the class map can discover:

```text
messagingfoundationplugin
```

MessagingFoundation alone does not send messages. Install a concrete implementation such as `MessageHub`.

---

## 19. Runtime requirements

MessagingFoundation itself has minimal runtime requirements.

Concrete functionality depends on implementation plugins.

For example:

| Feature             | Provided by                |
| ------------------- | -------------------------- |
| Template storage    | MessageHub                 |
| Variant storage     | MessageHub                 |
| Rendering           | MessageHub                 |
| Queue               | MessageHub                 |
| Delivery logs       | MessageHub                 |
| Worker jobs         | MessageHub                 |
| PHPMailer transport | Base3IliasLab              |
| Admin displays      | MessageHub / Base3IliasLab |

---

## 20. Compatibility and stability

MessagingFoundation is intended to be the stable boundary between consumer plugins and messaging implementations.

Changes should be conservative.

Safe changes:

* adding new DTO helper methods
* adding optional interfaces
* adding new events
* adding new exception subclasses

Risky changes:

* changing method signatures
* renaming interfaces
* changing DTO constructor order
* changing event constructor order
* changing technical names

Consumer plugins should depend on interfaces, not concrete MessageHub classes.

---

## 21. Design principles

### 21.1 Contract-only

MessagingFoundation should define what exists, not how it is implemented.

### 21.2 Protocol-neutral

The foundation must not assume email, SMTP, PHPMailer, ILIAS mail, webhooks, SMS or push notifications.

Email is only one possible transport.

### 21.3 Host-neutral

The foundation must not depend on ILIAS-specific APIs.

Host-specific adapters belong into host or project plugins.

### 21.4 Queue-compatible

The contracts support queue-based delivery, but the foundation does not implement a queue.

### 21.5 Immutable DTOs

Message DTOs should be easy to pass between services without unexpected side effects.

### 21.6 Discoverable providers

Message type providers and transports are designed to be discoverable through BASE3 class map mechanisms.

---

## 22. Development notes

### 22.1 Naming

Use stable lowercase technical names:

```php
public static function getName(): string {
	return 'examplewelcomemessage';
}
```

Avoid:

```text
ExampleWelcomeMessage
example-welcome-message
example_welcome_message
```

The exact naming convention can be project-specific, but names should stay stable once used in templates or settings.

### 22.2 Namespaces

Namespaces must match paths.

Example:

```text
src/Message/ExampleWelcomeMessageTypeProvider.php
```

```php
namespace ExamplePlugin\Message;
```

### 22.3 Constructor injection

Consumer services should request interfaces:

```php
public function __construct(
	private readonly IMessageRenderer $messageRenderer,
	private readonly IMessageService $messageService
) {}
```

Avoid service locator lookups inside domain logic.

### 22.4 No concrete implementation dependency

Consumer plugins should avoid depending on:

```php
MessageHub\Service\MessageService
MessageHub\Repository\DatabaseMessageQueueRepository
Base3IliasLab\Messaging\PhpMailerMessageTransport
```

Use:

```php
MessagingFoundation\Api\IMessageService
MessagingFoundation\Api\IMessageRenderer
MessagingFoundation\Api\IMessageTransport
```

---

## 23. Example: custom transport contract usage

A project plugin can provide a transport:

```php
<?php declare(strict_types=1);

namespace ProjectPlugin\Messaging;

use MessagingFoundation\Api\IMessageTransport;
use MessagingFoundation\Dto\Message;
use MessagingFoundation\Dto\MessageDeliveryResult;

final class WebhookMessageTransport implements IMessageTransport {

	public static function getName(): string {
		return 'webhook';
	}

	public function getLabel(): string {
		return 'Webhook';
	}

	public function supports(Message $message, array $settings = []): bool {
		return true;
	}

	public function send(Message $message, array $settings = []): MessageDeliveryResult {
		// Send payload to webhook endpoint.

		return new MessageDeliveryResult(
			true,
			'Webhook accepted message.',
			'',
			[
				'transport' => self::getName()
			]
		);
	}

	public function getSchema(): array {
		return [
			'type' => 'object',
			'properties' => [
				'endpoint' => ['type' => 'string'],
				'token' => ['type' => 'string']
			],
			'required' => [
				'endpoint'
			]
		];
	}
}
```

An implementation plugin can discover this class and offer it as an available transport.

---

## 24. Example: custom message type provider

```php
<?php declare(strict_types=1);

namespace CoursePlugin\Message;

use MessagingFoundation\Api\IMessageTypeProvider;

final class CourseRegistrationMessageTypeProvider implements IMessageTypeProvider {

	public static function getName(): string {
		return 'courseregistrationmessage';
	}

	public function getLabel(): string {
		return 'Course registration message';
	}

	public function getDescription(): string {
		return 'Sent after a user is registered for a course.';
	}

	public function getDefaultSubject(): string {
		return 'Registration confirmed: {{course_title}}';
	}

	public function getDefaultBodyText(): string {
		return "Hello {{user_name}},\n\n" .
			"your registration for {{course_title}} has been confirmed.";
	}

	public function getDefaultBodyHtml(): string {
		return '<p>Hello {{user_name}},</p>' .
			'<p>your registration for <strong>{{course_title}}</strong> has been confirmed.</p>';
	}

	public function getPlaceholders(): array {
		return [
			[
				'name' => 'user_name',
				'label' => 'User name',
				'description' => 'Display name of the registered user.',
				'required' => true,
				'example' => 'Jane Doe'
			], [
				'name' => 'course_title',
				'label' => 'Course title',
				'description' => 'Title of the course.',
				'required' => true,
				'example' => 'Introduction to BASE3'
			]
		];
	}

	public function getSchema(): array {
		return [
			'type' => 'object',
			'properties' => [
				'user_name' => ['type' => 'string'],
				'course_title' => ['type' => 'string']
			],
			'required' => [
				'user_name',
				'course_title'
			]
		];
	}
}
```

---

## 25. Testing

MessagingFoundation itself can be tested without a database or transport backend.

Useful tests:

* DTO construction
* DTO serialization
* immutable helper methods
* event getters
* exception hierarchy
* provider schema shape
* transport mock behavior

Example test idea:

```php
$message = new Message(
	'example',
	'Subject',
	'Text'
);

$changed = $message->withRecipient(
	new MessageAddress('to', 'jane@example.org', 'Jane Doe')
);

assert($message !== $changed);
assert(count($message->getRecipients()) === 0);
assert(count($changed->getRecipients()) === 1);
```

Implementation-level tests belong to plugins such as `MessageHub`.

---

## 26. Security notes

MessagingFoundation does not store or transmit data itself.

However, DTOs may carry sensitive data:

* recipient addresses
* names
* rendered message bodies
* metadata
* attachment paths
* reply-to addresses

Implementation plugins must decide:

* how long messages are stored
* whether message bodies are logged
* whether metadata is persisted
* who may inspect delivery logs
* how attachments are protected
* how secrets are resolved

Consumer plugins should avoid placing secrets in metadata or rendered message bodies unless strictly necessary.

---

## 27. Current limitations

MessagingFoundation intentionally does not define:

* a templating language
* placeholder validation rules
* HTML sanitization rules
* attachment storage abstraction
* rate limiting contracts
* bounce handling contracts
* unsubscribe handling
* transport priority rules
* multi-tenant routing rules
* queue locking strategy
* worker policy
* admin UI contracts

These belong either to implementation plugins or future optional contracts.

---

## 28. Versioning

MessagingFoundation should follow semantic versioning once published.

Recommended interpretation:

```text
PATCH
  Internal fixes, documentation, comments, non-breaking helper additions.

MINOR
  New interfaces, new optional methods via new interfaces, new DTO helpers, new events.

MAJOR
  Changed method signatures, renamed classes, removed methods, changed constructor contracts.
```

Because consumer plugins depend on this package, breaking changes should be avoided.

---

## 29. Summary

MessagingFoundation is the stable contract layer for BASE3 messaging.

It provides:

* neutral service interfaces
* repository contracts
* transport contracts
* message type provider contracts
* immutable DTOs
* events
* exceptions

It does not provide:

* queue implementation
* database schema
* rendering persistence
* worker jobs
* admin displays
* mail backend
* PHPMailer integration
* ILIAS integration

Use MessagingFoundation when building consumer plugins or implementation plugins that need a shared messaging vocabulary.

Use MessageHub when you need the concrete queue-first messaging implementation.
