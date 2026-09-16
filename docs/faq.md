# MessagingFoundation FAQ

## What is MessagingFoundation?

MessagingFoundation is the protocol-neutral contract layer for messaging in BASE3. It defines stable interfaces, DTOs, events, and exceptions that consumer plugins and concrete messaging implementations can share.

It does not provide a queue worker, database schema, template editor, delivery transport, SMTP client, webhook client, or administration interface.

## What is the main architectural purpose of the foundation?

The foundation separates messaging consumers from concrete delivery implementations. A consumer can depend on `IMessageService`, `IMessageRenderer`, `IMessageTransport`, and the messaging DTOs without knowing how messages are stored, queued, rendered, delivered, retried, or logged.

This allows the final runtime composition to choose an implementation without changing the consumer plugin.

## Which service contracts are included?

MessagingFoundation currently defines:

- `IMessageService`
- `IMessageQueueService`
- `IMessageRenderer`
- `IMessageTransport`
- `IMessageTransportRegistry`
- `IMessageTypeProvider`
- `IMessageTypeSynchronizationService`
- `IMessageIdGenerator`

It also defines repository contracts for templates, variants, queue entries, and delivery records.

## What does `IMessageService` provide?

`IMessageService` is the high-level sending contract. It supports two operations:

- `enqueue()` for queue-based delivery
- `sendNow()` for immediate processing by the active implementation

Both operations accept a `Message` DTO. The concrete implementation decides how queueing and immediate delivery are performed.

## What does `IMessageQueueService` provide?

`IMessageQueueService` defines the queue lifecycle:

- enqueue a message
- claim messages for processing
- mark a queue entry as sent
- mark a queue entry as failed
- cancel a queue entry

The foundation does not define a locking algorithm, retry strategy, database schema, or worker schedule.

## What is a `Message`?

`MessagingFoundation\Dto\Message` is the immutable runtime representation of a rendered message.

It can contain:

- technical message type name
- subject
- plain text body
- HTML body
- recipients
- attachments
- sender address and name
- reply-to address and name
- arbitrary metadata

The object can be converted to and restored from an array with `toArray()` and `fromArray()`.

## Are `Message` objects mutable?

No. The DTO is immutable. Methods such as `withRecipients()`, `withAttachments()`, `withSender()`, `withReplyTo()`, and `withMetadata()` return a new `Message` instance.

This makes it possible to render a base message and then add delivery-specific information without modifying the original object.

## How are recipients represented?

Recipients use `MessageAddress`.

Each address contains:

- a type such as `to`, `cc`, or `bcc`
- the address itself
- an optional display name

The foundation does not validate whether the address is an email address, phone number, chat ID, topic, or another transport-specific identifier.

## How are attachments represented?

Attachments use `MessageAttachment`.

The DTO stores:

- local path
- optional display filename
- optional MIME type
- inline flag
- optional content ID

The foundation does not read the file and does not copy attachment bytes into the DTO. A transport implementation decides whether and how the referenced file is processed.

## What is `MessageTemplate`?

`MessageTemplate` describes the stable configuration around a message type. It contains:

- template ID
- type name
- label
- description
- scope type
- scope ID
- default transport
- enabled state

Template storage and scope semantics are implementation responsibilities.

## What is `MessageVariant`?

`MessageVariant` contains the editable content for one template and language:

- variant ID
- template ID
- language
- subject
- plain text body
- HTML body
- enabled state
- fallback flag

The foundation does not define a templating language or placeholder replacement algorithm.

## What is a queued message?

`QueuedMessage` combines a `Message` with queue runtime information:

- queue ID
- transport name
- status
- attempts
- maximum attempts

The DTO does not persist itself and does not manage locking.

## What is `MessageDeliveryResult`?

`MessageDeliveryResult` is the neutral result returned by a transport.

It contains:

- success flag
- human-readable result or error message
- optional external provider ID
- arbitrary details array

A concrete implementation can persist or log these values, but the foundation itself does not.

## What is an `IMessageTransport`?

