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

use Base3\MessagingFoundation\Dto\MessageAddress;
use PHPUnit\Framework\TestCase;

final class MessageAddressTest extends TestCase {

	public function testConstructorPreservesExplicitRecipientTypeAddressAndName(): void {
		$address = new MessageAddress('bcc', 'alerts@example.test', 'Security');

		$this->assertSame('bcc', $address->getType());
		$this->assertSame('alerts@example.test', $address->getAddress());
		$this->assertSame('Security', $address->getName());
	}

	public function testConstructorDefaultsNameToEmpty(): void {
		$address = new MessageAddress('to', 'recipient@example.test');

		$this->assertSame('to', $address->getType());
		$this->assertSame('recipient@example.test', $address->getAddress());
		$this->assertSame('', $address->getName());
	}

	public function testSerializationRetainsExactlyThreeCanonicalFields(): void {
		$address = new MessageAddress('cc', 'info@example.test', 'Info');

		$this->assertSame([
			'type' => 'cc',
			'address' => 'info@example.test',
			'name' => 'Info'
		], $address->toArray());
	}

	public function testFromArrayUsesToAndEmptyStringDefaults(): void {
		$address = MessageAddress::fromArray([]);

		$this->assertSame('to', $address->getType());
		$this->assertSame('', $address->getAddress());
		$this->assertSame('', $address->getName());
		$this->assertSame(['type' => 'to', 'address' => '', 'name' => ''], $address->toArray());
	}

	public function testFromArrayCastsScalarFieldsRatherThanDroppingThem(): void {
		$address = MessageAddress::fromArray(['type' => 3, 'address' => 142, 'name' => true]);

		$this->assertSame('3', $address->getType());
		$this->assertSame('142', $address->getAddress());
		$this->assertSame('1', $address->getName());
	}

	public function testExplicitNullFieldsFallBackToDefaults(): void {
		$address = MessageAddress::fromArray(['type' => null, 'address' => null, 'name' => null]);

		$this->assertSame('to', $address->getType());
		$this->assertSame('', $address->getAddress());
		$this->assertSame('', $address->getName());
	}

	public function testRoundTripPreservesUnicodeAndSpecialCharacters(): void {
		$address = new MessageAddress('to', 'jane+tag@example.test', 'Jäne <External>');
		$restored = MessageAddress::fromArray($address->toArray());

		$this->assertNotSame($address, $restored);
		$this->assertSame($address->toArray(), $restored->toArray());
	}

	public function testUnknownAddressTypesArePreservedWithoutTransportValidation(): void {
		$address = MessageAddress::fromArray(['type' => 'reply-to', 'address' => 'reply@example.test']);

		$this->assertSame('reply-to', $address->getType());
		$this->assertSame('reply@example.test', $address->getAddress());
	}
}
