<?php
/**
 * @see       https://github.com/phly/phly-swoole-taskworker for the canonical source repository
 * @copyright Copyright (c) 2019 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/phly/phly-swoole-taskworker/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phlytest\Swoole\TaskWorker;

use Phly\Swoole\TaskWorker\DeferredListener;
use Phly\Swoole\TaskWorker\DeferredListenerDelegator;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Swoole\Http\Server as HttpServer;
use stdClass;

class DeferredListenerDelegatorTest extends TestCase
{
    public function setUp()
    {
        $this->server = $this->createMock(HttpServer::class);
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    public function testReturnsOriginalServiceIfNotCallable()
    {
        $instance = new stdClass();
        $factory = function () use ($instance) {
            return $instance;
        };

        $delegator = new DeferredListenerDelegator();

        $this->assertSame($instance, $delegator(
            $this->container->reveal(),
            'listener',
            $factory
        ));
        $this->container->get(HttpServer::class)->shouldNotHaveBeenCalled();
    }

    public function testReturnsDecoratedListenerWhenOriginalServiceIsCallable()
    {
        $listener = function ($event) {
        };
        $factory = function () use ($listener) {
            return $listener;
        };
        $this->container->get(HttpServer::class)->willReturn($this->server);

        $delegator = new DeferredListenerDelegator();

        $instance = $delegator(
            $this->container->reveal(),
            'listener',
            $factory
        );

        $this->assertNotSame($listener, $instance);
        $this->container->get(HttpServer::class)->shouldHaveBeenCalled();
        $this->assertAttributeSame($this->server, 'server', $instance);
        $this->assertAttributeSame($listener, 'listener', $instance);
    }
}
