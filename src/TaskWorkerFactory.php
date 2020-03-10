<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Phly\Swoole\TaskWorker;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class TaskWorkerFactory
{
    public function __invoke(ContainerInterface $container) : TaskWorker
    {
        return new TaskWorker(
            $container,
            $container->get(LoggerInterface::class)
        );
    }
}
