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

use Base3\MessagingFoundation\Dto\MessageDeliveryResult;
use PHPUnit\Framework\TestCase;

final class MessageDeliveryResultTest extends TestCase {

	public function testDefaultSuccessfulDeliveryRetainsDefaultValues(): void {
		$result = new MessageDeliveryResult(true);

		$this->assertTrue($result->isSuccess());
		$this->assertSame('', $result->getMessage());
		$this->assertSame('', $result->getExternalId());
		$this->assertSame([], $result->getDetails());
	}

	public function testFailedDeliveryRetainsErrorAndTypedDetails(): void {
		$details = ['status' => 503, 'retryable' => true, 'response' => ['code' => 'unavailable']];
		$result = new MessageDeliveryResult(false, 'Transport unavailable', '', $details);

		$this->assertFalse($result->isSuccess());
		$this->assertSame('Transport unavailable', $result->getMessage());
		$this->assertSame('', $result->getExternalId());
		$this->assertSame($details, $result->getDetails());
	}

	public function testSuccessfulDeliveryRetainsExternalIdAndDetails(): void {
		$result = new MessageDeliveryResult(true, 'Accepted', 'smtp-001', ['attempts' => 1]);

		$this->assertTrue($result->isSuccess());
		$this->assertSame('Accepted', $result->getMessage());
		$this->assertSame('smtp-001', $result->getExternalId());
		$this->assertSame(['attempts' => 1], $result->getDetails());
	}

	public function testToArrayPreservesCanonicalKeyNamesAndValueTypes(): void {
		$result = new MessageDeliveryResult(false, 'failed', 'remote-2', ['elapsed' => 0.125, 'note' => null]);

		$this->assertSame([
			'success' => false,
			'message' => 'failed',
			'external_id' => 'remote-2',
			'details' => ['elapsed' => 0.125, 'note' => null]
		], $result->toArray());
	}

	public function testToArrayWithDefaultValuesStillIncludesAllFields(): void {
		$result = new MessageDeliveryResult(false);

		$this->assertSame([
			'success' => false, 'message' => '', 'external_id' => '', 'details' => []
		], $result->toArray());
	}
}
