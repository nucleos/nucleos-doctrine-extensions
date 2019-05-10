<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Bridge\Symfony\DependencyInjection;

use Core23\Doctrine\Bridge\Symfony\DependencyInjection\Core23DoctrineExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

final class Core23DoctrineExtensionTest extends AbstractExtensionTestCase
{
    public function testLoadDefault(): void
    {
        $this->load();

        static::assertTrue(true);
    }

    protected function getContainerExtensions()
    {
        return [
            new Core23DoctrineExtension(),
        ];
    }
}
