<?php declare(strict_types=1);

namespace MessagingFoundation\Test\Dto;

use MessagingFoundation\Dto\Message;
use MessagingFoundation\Dto\MessageAddress;
use MessagingFoundation\Dto\MessageAttachment;
use PHPUnit\Framework\TestCase;
use TypeError;

final class MessageTest extends TestCase {

	public function testDefaultConstructorAndEveryGetter(): void {
		$message = new Message('system.notice', 'Notice', 'Plain text');

		$this->assertSame('system.notice', $message->getTypeName());
		$this->assertSame('Notice', $message->getSubject());
		$this->assertSame('Plain text', $message->getBodyText());
		$this->assertSame('', $message->getBodyHtml());
		$this->assertSame([], $message->getRecipients());
		$this->assertSame([], $message->getAttachments());
		$this->assertSame('', $message->getFromAddress());
		$this->assertSame('', $message->getFromName());
		$this->assertSame('', $message->getReplyToAddress());
		$this->assertSame('', $message->getReplyToName());
		$this->assertSame([], $message->getMetadata());
	}

	public function testExplicitConstructorRetainsAllFieldsWithoutNormalization(): void {
		$message = $this->fullMessage();

		$this->assertSame('invoice.ready', $message->getTypeName());
		$this->assertSame('Invoice #12', $message->getSubject());
		$this->assertSame("Invoice\x00body", $message->getBodyText());
		$this->assertSame('<strong>Invoice</strong>', $message->getBodyHtml());
		$this->assertCount(2, $message->getRecipients());
		$this->assertCount(1, $message->getAttachments());
		$this->assertSame('billing@example.test', $message->getFromAddress());
		$this->assertSame('Billing', $message->getFromName());
		$this->assertSame('help@example.test', $message->getReplyToAddress());
		$this->assertSame('Helpdesk', $message->getReplyToName());
		$this->assertSame(['invoice' => 12, 'tags' => ['internal', 'pdf']], $message->getMetadata());
	}

	public function testToArraySerializesNestedRecipientsAttachmentsAndAllFields(): void {
		$message = $this->fullMessage();

		$this->assertSame([
			'type_name' => 'invoice.ready',
			'subject' => 'Invoice #12',
			'body_text' => "Invoice\x00body",
			'body_html' => '<strong>Invoice</strong>',
			'recipients' => [
				['type' => 'to', 'address' => 'ada@example.test', 'name' => 'Ada'],
				['type' => 'cc', 'address' => 'team@example.test', 'name' => 'Team']
			],
			'attachments' => [[
				'path' => '/private/invoice.pdf',
				'name' => 'invoice.pdf',
				'mime_type' => 'application/pdf',
				'inline' => false,
				'content_id' => ''
			]],
			'from_address' => 'billing@example.test',
			'from_name' => 'Billing',
			'reply_to_address' => 'help@example.test',
			'reply_to_name' => 'Helpdesk',
			'metadata' => ['invoice' => 12, 'tags' => ['internal', 'pdf']]
		], $message->toArray());
	}

	public function testToArrayIncludesExplicitDefaults(): void {
		$message = new Message('', '', '');

		$this->assertSame([
			'type_name' => '', 'subject' => '', 'body_text' => '', 'body_html' => '',
			'recipients' => [], 'attachments' => [],
			'from_address' => '', 'from_name' => '',
			'reply_to_address' => '', 'reply_to_name' => '',
			'metadata' => []
		], $message->toArray());
	}

	public function testWithRecipientsReplacesListAndPreservesOriginal(): void {
		$original = $this->fullMessage();
		$recipient = new MessageAddress('bcc', 'audit@example.test', 'Audit');
		$updated = $original->withRecipients([$recipient]);

		$this->assertNotSame($original, $updated);
		$this->assertSame([$recipient], $updated->getRecipients());
		$this->assertCount(2, $original->getRecipients());
		$this->assertSame($original->getAttachments(), $updated->getAttachments());
		$this->assertSame($original->getMetadata(), $updated->getMetadata());
		$this->assertSame($original->getFromAddress(), $updated->getFromAddress());
		$this->assertSame($original->getReplyToAddress(), $updated->getReplyToAddress());
		$this->assertSame($original->getBodyText(), $updated->getBodyText());
	}

