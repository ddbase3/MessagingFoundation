<?php declare(strict_types=1);

namespace MessagingFoundation\Test;

use Base3\Api\IContainer;
use Base3\Api\IPlugin;
use MessagingFoundation\MessagingFoundationPlugin;
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
