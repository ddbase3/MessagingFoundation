<?php declare(strict_types=1);

namespace MessagingFoundation\Test\Dto;

use MessagingFoundation\Dto\MessageVariant;
use PHPUnit\Framework\TestCase;

final class MessageVariantTest extends TestCase {

	public function testDefaultConstructorExposesEveryField(): void {
		$variant = new MessageVariant('variant-1', 'template-1', 'en', 'Subject', 'Plain body');

		$this->assertSame('variant-1', $variant->getId());
		$this->assertSame('template-1', $variant->getTemplateId());
		$this->assertSame('en', $variant->getLanguage());
		$this->assertSame('Subject', $variant->getSubject());
		$this->assertSame('Plain body', $variant->getBodyText());
		$this->assertSame('', $variant->getBodyHtml());
		$this->assertTrue($variant->isEnabled());
		$this->assertFalse($variant->isFallback());
	}

	public function testExplicitConstructorPreservesFallbackAndDisabledFlags(): void {
		$variant = new MessageVariant(
			'variant-2', 'template-2', 'de', 'Betreff',
			'Text', '<p>HTML</p>', false, true
		);

		$this->assertSame('variant-2', $variant->getId());
		$this->assertSame('template-2', $variant->getTemplateId());
		$this->assertSame('de', $variant->getLanguage());
		$this->assertSame('Betreff', $variant->getSubject());
		$this->assertSame('Text', $variant->getBodyText());
		$this->assertSame('<p>HTML</p>', $variant->getBodyHtml());
		$this->assertFalse($variant->isEnabled());
		$this->assertTrue($variant->isFallback());
	}

	public function testEnabledFallbackIsAllowed(): void {
		$variant = new MessageVariant('id', 'template', 'en', '', '', '', true, true);

		$this->assertTrue($variant->isEnabled());
		$this->assertTrue($variant->isFallback());
	}

	public function testDisabledNonFallbackIsAllowed(): void {
		$variant = new MessageVariant('id', 'template', 'en', '', '', '', false, false);

		$this->assertFalse($variant->isEnabled());
		$this->assertFalse($variant->isFallback());
	}

	public function testBodyFieldsRetainLineBreaksAndTemplatePlaceholders(): void {
		$bodyText = "Hello {{name}}\nSecond line";
		$bodyHtml = '<p>{{name}} &amp; details</p>';
		$variant = new MessageVariant('id', 'template', 'en-US', '{{subject}}', $bodyText, $bodyHtml);

		$this->assertSame('en-US', $variant->getLanguage());
		$this->assertSame('{{subject}}', $variant->getSubject());
		$this->assertSame($bodyText, $variant->getBodyText());
		$this->assertSame($bodyHtml, $variant->getBodyHtml());
	}
}
