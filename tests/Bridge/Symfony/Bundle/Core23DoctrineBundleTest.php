<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Bridge\Symfony\Bundle;

use Core23\Doctrine\Bridge\Symfony\Bundle\Core23DoctrineBundle;
use Core23\Doctrine\Bridge\Symfony\DependencyInjection\Core23DoctrineExtension;
use PHPUnit\Framework\TestCase;

final class Core23DoctrineBundleTest extends TestCase
{
    public function testGetPath(): void
    {
        $bundle = new Core23DoctrineBundle();

        static::assertStringEndsWith('Bridge/Symfony/Bundle', \dirname($bundle->getPath()));
    }

    public function testGetContainerExtension(): void
    {
        $bundle = new Core23DoctrineBundle();

        static::assertInstanceOf(Core23DoctrineExtension::class, $bundle->getContainerExtension());
    }
}
