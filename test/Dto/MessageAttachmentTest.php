<?php declare(strict_types=1);

/***********************************************************************
 * This file is part of MessagingFoundation for BASE3 Framework.
 *
 * MessagingFoundation defines protocol-neutral messaging contracts.
 * It provides shared APIs, DTOs, events and exceptions for BASE3.
 * Storage, delivery and queues belong to implementation plugins.
 *
 * Developed by Daniel Dahme
 * Licensed under GPL-3.0
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * https://base3.de
 * https://github.com/ddbase3/MessagingFoundation
 **********************************************************************/

namespace Base3\MessagingFoundation\Test\Dto;

use Base3\MessagingFoundation\Dto\MessageAttachment;
use PHPUnit\Framework\TestCase;

final class MessageAttachmentTest extends TestCase {

	public function testConstructorDefaultsAllOptionalFields(): void {
		$attachment = new MessageAttachment('/tmp/statement.pdf');

		$this->assertSame('/tmp/statement.pdf', $attachment->getPath());
		$this->assertSame('', $attachment->getName());
		$this->assertSame('', $attachment->getMimeType());
		$this->assertFalse($attachment->isInline());
		$this->assertSame('', $attachment->getContentId());
	}

	public function testConstructorRetainsInlineDispositionAndContentId(): void {
		$attachment = new MessageAttachment('/private/logo.png', 'logo.png', 'image/png', true, 'logo-1');

		$this->assertSame('/private/logo.png', $attachment->getPath());
		$this->assertSame('logo.png', $attachment->getName());
		$this->assertSame('image/png', $attachment->getMimeType());
		$this->assertTrue($attachment->isInline());
		$this->assertSame('logo-1', $attachment->getContentId());
	}

	public function testToArrayUsesStableTransportNeutralFields(): void {
		$attachment = new MessageAttachment('/a', 'a.txt', 'text/plain', true, 'cid-a');

		$this->assertSame([
			'path' => '/a',
			'name' => 'a.txt',
			'mime_type' => 'text/plain',
			'inline' => true,
			'content_id' => 'cid-a'
		], $attachment->toArray());
	}

	public function testFromArrayAppliesAllDefaults(): void {
		$attachment = MessageAttachment::fromArray([]);

		$this->assertSame('', $attachment->getPath());
		$this->assertSame('', $attachment->getName());
		$this->assertSame('', $attachment->getMimeType());
		$this->assertFalse($attachment->isInline());
		$this->assertSame('', $attachment->getContentId());
	}

	public function testFromArrayCastsScalarsToDeclaredTypes(): void {
		$attachment = MessageAttachment::fromArray([
			'path' => 101,
			'name' => false,
			'mime_type' => true,
			'inline' => 1,
			'content_id' => 202
		]);

		$this->assertSame('101', $attachment->getPath());
		$this->assertSame('', $attachment->getName());
		$this->assertSame('1', $attachment->getMimeType());
		$this->assertTrue($attachment->isInline());
		$this->assertSame('202', $attachment->getContentId());
	}

	public function testExplicitNullValuesUseDefaults(): void {
		$attachment = MessageAttachment::fromArray([
			'path' => null, 'name' => null, 'mime_type' => null,
			'inline' => null, 'content_id' => null
		]);

		$this->assertSame([
			'path' => '', 'name' => '', 'mime_type' => '',
			'inline' => false, 'content_id' => ''
		], $attachment->toArray());
	}

	public function testFromArrayPreservesFalseInlineFlag(): void {
		$attachment = MessageAttachment::fromArray(['path' => '/file.txt', 'inline' => false]);

		$this->assertFalse($attachment->isInline());
		$this->assertSame('/file.txt', $attachment->getPath());
	}

	public function testFromArrayFollowsPhpBooleanCoercionForTruthyStrings(): void {
		$attachment = MessageAttachment::fromArray(['inline' => 'yes']);

		$this->assertTrue($attachment->isInline());
	}

	public function testRoundTripPreservesInlineAttachmentMetadata(): void {
		$original = new MessageAttachment('/img/pic.jpg', 'pic.jpg', 'image/jpeg', true, 'unique-content-id');
		$restored = MessageAttachment::fromArray($original->toArray());

		$this->assertNotSame($original, $restored);
		$this->assertSame($original->toArray(), $restored->toArray());
	}

	public function testPathIsNotSilentlyRewrittenOrValidated(): void {
		$path = "../attachments/\x00opaque";
		$attachment = new MessageAttachment($path);

		$this->assertSame($path, $attachment->getPath());
		$this->assertSame($path, $attachment->toArray()['path']);
	}
}
