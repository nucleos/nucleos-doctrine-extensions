<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Tests\Bridge\Symfony\DependencyInjection;

use Core23\DoctrineExtensions\Bridge\Symfony\DependencyInjection\Core23DoctrineExtensionsExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class Core23DoctrineExtensionsExtensionTest extends AbstractExtensionTestCase
{
    public function testLoadDefault(): void
    {
        $this->load();

        $this->assertTrue(true);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new Core23DoctrineExtensionsExtension(),
        ];
    }
}