	public function testWithRecipientsAcceptsAnEmptyReplacement(): void {
		$original = $this->fullMessage();
		$updated = $original->withRecipients([]);

		$this->assertSame([], $updated->getRecipients());
		$this->assertCount(2, $original->getRecipients());
		$this->assertSame($original->getAttachments(), $updated->getAttachments());
	}

	public function testWithAttachmentsReplacesListAndPreservesOriginal(): void {
		$original = $this->fullMessage();
		$attachment = new MessageAttachment('/private/logo.png', 'logo.png', 'image/png', true, 'logo');
		$updated = $original->withAttachments([$attachment]);

		$this->assertNotSame($original, $updated);
		$this->assertSame([$attachment], $updated->getAttachments());
		$this->assertCount(1, $original->getAttachments());
		$this->assertSame($original->getRecipients(), $updated->getRecipients());
		$this->assertSame($original->getMetadata(), $updated->getMetadata());
		$this->assertSame($original->getFromAddress(), $updated->getFromAddress());
		$this->assertSame($original->getReplyToAddress(), $updated->getReplyToAddress());
	}

	public function testWithAttachmentsAllowsClearingAttachments(): void {
		$original = $this->fullMessage();
		$updated = $original->withAttachments([]);

		$this->assertSame([], $updated->getAttachments());
		$this->assertCount(1, $original->getAttachments());
		$this->assertSame($original->getRecipients(), $updated->getRecipients());
	}

	public function testWithSenderReplacesBothSenderFields(): void {
		$original = $this->fullMessage();
		$updated = $original->withSender('alerts@example.test', 'Alerts');

		$this->assertNotSame($original, $updated);
		$this->assertSame('alerts@example.test', $updated->getFromAddress());
		$this->assertSame('Alerts', $updated->getFromName());
		$this->assertSame('billing@example.test', $original->getFromAddress());
		$this->assertSame('Billing', $original->getFromName());
		$this->assertSame($original->getReplyToAddress(), $updated->getReplyToAddress());
		$this->assertSame($original->getAttachments(), $updated->getAttachments());
	}

	public function testWithSenderDefaultsNameToEmptyInsteadOfKeepingPreviousName(): void {
		$updated = $this->fullMessage()->withSender('new@example.test');

		$this->assertSame('new@example.test', $updated->getFromAddress());
		$this->assertSame('', $updated->getFromName());
	}

	public function testWithReplyToReplacesBothReplyToFields(): void {
		$original = $this->fullMessage();
		$updated = $original->withReplyTo('reply@example.test', 'Replies');

		$this->assertNotSame($original, $updated);
		$this->assertSame('reply@example.test', $updated->getReplyToAddress());
		$this->assertSame('Replies', $updated->getReplyToName());
		$this->assertSame('help@example.test', $original->getReplyToAddress());
		$this->assertSame('Helpdesk', $original->getReplyToName());
		$this->assertSame($original->getFromAddress(), $updated->getFromAddress());
		$this->assertSame($original->getRecipients(), $updated->getRecipients());
	}

	public function testWithReplyToDefaultsNameToEmptyInsteadOfKeepingPreviousName(): void {
		$updated = $this->fullMessage()->withReplyTo('reply@example.test');

		$this->assertSame('reply@example.test', $updated->getReplyToAddress());
		$this->assertSame('', $updated->getReplyToName());
	}

