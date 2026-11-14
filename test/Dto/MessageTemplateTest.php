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

use Base3\MessagingFoundation\Dto\MessageTemplate;
use PHPUnit\Framework\TestCase;

final class MessageTemplateTest extends TestCase {

	public function testDefaultConstructorExposesEveryField(): void {
		$template = new MessageTemplate('template-1', 'notice', 'Notice');

		$this->assertSame('template-1', $template->getId());
		$this->assertSame('notice', $template->getTypeName());
		$this->assertSame('Notice', $template->getLabel());
		$this->assertSame('', $template->getDescription());
		$this->assertSame('global', $template->getScopeType());
		$this->assertSame('', $template->getScopeId());
		$this->assertSame('', $template->getDefaultTransport());
		$this->assertTrue($template->isEnabled());
	}

	public function testExplicitConstructorSupportsDisabledProjectScopedTemplates(): void {
		$template = new MessageTemplate(
			'template-2', 'invoice', 'Invoice', 'Invoice notification',
			'project', 'project-7', 'smtp', false
		);

		$this->assertSame('template-2', $template->getId());
		$this->assertSame('invoice', $template->getTypeName());
		$this->assertSame('Invoice', $template->getLabel());
		$this->assertSame('Invoice notification', $template->getDescription());
		$this->assertSame('project', $template->getScopeType());
		$this->assertSame('project-7', $template->getScopeId());
		$this->assertSame('smtp', $template->getDefaultTransport());
		$this->assertFalse($template->isEnabled());
	}

	public function testCustomScopeAndTransportStringsAreNotRewritten(): void {
		$template = new MessageTemplate('', '', '', '', 'tenant-custom', 'acme:de', 'webhook', true);

		$this->assertSame('tenant-custom', $template->getScopeType());
		$this->assertSame('acme:de', $template->getScopeId());
		$this->assertSame('webhook', $template->getDefaultTransport());
		$this->assertTrue($template->isEnabled());
	}

	public function testLabelsAndDescriptionsRetainUnicode(): void {
		$template = new MessageTemplate('id', 'type', 'Überprüfung', 'Änderung bestätigt');

		$this->assertSame('Überprüfung', $template->getLabel());
		$this->assertSame('Änderung bestätigt', $template->getDescription());
	}
}
