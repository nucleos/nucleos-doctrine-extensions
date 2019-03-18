<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Bridge\Symfony\Bundle;

use Core23\Doctrine\Bridge\Symfony\Bundle\Core23DoctrineBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class Core23DoctrineBundleTest extends TestCase
{
    public function testItIsInstantiable(): void
    {
        $bundle = new Core23DoctrineBundle();

        $this->assertInstanceOf(BundleInterface::class, $bundle);
    }

    public function testGetPath(): void
    {
        $bundle = new Core23DoctrineBundle();

        $this->assertStringEndsWith('Bridge/Symfony/Bundle', \dirname($bundle->getPath()));
    }
}