	public function testWithMetadataMergesByDefaultAndOverwritesMatchingKeys(): void {
		$original = $this->fullMessage();
		$updated = $original->withMetadata(['invoice' => 13, 'delivered' => false]);

		$this->assertNotSame($original, $updated);
		$this->assertSame(['invoice' => 13, 'tags' => ['internal', 'pdf'], 'delivered' => false], $updated->getMetadata());
		$this->assertSame(['invoice' => 12, 'tags' => ['internal', 'pdf']], $original->getMetadata());
		$this->assertSame($original->getRecipients(), $updated->getRecipients());
		$this->assertSame($original->getAttachments(), $updated->getAttachments());
	}

	public function testWithMetadataCanReplaceAllExistingValues(): void {
		$original = $this->fullMessage();
		$updated = $original->withMetadata(['fresh' => true], false);

		$this->assertSame(['fresh' => true], $updated->getMetadata());
		$this->assertSame(['invoice' => 12, 'tags' => ['internal', 'pdf']], $original->getMetadata());
		$this->assertSame($original->getBodyHtml(), $updated->getBodyHtml());
	}

	public function testWithMetadataCanClearAllMetadata(): void {
		$original = $this->fullMessage();
		$updated = $original->withMetadata([], false);

		$this->assertSame([], $updated->getMetadata());
		$this->assertSame(['invoice' => 12, 'tags' => ['internal', 'pdf']], $original->getMetadata());
	}

	public function testWithMetadataEmptyMergeCreatesAnEqualButDistinctMessage(): void {
		$original = $this->fullMessage();
		$updated = $original->withMetadata([]);

		$this->assertNotSame($original, $updated);
		$this->assertSame($original->toArray(), $updated->toArray());
	}

	public function testWithMetadataUsesStandardArrayMergeIndexSemantics(): void {
		$original = new Message('test', '', '', metadata: [2 => 'before', 'name' => 'old']);
		$updated = $original->withMetadata([8 => 'after', 'name' => 'new']);

		$this->assertSame([0 => 'before', 'name' => 'new', 1 => 'after'], $updated->getMetadata());
		$this->assertSame([2 => 'before', 'name' => 'old'], $original->getMetadata());
	}

	public function testChainedWithMethodsRetainEachIndependentChange(): void {
		$original = $this->fullMessage();
		$recipient = new MessageAddress('to', 'new@example.test');
		$updated = $original
			->withRecipients([$recipient])
			->withAttachments([])
			->withSender('sender@example.test')
			->withReplyTo('reply@example.test', 'Support')
			->withMetadata(['updated' => true]);

		$this->assertSame([$recipient], $updated->getRecipients());
		$this->assertSame([], $updated->getAttachments());
		$this->assertSame('sender@example.test', $updated->getFromAddress());
		$this->assertSame('', $updated->getFromName());
		$this->assertSame('reply@example.test', $updated->getReplyToAddress());
		$this->assertSame('Support', $updated->getReplyToName());
		$this->assertSame(['invoice' => 12, 'tags' => ['internal', 'pdf'], 'updated' => true], $updated->getMetadata());
		$this->assertSame('billing@example.test', $original->getFromAddress());
		$this->assertCount(2, $original->getRecipients());
	}

	public function testFromArrayWithNoFieldsUsesEmptyDefaults(): void {
		$message = Message::fromArray([]);

		$this->assertSame((new Message('', '', ''))->toArray(), $message->toArray());
	}

	public function testFromArrayReconstructsAllFieldsAndNestedDtos(): void {
		$original = $this->fullMessage();
		$restored = Message::fromArray($original->toArray());

		$this->assertNotSame($original, $restored);
		$this->assertSame($original->toArray(), $restored->toArray());
		$this->assertInstanceOf(MessageAddress::class, $restored->getRecipients()[0]);
		$this->assertInstanceOf(MessageAttachment::class, $restored->getAttachments()[0]);
		$this->assertNotSame($original->getRecipients()[0], $restored->getRecipients()[0]);
		$this->assertNotSame($original->getAttachments()[0], $restored->getAttachments()[0]);
	}