`IMessageTransport` is the common contract for one delivery mechanism.

A transport must provide:

- a stable technical name through `getName()`
- a human-readable label
- a settings summary
- a settings schema
- a `supports()` check
- a `send()` operation

The settings schema allows a host or administration UI to describe transport-specific settings without hard-coding every transport into the consumer.

## Does MessagingFoundation include any transports?

No. It only defines the `IMessageTransport` contract. Concrete transports belong to implementation plugins.

## What is `IMessageTransportRegistry`?

The registry contract provides access to discovered transports and their settings. It can return:

- all transports
- one transport by technical name
- the default transport name
- settings for a transport

How discovery and settings persistence work is left to the implementation.

## What is a message type provider?

A class implementing `IMessageTypeProvider` describes one discoverable message type.

It provides:

- stable technical name
- label
- description
- default subject
- default plain text body
- default HTML body
- placeholder descriptions
- input schema through `ISchemaProvider`

A provider defines defaults. It does not send messages by itself.

## What is message type synchronization?

`IMessageTypeSynchronizationService` defines a mechanism for synchronizing discoverable message type providers with the concrete template storage used by an implementation.

The interface supports:

- synchronizing all providers
- synchronizing one provider
- listing provider summaries

The exact create, update, overwrite, or skip behavior belongs to the implementation.

## Which repository contracts are provided?

The foundation defines repositories for:

- templates
- variants
- queue entries
- delivery records

The contracts include operations for persistence, paging, queue state changes, delivery detail, and delivery cleanup.

The foundation does not require a database. A different implementation can use another storage backend.

## Which events are defined?

MessagingFoundation defines:

- `MessageQueuedEvent`
- `MessageSentEvent`
- `MessageFailedEvent`

These events contain technical identifiers and, for failed delivery, the error message.

The foundation does not fire events by itself. A concrete implementation decides when they are emitted.

## Which exceptions are defined?

The package provides:

- `MessageException`
- `MessageTransportNotFoundException`

These provide stable messaging-specific failure categories for consumers and implementations.

## Does the foundation store messages?

No. MessagingFoundation contains no database schema, repository implementation, file storage, cache, state store, or queue backend.

Repository interfaces describe storage operations, but no storage is activated by installing the foundation alone.

## Does the foundation send data over the network?

No. It contains no HTTP client, SMTP client, mailer, webhook client, or other network transport.

Network communication starts only in a concrete transport implementation.

## Does the foundation log message content?

No. MessagingFoundation does not depend on the BASE3 logger and does not write logs.

A concrete messaging implementation can choose to log operational information.

## Does the foundation provide authentication or permissions?

No. It does not identify users, authorize message creation, or control access to queue and delivery records.

Authorization belongs to the application boundary that exposes a messaging operation.

## Does the foundation sanitize HTML?

No. `Message` and `MessageVariant` can carry HTML strings, but the foundation does not sanitize, validate, or render them.

Consumers should treat HTML content according to the requirements of the active transport and any UI that displays it.

## Does the foundation validate placeholders?

No. Message type providers can describe placeholders, but MessagingFoundation does not define placeholder syntax, replacement rules, escaping, or validation.

Those concerns belong to the renderer implementation.

## Can message metadata contain arbitrary values?

Yes. `Message` metadata is an `array<string,mixed>`.

Consumers should use it only for data that is necessary for messaging or downstream processing. Secrets should not be placed in metadata unless exposure to the concrete queue, delivery store, transport, and diagnostics is explicitly intended.

## Is MessagingFoundation specific to email?

No. Recipient addresses and message bodies are intentionally protocol-neutral. A concrete transport can interpret an address as an email address, phone number, chat ID, topic, webhook context, or another destination identifier.

## When should a plugin depend on MessagingFoundation?

A plugin should depend on MessagingFoundation when it needs to:

- define a reusable message type
- render or send messages through the shared messaging slot
- implement a new transport
- implement an alternative messaging backend
- listen for messaging events

Consumer plugins should normally depend on the foundation contracts rather than on a concrete messaging implementation.
