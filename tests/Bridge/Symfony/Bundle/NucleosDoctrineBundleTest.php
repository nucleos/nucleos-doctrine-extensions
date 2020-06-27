<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\Bridge\Symfony\Bundle;

use Nucleos\Doctrine\Bridge\Symfony\Bundle\NucleosDoctrineBundle;
use Nucleos\Doctrine\Bridge\Symfony\DependencyInjection\NucleosDoctrineExtension;
use PHPUnit\Framework\TestCase;

final class NucleosDoctrineBundleTest extends TestCase
{
    public function testGetPath(): void
    {
        $bundle = new NucleosDoctrineBundle();

        static::assertStringEndsWith('Bridge/Symfony/Bundle', \dirname($bundle->getPath()));
    }

    public function testGetContainerExtension(): void
    {
        $bundle = new NucleosDoctrineBundle();

        static::assertInstanceOf(NucleosDoctrineExtension::class, $bundle->getContainerExtension());
    }
}
