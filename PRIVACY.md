# Privacy and Data Processing in MessagingFoundation

This document describes the privacy-relevant behavior of MessagingFoundation itself. MessagingFoundation is a contract and DTO layer. It defines the structures through which message data can move, but it does not provide message storage, queue execution, delivery transports, administration interfaces, logging, or network communication.

Concrete implementations and consumer plugins can introduce additional processing that must be documented separately.

## Component scope

MessagingFoundation currently provides:

- messaging service interfaces
- queue and delivery repository interfaces
- message renderer and transport contracts
- message type provider contracts
- message type synchronization contracts
- immutable message DTOs
- queue and delivery DTOs
- message lifecycle events
- messaging-specific exceptions
- a minimal BASE3 plugin registration class

The component itself does not create database tables, open files, send HTTP requests, connect to SMTP servers, write logs, or establish user sessions.

## Data that can pass through the contracts

The DTOs are capable of carrying personal, confidential, or otherwise sensitive data.

Examples include:

- recipient addresses
- recipient names
- message subjects
- plain text message bodies
- HTML message bodies
- sender addresses and names
- reply-to addresses and names
- arbitrary message metadata
- attachment paths
- attachment filenames
- attachment MIME types
- attachment content IDs
- template labels and descriptions
- template and variant content
- queue and delivery identifiers
- provider-specific external IDs
- delivery result details
- delivery error messages

Whether these values are personal data depends on the consumer and the message content.

## Message serialization

`Message::toArray()` exports the complete logical message structure, including recipients, attachment metadata, sender information, reply-to information, and arbitrary metadata.

`Message::fromArray()` reconstructs a message from that structure.

This makes the DTO suitable for queue or delivery persistence, but MessagingFoundation does not perform that persistence itself.

A concrete implementation that serializes a `Message` should treat the serialized representation as potentially containing the same sensitive data as the original message.

## Recipient addresses

`MessageAddress` intentionally does not prescribe an address format.

Depending on the transport, an address can be:

- an email address
- a telephone number
- a chat identifier
- a topic name
- another provider-specific destination identifier

The foundation does not validate, normalize, hash, or pseudonymize these values.

## Attachment references

`MessageAttachment` stores a local path and attachment metadata. It does not contain the attachment bytes themselves.

A file path can still reveal sensitive operational information, including:

- user or tenant identifiers
- project names
- local directory structure
- generated filenames
- document names

A concrete transport or delivery store must decide whether paths are persisted, logged, exposed to administrators, or transmitted.

## Message metadata

`Message` accepts an arbitrary metadata array.

Metadata can contain technical values, business identifiers, user identifiers, correlation IDs, or other structured context. MessagingFoundation does not inspect or restrict it.

Consumers should avoid placing passwords, API keys, access tokens, session identifiers, or unnecessary personal data into message metadata.

## Templates and variants

`MessageTemplate` and `MessageVariant` can carry reusable text that later becomes message content.

The foundation does not persist these objects and does not determine whether template text contains personal data. Implementations that store templates and variants should protect them according to their contents and administration model.

## Delivery results

`MessageDeliveryResult` can carry:

- a success or failure message
- an external provider ID
- arbitrary detail data

Provider responses can include identifiers, status information, recipient references, error details, or other information. A concrete implementation should review what it persists or logs from the result object.

## Message lifecycle events

The foundation defines queued, sent, and failed events.

The failure event includes the error message. Event listeners in a concrete runtime can therefore observe messaging identifiers and failure information.

MessagingFoundation itself does not dispatch or persist the events.

## No persistence by the foundation

MessagingFoundation contains no database implementation and no storage backend.

Installing the foundation alone does not persist:

- message bodies
- recipients
- metadata
- attachment paths
- queue entries
- delivery records
- templates
- variants
- delivery results

Persistence begins only when a concrete implementation uses the repository contracts or otherwise stores the data.

## No network communication by the foundation

MessagingFoundation does not contact mail servers, webhook endpoints, cloud messaging providers, SMS providers, chat services, or other remote systems.

The `IMessageTransport` interface is only a contract. Any external transfer belongs to the concrete transport implementation.

## No logging by the foundation

The foundation does not inject or call `ILogger`.

It does not automatically log:

- subjects
- bodies
- recipients
- errors
- provider IDs
- metadata
- attachment paths

Concrete implementations should document their own logging behavior because message subjects, addresses, and error messages can contain personal data.

## No authentication or authorization

MessagingFoundation does not identify users and does not enforce permissions.

It does not decide:

- who may send a message
- who may view templates
- who may inspect queue entries
- who may inspect delivery results
- who may configure transports

These decisions belong to the application and implementation layers that expose messaging functions.

## No HTML sanitization

The foundation stores HTML message content as a string. It does not sanitize scripts, links, tracking resources, or active content.

A transport or administration UI that renders HTML should apply the controls appropriate to its environment.

## No address validation

The foundation does not verify that a destination is deliverable or belongs to the intended person.

Address validation, normalization, consent handling, unsubscribe handling, and provider-specific requirements belong to higher layers.

## Retention and deletion

MessagingFoundation owns no persistent storage and therefore defines no retention period.

Concrete implementations should define retention for at least:

- queue records
- completed and failed delivery records
- recipients
- provider response details
- error information
- templates and variants
- attachment references
- logs
- backups and replicas

Deleting a DTO in process memory does not delete copies that another implementation has persisted or transmitted.

## Data minimization

Consumers should construct messages with only the information needed for the selected delivery purpose.

In particular:

- avoid secrets in message bodies and metadata
- avoid unnecessary recipient attributes
- avoid unnecessary attachment paths
- do not add internal identifiers unless they are required
- keep provider-specific result details as small as practical

## Responsibilities of implementations

Implementations of MessagingFoundation contracts should document, as applicable:

- storage backend and database schema
- queue retention
- retry behavior
- delivery log retention
- transport endpoints and providers
- credential storage and resolution
- TLS handling
- recipient validation
- attachment access
- template administration permissions
- delivery log permissions
- logging behavior
- event listeners
- backup and deletion behavior

MessagingFoundation provides the common messaging vocabulary. It does not replace those implementation-level privacy decisions.
