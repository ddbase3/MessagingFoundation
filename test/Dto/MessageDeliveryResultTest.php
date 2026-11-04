<?php declare(strict_types=1);

namespace MessagingFoundation\Test\Dto;

use MessagingFoundation\Dto\MessageDeliveryResult;
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