	public function testFromArraySkipsInvalidRecipientAndAttachmentEntries(): void {
		$message = Message::fromArray([
			'recipients' => ['ignore', null, ['address' => 'a@example.test'], false, ['type' => 'bcc', 'address' => 'b@example.test']],
			'attachments' => [42, ['path' => '/one'], new MessageAttachment('/ignored'), ['path' => '/two', 'inline' => true]]
		]);

		$this->assertCount(2, $message->getRecipients());
		$this->assertCount(2, $message->getAttachments());
		$this->assertSame('a@example.test', $message->getRecipients()[0]->getAddress());
		$this->assertSame('bcc', $message->getRecipients()[1]->getType());
		$this->assertSame('/one', $message->getAttachments()[0]->getPath());
		$this->assertSame('/two', $message->getAttachments()[1]->getPath());
		$this->assertTrue($message->getAttachments()[1]->isInline());
	}

	public function testFromArrayCastsScalarFieldsToStrings(): void {
		$message = Message::fromArray([
			'type_name' => 99,
			'subject' => false,
			'body_text' => true,
			'body_html' => 2.5,
			'from_address' => 42,
			'from_name' => null,
			'reply_to_address' => 17,
			'reply_to_name' => true
		]);

		$this->assertSame('99', $message->getTypeName());
		$this->assertSame('', $message->getSubject());
		$this->assertSame('1', $message->getBodyText());
		$this->assertSame('2.5', $message->getBodyHtml());
		$this->assertSame('42', $message->getFromAddress());
		$this->assertSame('', $message->getFromName());
		$this->assertSame('17', $message->getReplyToAddress());
		$this->assertSame('1', $message->getReplyToName());
	}

	public function testFromArrayRejectsNonArrayMetadataByUsingEmptyArray(): void {
		foreach([null, 'json', false, 7] as $invalidMetadata) {
			$message = Message::fromArray(['metadata' => $invalidMetadata]);
			$this->assertSame([], $message->getMetadata());
		}
	}

	public function testFromArrayPreservesNestedMetadataTypes(): void {
		$metadata = ['flag' => false, 'count' => 0, 'empty' => null, 'nested' => ['answer' => 42]];
		$message = Message::fromArray(['metadata' => $metadata]);

		$this->assertSame($metadata, $message->getMetadata());
		$this->assertSame($metadata, $message->toArray()['metadata']);
	}

	public function testFromArrayAppliesNestedDtoDefaults(): void {
		$message = Message::fromArray(['recipients' => [[]], 'attachments' => [[]]]);

		$this->assertSame([['type' => 'to', 'address' => '', 'name' => '']], $message->toArray()['recipients']);
		$this->assertSame([[
			'path' => '', 'name' => '', 'mime_type' => '', 'inline' => false, 'content_id' => ''
		]], $message->toArray()['attachments']);
	}

	public function testSerializationRejectsNonMessageAddressRecipients(): void {
		$message = new Message('test', '', '', recipients: ['not-an-address']);

		$this->expectException(TypeError::class);
		$message->toArray();
	}

	public function testSerializationRejectsNonMessageAttachmentAttachments(): void {
		$message = new Message('test', '', '', attachments: [42]);

		$this->expectException(TypeError::class);
		$message->toArray();
	}

	private function fullMessage(): Message {
		return new Message(
			'invoice.ready',
			'Invoice #12',
			"Invoice\x00body",
			'<strong>Invoice</strong>',
			[
				new MessageAddress('to', 'ada@example.test', 'Ada'),
				new MessageAddress('cc', 'team@example.test', 'Team')
			],
			[new MessageAttachment('/private/invoice.pdf', 'invoice.pdf', 'application/pdf')],
			'billing@example.test',
			'Billing',
			'help@example.test',
			'Helpdesk',
			['invoice' => 12, 'tags' => ['internal', 'pdf']]
		);
	}
}
