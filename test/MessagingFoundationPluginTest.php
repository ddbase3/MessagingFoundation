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

namespace Base3\MessagingFoundation\Test;

use Base3\CoreFoundation\Api\IContainer;
use Base3\CoreFoundation\Api\IPlugin;
use Base3\MessagingFoundation\MessagingFoundationPlugin;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class MessagingFoundationPluginTest extends TestCase {

	public function testPluginImplementsBase3PluginContractAndUsesStableName(): void {
		$container = $this->createStub(IContainer::class);
		$plugin = new MessagingFoundationPlugin($container);

		$this->assertInstanceOf(IPlugin::class, $plugin);
		$this->assertSame('messagingfoundationplugin', MessagingFoundationPlugin::getName());
	}

	public function testConstructionDoesNotRegisterServicesBeforeInit(): void {
		$container = $this->createMock(IContainer::class);
		$container->expects($this->never())->method('set');

		$plugin = new MessagingFoundationPlugin($container);

		$this->assertInstanceOf(MessagingFoundationPlugin::class, $plugin);
	}

	public function testInitRegistersOnlyPluginInstanceAsSharedService(): void {
		$container = $this->createMock(IContainer::class);
		$plugin = new MessagingFoundationPlugin($container);
		$container->expects($this->once())
			->method('set')
			->with('messagingfoundationplugin', $plugin, IContainer::SHARED)
			->willReturn($container);

		$this->assertNull($plugin->init());
	}

	public function testInitKeepsRegistrationKeyConsistentWithDiscoverableName(): void {
		$container = $this->createStub(IContainer::class);
		$registrations = [];
		$container->method('set')->willReturnCallback(
			static function(string $name, mixed $service, int $flags = 0) use ($container, &$registrations): IContainer {
				$registrations[] = [$name, $service, $flags];
				return $container;
			}
		);
		$plugin = new MessagingFoundationPlugin($container);

		$plugin->init();

		$this->assertSame([[MessagingFoundationPlugin::getName(), $plugin, IContainer::SHARED]], $registrations);
	}

	public function testRepeatedInitRegistersTheSamePluginInstance(): void {
		$container = $this->createStub(IContainer::class);
		$registrations = [];
		$container->method('set')->willReturnCallback(
			static function(string $name, mixed $service, int $flags = 0) use ($container, &$registrations): IContainer {
				$registrations[] = [$name, $service, $flags];
				return $container;
			}
		);
		$plugin = new MessagingFoundationPlugin($container);

		$plugin->init();
		$plugin->init();

		$this->assertSame([
			[MessagingFoundationPlugin::getName(), $plugin, IContainer::SHARED],
			[MessagingFoundationPlugin::getName(), $plugin, IContainer::SHARED]
		], $registrations);
	}

	public function testInitPropagatesContainerRegistrationFailures(): void {
		$container = $this->createStub(IContainer::class);
		$container->method('set')->willThrowException(new RuntimeException('Container unavailable'));
		$plugin = new MessagingFoundationPlugin($container);

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Container unavailable');
		$plugin->init();
	}
}
